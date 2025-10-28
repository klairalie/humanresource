<?php

namespace App\Http\Controllers;
use App\Models\Archiveprofile;          
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employeeprofiles;
class ArchivedProfilesController extends Controller
{
  

    public function showArchivedProfiles()
    {
        $archives = DB::table('archiveprofiles')->orderBy('archived_at','desc')->get();
        return view('HR.archivedprofiles', compact('archives'));
    }

      public function reactivate($id)
    {
        // Find the archived record
        $archive = Archiveprofile::findOrFail($id);

        // Reinsert the employee data into employeeprofiles table
        $newEmployee = Employeeprofiles::create([
            'first_name'      => $archive->first_name,
            'middle_name'     => $archive->middle_name,
            'last_name'       => $archive->last_name,
            'gender'          => $archive->gender,
            'date_of_birth'      => $archive->date_of_birth,
            'address'         => $archive->address,
            'contact_number'  => $archive->contact_number,
            'email'           => $archive->email,
            'position'        => $archive->position,
            'hire_date'       => $archive->hire_date,
            'salary'          => $archive->salary,
            'status'          => 'reactivated', // mark as reactivated
        ]);

        // Optional: Update the archive record to show it's reactivated
        $archive->update([
            'status' => 'reactivated',
        ]);

        return redirect()->back()->with('success', 'Employee profile reactivated successfully!');
    }
}




