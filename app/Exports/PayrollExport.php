<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Payroll;

class PayrollExport implements FromCollection
{
    protected $employee, $request;

    public function __construct($employee, $request)
    {
        $this->employee = $employee;
        $this->request = $request;
    }

    public function collection()
    {
        return Payroll::where('employeeprofiles_id', $this->employee->employeeprofiles_id)
            ->when($this->request->month, fn($q) =>
                $q->whereMonth('created_at', $this->request->month)
            )
            ->when($this->request->period, fn($q) =>
                $q->where('pay_period', $this->request->period)
            )
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
