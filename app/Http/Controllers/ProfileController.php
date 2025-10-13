<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrativeaccount;
use App\Models\Employeeprofiles;
use App\Models\Login;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Show Edit Profile page
   public function showEditProfile()
{
    $userEmail = Session::get('user_email');

    if (!$userEmail) {
        abort(403, 'No logged-in user found in session.');
    }

    // Fetch employee by email
    $employee = Employeeprofiles::where('email', $userEmail)->firstOrFail();

    // Fetch admin account using employee ID
    $admin = Administrativeaccount::where('employeeprofiles_id', $employee->employeeprofiles_id)->firstOrFail();

    // Initialize
    $plainPassword = '';
    $passwordStatus = 'plain'; // default to plain

    try {
        // Try decrypting password if it was stored with Crypt
        $plainPassword = \Illuminate\Support\Facades\Crypt::decryptString($admin->password);
        $passwordStatus = 'decrypted';
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        // Not encrypted with Crypt. Determine if it's hashed
        if (\Illuminate\Support\Facades\Hash::needsRehash($admin->password) === false 
            && (str_starts_with($admin->password, '$2y$') || str_starts_with($admin->password, '$2b$'))) {
            $plainPassword = null;
            $passwordStatus = 'hashed';
        } else {
            // Plain stored
            $plainPassword = $admin->password;
            $passwordStatus = 'plain';
        }
    }

    // Pass both variables to the view
    return view('HR.editprofile', compact('employee', 'admin', 'plainPassword', 'passwordStatus'));
}


 
public function update(Request $request)
{
    $userEmail = Session::get('user_email');

    // Find employee & admin
    $employee = Employeeprofiles::where('email', $userEmail)->firstOrFail();
    $admin = Administrativeaccount::where('employeeprofiles_id', $employee->employeeprofiles_id)->firstOrFail();

    // Validate incoming data (all optional)
    $data = $request->validate([
        'email'    => 'nullable|email|max:255',
        'username' => 'nullable|string|max:255',
        'password' => 'nullable|string|max:255',
    ]);

    $updateLogin = false;

    // 🔹 Update Employee Email (if changed)
    if (!empty($data['email']) && $data['email'] !== $employee->email) {
        $oldEmail = $employee->email;
        $employee->email = $data['email'];
        $employee->save();

        // Update session to new email
        Session::put('user_email', $data['email']);

        // Update logins.email to match new one
        DB::table('logins')
            ->where('email', $oldEmail)
            ->update(['email' => $data['email']]);
    }

    // 🔹 Update Admin Username (if changed)
    if (!empty($data['username']) && $data['username'] !== $admin->username) {
        $admin->username = $data['username'];
    }

    // 🔹 Update Password (if changed)
    if (!empty($data['password'])) {
        // Hash new password before saving
        $hashedPassword = Hash::make($data['password']);
        $admin->password = $hashedPassword;
        $updateLogin = true;
    }

    $admin->save();

    // 🔹 Sync hashed password with logins table (using email as key)
    if ($updateLogin) {
        DB::table('logins')
            ->where('email', $employee->email)
            ->update(['password' => $hashedPassword]);
    }

    return redirect()->route('show.dashboard')->with('success', 'Profile updated successfully!');
}

}
