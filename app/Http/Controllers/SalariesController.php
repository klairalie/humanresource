<?php

namespace App\Http\Controllers;
use App\Models\Salaries;
use Illuminate\Http\Request;

class SalariesController extends Controller
{
    public function showSalaries()
    {
        $salaries = Salaries::all();
        return view('HR.view_salaries', ['salaries' => $salaries]);
    }

    public function updateSalariesForm(Request $request)
    {
        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'position' => 'required|string|max:255',
        ]);
        $salary = Salaries::findOrFail($request->input('salaries_id'));
        $salary->update($validated);
        return redirect()->route('show.salaries')->with('success', 'Salary updated successfully!');
        
    }
}
