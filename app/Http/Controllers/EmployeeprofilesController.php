<?php

namespace App\Http\Controllers;

use App\Models\Employeeprofiles;
use Illuminate\Http\Request;

class EmployeeprofilesController extends Controller
{
    public function showEmployeeprofiles()
    {
        $employee = Employeeprofiles::all();
        return view('HR.viewemployees', ["employee" => $employee]);
    }

    public function EmployeeprofilesForm()
    {
        return view('HR.employeeprofiles');
    }

    public function submitEmployeeprofiles(Request $request)
    {
        $validated = $request->validate([
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'address'            => 'required|string|max:255',
            'position'           => 'required|string|max:255',
            'contact_info'       => 'required|string|max:255',
            'hire_date'          => 'required|date',
            'status'             => 'required|string|max:255',
            'emergency_contact'  => 'required|string|max:255',
            'fingerprint'        => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $base64Fingerprint = null;
        if ($request->hasFile('fingerprint')) {
            $imageData = file_get_contents($request->file('fingerprint')->getRealPath());
            $base64Fingerprint = base64_encode($imageData);
        }

        $validated['fingerprint_data'] = $base64Fingerprint;

        Employeeprofiles::create($validated);

        return redirect()->route('show.employeeprofiles')->with('success', 'Employee profile submitted successfully!');
    }

public function edit($employeeprofile_id)
{
    $employee = Employeeprofiles::findOrFail($employeeprofile_id); 
    return view('HR.updateprofile', ["employee" => $employee]);
}

public function update(Request $request, $employeeprofile_id)
{
    $employee = Employeeprofiles::findOrFail($employeeprofile_id);
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'contact_info' => 'nullable|string|max:255',
        'hire_date' => 'nullable|date',
        'status' => 'nullable|string|max:255',
        'emergency_contact' => 'nullable|string|max:255',
    ]);

    
    $employee->update($validated);

    return redirect()->route('show.employeeprofiles', ['id' => $employeeprofile_id])->with('success', 'Book updated successfully!');
}
}
