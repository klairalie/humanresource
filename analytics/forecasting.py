#!/usr/bin/env python3
import os
import sys
import json
import numpy as np
import pandas as pd
import mysql.connector
from sklearn.linear_model import LinearRegression
from datetime import datetime

# Optional CLI args: [months_ahead]
months_ahead = 3
if len(sys.argv) >= 2:
    try:
        months_ahead = int(sys.argv[1])
    except:
        months_ahead = 3

# --- 🔑 MySQL connection (update if needed, or use dotenv) ---
db = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="",
    database="sacstms"
)
cursor = db.cursor(dictionary=True)

# Only attendance & services here — metrics that return month + value
queries = {
    "attendance": """
        SELECT DATE_FORMAT(`date`, '%Y-%m') AS month,
               COUNT(*) AS value
        FROM attendances
        WHERE status = 'Present'
        GROUP BY month
        ORDER BY month ASC
    """,
    "services": """
        SELECT DATE_FORMAT(sr.created_at, '%Y-%m') AS month, 
               COUNT(sr.servicerequest_id) AS value
        FROM servicerequests sr
        GROUP BY month
        ORDER BY month ASC
    """
}

raw_data = {}
for metric, query in queries.items():
    cursor.execute(query)
    rows = cursor.fetchall()  # list of dicts with keys 'month' and 'value'
    raw_data[metric] = rows

cursor.close()
db.close()

def forecast_metric(rows, months_ahead=3):
    """
    rows: list of dicts with keys 'month' (YYYY-MM) and 'value'
    returns: {"months": [...], "values": [...]} with history + forecast
    """
    df = pd.DataFrame(rows)
    if df.empty:
        return {"months": [], "values": []}

    # ensure types and parse month
    df = df.copy()
    df["month"] = pd.to_datetime(df["month"].astype(str) + "-01")
    df = df.sort_values("month").reset_index(drop=True)

    # create full month index between min and max (fills missing months with 0)
    full_range = pd.date_range(start=df["month"].min(), end=df["month"].max(), freq='MS')
    df = df.set_index("month").reindex(full_range, fill_value=0).rename_axis("month").reset_index()
    df["value"] = df["value"].astype(int)

    y = df["value"].values
    X = np.arange(1, len(y) + 1).reshape(-1, 1)

    # If too few points for regression, produce zero forecasts or repeat last value
    if len(y) < 2 or (y == 0).all():
        # Use last known value repeated (or zeros)
        last_val = int(y[-1]) if len(y) >= 1 else 0
        future_preds = [last_val for _ in range(months_ahead)]
    else:
        model = LinearRegression().fit(X, y)
        future_X = np.arange(len(y) + 1, len(y) + months_ahead + 1).reshape(-1, 1)
        preds = model.predict(future_X)
        # Round and ensure no tiny negative values due to regression artefacts
        future_preds = [max(0, float(round(float(p), 2))) for p in preds]

    hist_months = df["month"].dt.strftime("%b %Y").tolist()
    hist_values = [int(v) for v in df["value"].tolist()]

    last_month = df["month"].iloc[-1]
    future_months = [
        (last_month + pd.DateOffset(months=i)).strftime("%b %Y")
        for i in range(1, months_ahead + 1)
    ]

    combined_months = hist_months + future_months
    combined_values = hist_values + future_preds

    # Convert all to native Python types (no numpy types)
    combined_values = [int(v) if float(v).is_integer() else float(v) for v in combined_values]

    return {"months": combined_months, "values": combined_values}

# Run forecasting for each metric
forecast_results = {}
for metric, rows in raw_data.items():
    forecast_results[metric] = forecast_metric(rows, months_ahead=months_ahead)

# Align months across attendance & services (use union and fill missing with 0)
def unify_months(data_dict):
    # gather all months
    all_months = sorted(set(
        data_dict.get("attendance", {}).get("months", []) + data_dict.get("services", {}).get("months", [])
    ), key=lambda x: datetime.strptime(x, "%b %Y") if x else datetime.min)

    def align(metric):
        metric_map = dict(zip(data_dict.get(metric, {}).get("months", []), data_dict.get(metric, {}).get("values", [])))
        return [metric_map.get(m, 0) for m in all_months]

    return {
        "attendance": {"months": all_months, "values": align("attendance")},
        "services": {"months": all_months, "values": align("services")}
    }

final_results = unify_months(forecast_results)

# Safe JSON output (convert numpy types)
def make_json_safe(obj):
    if isinstance(obj, dict):
        return {k: make_json_safe(v) for k, v in obj.items()}
    if isinstance(obj, list):
        return [make_json_safe(i) for i in obj]
    if isinstance(obj, (np.integer,)):
        return int(obj)
    if isinstance(obj, (np.floating,)):
        return float(obj)
    return obj

safe_results = make_json_safe(final_results)

# Export files to Laravel storage path if available, else current dir
storage_dir = os.path.join(os.getcwd(), "storage", "app")
try:
    os.makedirs(storage_dir, exist_ok=True)
except:
    storage_dir = os.getcwd()

csv_path = os.path.join(storage_dir, "forecast_output.csv")
xlsx_path = os.path.join(storage_dir, "forecast_output.xlsx")

# Write CSV
csv_rows = []
for metric, data in safe_results.items():
    for m, v in zip(data["months"], data["values"]):
        csv_rows.append({"Metric": metric, "Month": m, "Value": v})
pd.DataFrame(csv_rows).to_csv(csv_path, index=False)

# Write Excel
with pd.ExcelWriter(xlsx_path) as writer:
    for metric, data in safe_results.items():
        pd.DataFrame({"Month": data["months"], "Value": data["values"]}).to_excel(writer, sheet_name=metric.capitalize(), index=False)

# Add export paths to output
output = {
    "attendance": safe_results["attendance"],
    "services": safe_results["services"],
    "exports": {
        "csv": csv_path,
        "xlsx": xlsx_path
    }
}

print(json.dumps(output))
