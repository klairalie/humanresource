<?php

namespace App\Http\Controllers;

use App\Models\Employeeprofiles;
use App\Models\Applicant;
use App\Models\SalaryRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $employee = Employeeprofiles::with('salary')
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
            'card_Idnumber'     => 'nullable|string|max:255|unique:employeeprofiles,card_Idnumber,' . $employee->employeeprofiles_id . ',employeeprofiles_id',
        ]);

        $employee->update($validated);

        return redirect()
            ->route('show.employeeprofiles')
            ->with('success', 'Employee profile updated successfully.');
    }

      // 🔹 Store New Employee Profile
    public function submitEmployeeprofiles(Request $request)
    {
        $validated = $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'address'           => 'required|string|max:255',
            'email'             => 'required|email|unique:employeeprofiles,email',
            'position'          => 'required|string|max:255',
            'date_of_birth'     => 'required|date',
            'contact_number'    => 'required|string|max:255',
            'hire_date'         => 'nullable|date',
            'status'            => 'required|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'card_Idnumber'     => 'nullable|string|max:255|unique:employeeprofiles,card_Idnumber',
        ]);

        Employeeprofiles::create([
            'first_name'        => $validated['first_name'],
            'last_name'         => $validated['last_name'],
            'address'           => $validated['address'],
            'email'             => $validated['email'],
            'position'          => $validated['position'],
            'date_of_birth'     => $validated['date_of_birth'],
            'contact_number'    => $validated['contact_number'],
            'hire_date'         => $validated['hire_date'] ?? null,
            'status'            => $validated['status'],
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'card_Idnumber'     => $validated['card_Idnumber'] ?? null,
        ]);

        return redirect()
            ->route('show.employeeprofiles')
            ->with('success', 'New employee profile added successfully.');
    }

    public function deactivate(Request $request, $employeeprofiles_id)
{
    $employee = Employeeprofiles::findOrFail($employeeprofiles_id);

    // Validate reason input
    $validated = $request->validate([
        'reason' => 'required|string|max:255',
    ]);

    $hrManager = Employeeprofiles::where('position', 'Human Resource Manager')->first();
    $archivedBy = $hrManager ? $hrManager->first_name . ' ' . $hrManager->last_name : 'System';

    DB::transaction(function () use ($employee, $validated, $archivedBy) {
        // Step 1️⃣: Archive employee data
        DB::table('archiveprofiles')->insert([
            'employeeprofiles_id' => $employee->employeeprofiles_id,
            'status'              => 'deactivated',
            'reason'              => $validated['reason'],
            'first_name'          => $employee->first_name,
            'last_name'           => $employee->last_name,
            'position'            => $employee->position,
            'contact_number'        => $employee->contact_number,
            'hire_date'           => $employee->hire_date,
            'archived_at'         => now(),
            'archived_by'         => $archivedBy,
            'emergency_contact'   => $employee->emergency_contact,
            'card_Idnumber'       => $employee->card_Idnumber,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // Step 2️⃣: Delete from main employees table
        $employee->delete();
    });

    return redirect()->back()->with('success', 'Employee has been deactivated, archived, and removed.');
}

}
