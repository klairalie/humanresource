#!/usr/bin/env python3
import sys
import json
import numpy as np
import pandas as pd
import mysql.connector
from sklearn.linear_model import LinearRegression
from sklearn.metrics import r2_score
from datetime import datetime

def safe_json_output(data):
    try:
        print(json.dumps(data, ensure_ascii=False))
    except Exception as e:
        print(json.dumps({"error": f"JSON encoding failed: {str(e)}"}))

# ===== CONFIG =====
months_ahead = 3
if len(sys.argv) >= 2:
    try:
        months_ahead = int(sys.argv[1])
    except:
        months_ahead = 3

# ===== DATABASE CONNECTION =====
try:
    db = mysql.connector.connect(
        host="127.0.0.1",
        user="root",
        password="",
        database="sacstms"
    )
    cursor = db.cursor(dictionary=True)

    # Attendance data
    cursor.execute("""
        SELECT DATE_FORMAT(`date`, '%Y-%m') AS month,
               COUNT(*) AS value
        FROM attendances
        WHERE status = 'Present'
        GROUP BY month
        ORDER BY month ASC
    """)
    attendance_rows = cursor.fetchall()

    # Evaluation data
    cursor.execute("""
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month,
               AVG(total_score / 200 * 5) AS value
        FROM evaluation_summaries
        WHERE total_score IS NOT NULL
        GROUP BY month
        ORDER BY month ASC
    """)
    service_rows = cursor.fetchall()

    cursor.close()
    db.close()

except mysql.connector.Error as err:
    safe_json_output({"error": f"Database error: {err}"})
    sys.exit(0)
except Exception as e:
    safe_json_output({"error": f"General error: {e}"})
    sys.exit(0)

# ===== FORECASTING FUNCTION =====
def forecast_metric(rows, months_ahead=3):
    df = pd.DataFrame(rows)
    if df.empty:
        return {"months": [], "values": [], "trend": "none", "growth_rate": 0.0, "confidence": 0.0}

    df["month"] = pd.to_datetime(df["month"].astype(str) + "-01")
    df = df.sort_values("month").reset_index(drop=True)

    full_range = pd.date_range(start=df["month"].min(), end=df["month"].max(), freq='MS')
    df = df.set_index("month").reindex(full_range, fill_value=0).rename_axis("month").reset_index()

    df["value"] = df["value"].astype(float)
    y = df["value"].values
    X = np.arange(1, len(y) + 1).reshape(-1, 1)

    if len(y) >= 3:
        y_smooth = np.convolve(y, np.ones(3)/3, mode='same')
    else:
        y_smooth = y

    if len(y_smooth) < 2 or np.allclose(y_smooth, 0):
        last_val = float(y_smooth[-1]) if len(y_smooth) else 0.0
        preds = [last_val] * months_ahead
        confidence = 0.0
    else:
        model = LinearRegression().fit(X, y_smooth)
        future_X = np.arange(len(y_smooth) + 1, len(y_smooth) + months_ahead + 1).reshape(-1, 1)
        preds = model.predict(future_X)
        preds = [max(0, round(float(p), 2)) for p in preds]
        confidence = round(float(r2_score(y_smooth, model.predict(X))), 2) if len(y_smooth) > 2 else 0.0

    hist_avg = np.mean(y_smooth[-3:]) if len(y_smooth) >= 3 else np.mean(y_smooth)
    forecast_avg = np.mean(preds)
    growth_rate = 0.0 if hist_avg == 0 else (forecast_avg - hist_avg) / hist_avg
    trend = "rising" if growth_rate > 0.1 else "falling" if growth_rate < -0.1 else "stable"

    hist_months = df["month"].dt.strftime("%b %Y").tolist()
    hist_values = [round(float(v), 2) for v in df["value"].tolist()]
    last_month = df["month"].iloc[-1]
    future_months = [(last_month + pd.DateOffset(months=i)).strftime("%b %Y") for i in range(1, months_ahead + 1)]

    return {
        "months": hist_months + future_months,
        "values": hist_values + preds,
        "trend": trend,
        "growth_rate": round(growth_rate, 3),
        "confidence": confidence
    }

# ===== RUN FORECASTS =====
attendance = forecast_metric(attendance_rows, months_ahead)
services = forecast_metric(service_rows, months_ahead)

# ===== GENERATE INSIGHT =====
try:
    last_attendance = attendance["values"][-months_ahead - 1] if len(attendance["values"]) > months_ahead else 0
    last_service = services["values"][-months_ahead - 1] if len(services["values"]) > months_ahead else 0

    forecast_attendance = attendance["values"][-1] if attendance["values"] else 0
    forecast_service = services["values"][-1] if services["values"] else 0

    # Combine weighted performance (60% attendance, 40% service)
    last_performance = (last_attendance * 0.6) + (last_service * 0.4)
    forecast_performance = (forecast_attendance * 0.6) + (forecast_service * 0.4)

    diff = forecast_performance - last_performance
    trend_word = "improve" if diff > 0 else "decline" if diff < 0 else "remain steady"

    insight_text = (
    f"Employees recorded an average attendance rate of {last_attendance:.1f}% "
    f"and service evaluation rating of {last_service:.1f}%. "
    f"The forecast suggests performance will {trend_word} "
    f"to around {forecast_performance:.1f}% next month."
)


except Exception as e:
    insight_text = f"Unable to generate forecast insight: {str(e)}"

# ===== FINAL JSON OUTPUT =====
safe_json_output({
    "attendance": attendance,
    "services": services,
    "forecast_insight": insight_text
})
