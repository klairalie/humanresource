<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\Employeeprofiles;
use App\Models\Payroll;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    /**
     * Show create form
     */

    public function showDeductionForm()
    {
        return view('HR.deductionform');
    }

    public function createDeduction()
    {
        $employees = Employeeprofiles::all();
        $payroll   = Payroll::all(); 

       redirect()->route('show.deductionform', [
            'employees' => $employees,
            'payroll'   => $payroll,
        ]);
    }

    /**
     * Store a new deduction (cash advance or partial payment)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payroll_id'          => 'required|exists:payrolls,payroll_id',
            'employeeprofiles_id' => 'required|exists:employeeprofiles,employeeprofiles_id',
            'deduction_type'      => 'required|string',
            'amount'              => 'required|numeric|min:0',
            'partial_payment'     => 'nullable|numeric|min:0',
            'date'                => 'required|date',
        ]);

        // ✅ Save the deduction record
        $deduction = Deduction::create($validated);

        // ✅ Calculate remaining balance (total amount - sum of partials)
        $totalPartial = Deduction::where('employeeprofiles_id', $validated['employeeprofiles_id'])
            ->where('payroll_id', $validated['payroll_id'])
            ->sum('partial_payment');

        $remaining = $validated['amount'] - $totalPartial;

    
        $payroll = Payroll::find($validated['payroll_id']);
        if ($payroll) {
            $payroll->deductions = $totalPartial;
            $payroll->save();
        }

        return redirect()->route('HR.deductionform')
            ->with('success', "Deduction saved. Remaining Balance: ₱{$remaining}");
    }

    /**
     * List deductions
     */
    public function index()
    {
        $deductions = Deduction::with('employeeprofiles', 'payroll')
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('HR.deductions.index', compact('deductions'));
    }
}
