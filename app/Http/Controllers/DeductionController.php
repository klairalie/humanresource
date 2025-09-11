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
        $employees = Employeeprofiles::all();
        return view('HR.deductionform', compact('employees'));
    }

    /**
     * Fetch payroll by employee (AJAX)
     */

    /**
     * Store a new deduction
     */
    public function storeDeduction(Request $request)
    {
        $validated = $request->validate([
            'employeeprofiles_id' => 'required|exists:employeeprofiles,employeeprofiles_id',
            'deduction_type'      => 'required|string',
            'amount'              => 'required|numeric|min:0',
            'deduction_date'      => 'required|date',
        ]);

        Deduction::create($validated);

       
        $totalDeductions = Deduction::with('payroll')->where('employeeprofiles_id', $validated['employeeprofiles_id'])->sum('amount');

        return redirect()->route('show.deductionform')
            ->with('success', "Deduction saved successfully. Total deductions for this payroll: ₱{$totalDeductions}");
    }

    public function viewDeductionRecords()
    {
        $deductions = Deduction::with('employeeprofiles')->paginate(10);
        return view('HR.deductionrecords', ["deductions" => $deductions]);
    }

    public function editDeduction($deduction_id)
    {
        $deduction = Deduction::findOrFail($deduction_id);
        return view('HR.editdeduction', ["deduction" => $deduction]);
    }

   public function updateDeduction(Request $request, $deduction_id)
{
    $deduction = Deduction::findOrFail($deduction_id);

    $validated = $request->validate([
        'partial_payment' => 'nullable|numeric|min:0',
    ]);

    // ✅ Create a new record instead of updating the old one
    if (!is_null($validated['partial_payment'])) {
        Deduction::create([
            'employeeprofiles_id' => $deduction->employeeprofiles_id,   // keep same employee
            'deduction_type'      => $deduction->deduction_type,       // carry forward type
            'partial_payment'     => $validated['partial_payment'],    // new payment
            'amount'              => $deduction->amount,               // keep original amount
            'deduction_date'      => $deduction->deduction_date,       // keep original deduction date
            'partialpay_date'     => now()->toDateString(),            // new date
        ]);
    }

    return redirect()->route('view.deductionrecords')
        ->with('success', "Deduction recorded successfully. Old record kept, new partial payment saved.");
}


    public function deleteDeduction($deduction_id)
    {
        $deduction = Deduction::findOrFail($deduction_id);
        $deduction->delete();

        return redirect()->route('view.deductionrecords', ['id' => $deduction_id])
            ->with('success', "Deduction deleted successfully.");
    }
}
