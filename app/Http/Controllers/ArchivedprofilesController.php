<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Employeeprofiles;
use App\Models\Archiveprofile;
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

  public function reactivate($id, Request $request)
{
    $archive = Archiveprofile::findOrFail($id);
    $reactivatedBy = $request->input('reactivated_by'); // gikan pud sa form nimo

    $employee = Employeeprofiles::create([
        'first_name'        => $archive->first_name,
        'last_name'         => $archive->last_name,
        'address'           => $archive->address,
        'email'             => $archive->email,
        'date_of_birth'     => $archive->date_of_birth,
        'position'          => $archive->position,
        'contact_number'    => $archive->contact_number,
        'hire_date'         => $archive->hire_date,
        'status'            => 'active',
        'emergency_contact' => $archive->emergency_contact,
        'card_Idnumber'     => $archive->card_Idnumber,
    ]);

    $archive->update([
        'status'         => 'reactivated',
        'reactivated_at' => now(),
        'reactivated_by' => $reactivatedBy,  // ✅ mao pud ni imo gusto
    ]);

    return redirect()->back()->with('success', 'Employee reactivated successfully!');
}

}