<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Employeeprofiles;
class ArchivedprofilesController extends Controller
{
    // login form
    public function loginForm()
    {
        return view('HR.archivedauthform');
    }

    // login action
    // public function login(Request $request)
    //{
        // $credentials = $request->validate([
        //     'email' => ['required','email'],
        //     'password' => ['required'],
        // ]);

        // if (Auth::attempt($credentials)) {
        //     $user = Auth::user();

        //     if ($user->position === 'Human Resource Manager' || $user->position === 'Supervisor') {
        //         return redirect()->route('HR.archivedprofiles');
        //     }

        //     Auth::logout();
        //     return back()->withErrors(['unauthorized' => 'Access denied.']);
        // }

        // return back()->withErrors(['email' => 'Invalid credentials.']);
    //}


    public function showArchivedProfiles()
    {
        $archives = DB::table('archiveprofiles')->orderBy('archived_at','desc')->get();
        return view('HR.archivedprofiles', compact('archives'));
    }

    public function logout(){
        return view('HR.viewemployees');
 
   }

   public function reactivate($archiveprofile_id)
{
    $archive = DB::table('archiveprofiles')->where('archiveprofile_id', $archiveprofile_id)->first();

    if (!$archive) {
        return redirect()->back()->with('error', 'Archived profile not found.');
    }

    $employee = Employeeprofiles::findOrFail($archive->employeeprofiles_id);

    // Find HR Manager
    $hrManager = Employeeprofiles::where('position', 'Human Resource Manager')->first();
    $reactivatedBy = $hrManager ? $hrManager->first_name . ' ' . $hrManager->last_name : 'System';

    // Update archive profile with reactivation info
    DB::table('archiveprofiles')
        ->where('archiveprofile_id', $archiveprofile_id)
        ->update([
            'status' => 'reactivated',
            'reactivated_at' => now(),
            'reactivated_by' => $reactivatedBy,
            'updated_at' => now(),
        ]);

    // Update employee status
    $employee->update(['status' => 'reactivated']);

    return redirect()->back()->with('success', 'Employee account has been reactivated.');
}

}
