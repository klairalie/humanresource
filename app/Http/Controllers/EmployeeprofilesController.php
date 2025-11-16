<?php

namespace App\Http\Controllers;

use App\Models\Employeeprofiles;
use App\Models\Applicant;
use App\Models\SalaryRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Archiveprofile;
use App\Models\Login;
use Illuminate\Support\Facades\Auth;

class EmployeeprofilesController extends Controller
{
    // Show all employee profiles
    public function showEmployeeprofiles()
    {
        // Auto-create employee profiles for hired applicants (same logic you had)
        $hiredApplicants = Applicant::where('applicant_status', 'Hired')->get();

        foreach ($hiredApplicants as $applicant) {
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
                    'hire_date'         => Carbon::now(),
                    'status'            => 'active',
                    'emergency_contact' => $applicant->emergency_contact,
                ]);
            }
        }

        // Fetch all active employees with their salary info
        $employee = Employeeprofiles::with('salary_rates')
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

    // 🔹 Add Employee Form
    public function EmployeeprofilesForm()
    {
        $salaries = SalaryRate::where('status', 'active')
            ->select('position', 'salary_rate')
            ->orderBy('position')
            ->get();

        return view('HR.employeeprofiles', ['salaries' => $salaries]);
    }

    // 🔹 Edit Employee Form
    public function edit($employeeprofiles_id)
    {
        $employee = Employeeprofiles::findOrFail($employeeprofiles_id);

        $salaries = SalaryRate::where('status', 'active')
            ->select('position', 'salary_rate')
            ->orderBy('position')
            ->get();

        return view('HR.updateprofile', compact('employee', 'salaries'));
    }

    // 🔹 Update Employee Profile
 // ======== UPDATE FUNCTION ========
public function update(Request $request, $employeeprofiles_id)
{
    $employee = Employeeprofiles::findOrFail($employeeprofiles_id);

    $validated = $request->validate([
        'first_name'        => 'required|string|max:255',
        'last_name'         => 'required|string|max:255',
        'address'           => 'nullable|string|max:255',
        'position'          => 'nullable|string|max:255',
        'contact_number'    => 'nullable|string|max:255',
        'hire_date'         => 'nullable|date',
        'status'            => 'nullable|string|max:255',
        'emergency_contact' => 'nullable|string|max:255',
        'face_descriptor'   => 'nullable|string',
    ]);

    // ======= UNIQUE FACE CHECK =======
    if (!empty($validated['face_descriptor'])) {
        $incoming = json_decode($validated['face_descriptor'], true);
        $THRESHOLD = 0.45;

        $employees = Employeeprofiles::whereNotNull('face_descriptor')
            ->where('employeeprofiles_id', '!=', $employeeprofiles_id)
            ->get();

        $euclidean = fn($a, $b) => sqrt(array_sum(array_map(fn($x, $y) => ($x-$y)**2, $a, $b)));

        foreach ($employees as $emp) {
            $existing = json_decode($emp->face_descriptor, true);
            if (!is_array($existing)) continue;

            // compare distances
            if ($euclidean($incoming, $existing) < $THRESHOLD) {
                return back()->withErrors([
                    'face_descriptor' => "This face is already registered under: {$emp->first_name} {$emp->last_name}."
                ])->withInput();
            }
        }
    }

    $employee->update($validated);

    return redirect()->route('show.employeeprofiles')
                     ->with('success', 'Employee profile updated successfully.');
}


public function checkFaceDuplicate(Request $request)
{
    $descriptor = $request->input('face_descriptor');
    $currentId = $request->input('employeeprofiles_id', null);

    if (!$descriptor) {
        return response()->json(['status' => 'error', 'message' => 'No descriptor provided'], 422);
    }

    $descriptorArray = json_decode($descriptor, true);
    if (!is_array($descriptorArray)) {
        return response()->json(['status' => 'error', 'message' => 'Invalid descriptor format'], 422);
    }

    $THRESHOLD = 0.45;

    $employees = Employeeprofiles::whereNotNull('face_descriptor')
                                ->when($currentId, fn($q) => $q->where('employeeprofiles_id', '!=', $currentId))
                                ->get();

    $euclidean = fn($a, $b) => sqrt(array_sum(array_map(fn($x,$y)=>($x-$y)**2,$a,$b)));

    foreach ($employees as $emp) {
        $existing = json_decode($emp->face_descriptor, true);
        if (!is_array($existing)) continue;

        if ($euclidean($descriptorArray, $existing) < $THRESHOLD) {
            return response()->json([
                'status' => 'duplicate',
                'matched_employee' => "{$emp->first_name} {$emp->last_name}"
            ]);
        }
    }

    return response()->json(['status' => 'unique']);
}

public function deactivate(Request $request, $employeeprofiles_id)
{
    $request->validate([
        'reason' => 'required|string|max:1000',
    ]);

    $archived_by = session('user_email');
    $employee = Employeeprofiles::findOrFail($employeeprofiles_id);
    // dd($employee->first_name);


    // // Store employee info into archiveprofiles
    Archiveprofile::create([
        'original_employee_id' => $employee->employeeprofiles_id,
        'first_name' => $employee->first_name,
        'last_name' => $employee->last_name,
        'address' => $employee->address,
        'email' => $employee->email,
        'position' => $employee->position,
        'date_of_birth' => $employee->date_of_birth,
        'contact_number' => $employee->contact_number,
        'hire_date' => $employee->hire_date,
        'status' => 'deactivated', // ✅ Always set to "Deactivated"
        'emergency_contact' => $employee->emergency_contact,
        'card_Idnumber' => $employee->card_Idnumber,
        'reason' => $request->reason,
        'archived_by' => $archived_by,
        'archived_at' => Carbon::now(),
        
    ]);

    // Delete from active employeeprofiles
    $employee->delete();

    return redirect()->route('archived.profiles')->with('success', 'Employee successfully deactivated and moved to archives.');
}

}
