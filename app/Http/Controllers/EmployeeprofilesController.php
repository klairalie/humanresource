<?php

namespace App\Http\Controllers;
use App\Models\Salaries;
use App\Models\Employeeprofiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Applicant;
use Carbon\Carbon;

class EmployeeprofilesController extends Controller
{

public function showEmployeeprofiles()
{
    // 🔹 Fetch all applicants with "Hired" status
    $hiredApplicants = Applicant::where('applicant_status', 'Hired')->get();

    foreach ($hiredApplicants as $applicant) {
        // Check if applicant already exists in employeeprofiles
        $exists = Employeeprofiles::where('email', $applicant->email)->first();

        if (!$exists) {
            Employeeprofiles::create([
                'first_name'        => $applicant->first_name,
                'last_name'         => $applicant->last_name,
                'address'           => $applicant->address,
                'email'             => $applicant->email,
                'position'          => $applicant->position,
                'date_of_birth'     => $applicant->date_of_birth,
                'contact_number'    => $applicant->contact_number,
                'hire_date'         => Carbon::now(), // set hire_date to now
                'status'            => 'active', // default status for hired employees
                'emergency_contact' => $applicant->emergency_contact,
            ]);
        }
    }

    // 🔹 Now show employees
    $employee = Employeeprofiles::query()
        ->whereIn('status', ['active', 'reactivated'])
        ->when(request('search'), function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        })
        ->when(request('position'), function ($query, $position) {
            $query->where('position', $position);
        })
        ->paginate(10);

    return view('HR.viewemployees', ["employee" => $employee]);
}



    public function EmployeeprofilesForm()
    {
           $salaries = Salaries::select('position', 'basic_salary')
            ->orderBy('position')
            ->get();

        return view('HR.employeeprofiles', ['salaries' => $salaries]);
    }

    public function submitEmployeeprofiles(Request $request)
    {
        $validated = $request->validate([
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'address'            => 'required|string|max:255',
            'position'           => 'required|string|max:255',
            'basic_salary'     => 'required|numeric',
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
    $salaries = DB::table('salaries')->get(); // Fetch all positions and basic salaries

    return view('HR.updateprofile', [
        'employee' => $employee,
        'salaries' => $salaries
    ]);
}


public function update(Request $request, $employeeprofiles_id)
{
    $employee = Employeeprofiles::findOrFail($employeeprofiles_id);

    $validated = $request->validate([
        'first_name'        => 'required|string|max:255',
        'last_name'         => 'required|string|max:255',
        'address'           => 'nullable|string|max:255',
        'position'          => 'nullable|string|max:255',
        'contact_info'      => 'nullable|string|max:255',
        'hire_date'         => 'nullable|date',
        'status'            => 'nullable|string|max:255',
        'emergency_contact' => 'nullable|string|max:255',
        'card_Idnumber'     => 'nullable|string|max:255|unique:employeeprofiles,card_Idnumber,' . $employee->employeeprofiles_id . ',employeeprofiles_id',
    ]);

    $employee->update($validated);

    return redirect()
        ->route('show.employeeprofiles', ['id' => $employeeprofiles_id])
        ->with('success', 'Profile updated successfully!');
}

public function delete($employeeprofile_id)
{
    $employee = Employeeprofiles::findOrFail($employeeprofile_id);
    $employee->delete();
    return redirect()->route('show.employeeprofiles')->with('success', 'Employee profile deleted successfully!');

}


public function deactivate(Request $request, $employeeprofiles_id)
{
    $employee = Employeeprofiles::findOrFail($employeeprofiles_id);

    // validate reason input
    $validated = $request->validate([
        'reason' => 'required|string|max:255',
    ]);

    $hrManager = Employeeprofiles::where('position', 'Human Resource Manager')->first();
    $archivedBy = $hrManager ? $hrManager->first_name . ' ' . $hrManager->last_name : 'System';

    DB::transaction(function () use ($employee, $validated, $archivedBy) {
        // Step 1: archive employee
        DB::table('archiveprofiles')->insert([
            'employeeprofiles_id' => $employee->employeeprofiles_id,
            'status' => 'deactivated',
            'reason' => $validated['reason'],
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'position' => $employee->position,
            'contact_info' => $employee->contact_number,
            'hire_date' => $employee->hire_date,
            'archived_at' => now(),
            'archived_by' => $archivedBy,
            'emergency_contact' => $employee->emergency_contact,
            'fingerprint_data' => $employee->fingerprint_data,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Step 2: update employee status (not delete)
        $employee->update(['status' => 'deactivated']);
    });

    return redirect()->back()->with('success', 'Employee has been deactivated and archived.');
}

// public function empCount(){

//     $count = Employeeprofiles::whereIn('status', ['active', 'reactivated'])->count();
//     return $count;
// }



}