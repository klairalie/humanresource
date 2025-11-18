<!DOCTYPE html>
<html>
<head>
    <title>Payroll Print - {{ $employee->first_name }} {{ $employee->last_name }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            font-size: 12px;
        }
        th, td { 
            border: 1px solid #444; 
            padding: 8px; 
            text-align: center; 
        }
        thead { 
            background: #f0f0f0; 
        }
        h2 { 
            text-align: center; 
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .header-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="header-info">
        <h2>Payroll Records</h2>
        <h3>{{ $employee->first_name }} {{ $employee->last_name }}</h3>
        <p>Employee ID: {{ $employee->employeeprofiles_id }}</p>
        <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th>Total Days</th>
                <th>Basic Salary</th>
                <th>OT Pay</th>
                <th>Gross Pay</th>
                <th>Tax</th>
                <th>SSS</th>
                <th>PhilHealth</th>
                <th>Pag-IBIG</th>
                <th>Cash Advance</th>
                <th>Total Deductions</th>
                <th>Bonus</th>
                <th>Net Pay</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse($records as $record)
            <tr>
                <td>{{ $record->pay_period }}</td>
                <td>{{ $record->total_days_of_work }}</td>
                <td>₱{{ number_format($record->basic_salary, 2) }}</td>
                <td>₱{{ number_format($record->overtime_pay, 2) }}</td>
                <td>₱{{ number_format($record->gross_pay, 2) }}</td>
                <td>₱{{ number_format($record->tax_deduction, 2) }}</td>
                <td>₱{{ number_format($record->sss_contribution, 2) }}</td>
                <td>₱{{ number_format($record->philhealth_contribution, 2) }}</td>
                <td>₱{{ number_format($record->pagibig_contribution, 2) }}</td>
                <td>₱{{ number_format($record->cash_advance, 2) }}</td>
                <td>₱{{ number_format($record->deductions, 2) }}</td>
                <td>₱{{ number_format($record->bonus_amount, 2) }}</td>
                <td>₱{{ number_format($record->net_pay, 2) }}</td>
                <td>{{ $record->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="text-align: center; padding: 20px;">
                    No payroll records found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Print Document
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Close Window
        </button>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() { 
            setTimeout(() => {
                window.print();
            }, 500);
        }
        
        // Close window after print (optional)
        window.onafterprint = function() {
            // Uncomment the line below if you want to automatically close the window after printing
            // window.close();
        };
    </script>

</body>
</html>