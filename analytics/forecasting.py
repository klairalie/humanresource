#!/usr/bin/env python3
import os
import sys
import json
import numpy as np
import pandas as pd
import mysql.connector
from sklearn.linear_model import LinearRegression
from datetime import datetime

# how many months ahead to forecast
months_ahead = 3
if len(sys.argv) >= 2:
    try:
        months_ahead = int(sys.argv[1])
    except:
        months_ahead = 3

# --- connect to database ---
db = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="",
    database="sacstms"
)
cursor = db.cursor(dictionary=True)

# --- query attendance (unchanged) ---
cursor.execute("""
    SELECT DATE_FORMAT(`date`, '%Y-%m') AS month,
           COUNT(*) AS value
    FROM attendances
    WHERE status = 'Present'
    GROUP BY month
    ORDER BY month ASC
""")
attendance_rows = cursor.fetchall()

# --- query evaluation summaries (use total_score) ---
# --- query evaluation summaries (use total_score) ---
cursor.execute("""
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month,
           AVG(total_score / 200 * 5) AS value  -- normalize to 1–5 scale
    FROM evaluation_summaries
    WHERE total_score IS NOT NULL
    GROUP BY month
    ORDER BY month ASC
""")

service_rows = cursor.fetchall()
# print("Loaded service_rows:", service_rows)  # <-- optional debug


cursor.close()
db.close()

# --- forecasting function ---
def forecast_metric(rows, months_ahead=3):
    df = pd.DataFrame(rows)
    if df.empty:
        return {"months": [], "values": []}

    df["month"] = pd.to_datetime(df["month"].astype(str) + "-01")
    df = df.sort_values("month").reset_index(drop=True)

    full_range = pd.date_range(start=df["month"].min(), end=df["month"].max(), freq='MS')
    df = df.set_index("month").reindex(full_range, fill_value=0).rename_axis("month").reset_index()
    df["value"] = df["value"].astype(float)

    y = df["value"].values
    X = np.arange(1, len(y) + 1).reshape(-1, 1)

    if len(y) < 2 or np.allclose(y, 0):
        last_val = float(y[-1]) if len(y) else 0.0
        preds = [last_val] * months_ahead
    else:
        model = LinearRegression().fit(X, y)
        future_X = np.arange(len(y) + 1, len(y) + months_ahead + 1).reshape(-1, 1)
        preds = model.predict(future_X)
        preds = [max(0, round(float(p), 2)) for p in preds]

    hist_months = df["month"].dt.strftime("%b %Y").tolist()
    hist_values = [round(float(v), 2) for v in df["value"].tolist()]
    last_month = df["month"].iloc[-1]
    future_months = [
        (last_month + pd.DateOffset(months=i)).strftime("%b %Y")
        for i in range(1, months_ahead + 1)
    ]

    return {
        "months": hist_months + future_months,
        "values": hist_values + preds
    }

# --- build results ---
attendance = forecast_metric(attendance_rows, months_ahead)
services = forecast_metric(service_rows, months_ahead)

# --- align months ---
all_months = sorted(set(attendance["months"] + services["months"]),
                    key=lambda x: datetime.strptime(x, "%b %Y"))

def align(data):
    mapping = dict(zip(data["months"], data["values"]))
    return [mapping.get(m, 0) for m in all_months]

attendance["months"] = all_months
attendance["values"] = align(attendance)
services["months"] = all_months
services["values"] = align(services)

# --- output json ---
output = {
    "attendance": attendance,
    "services": services
}

print(json.dumps(output))
