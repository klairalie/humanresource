<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use App\Models\PositionPermission;
use Illuminate\Contracts\Encryption\DecryptException;
use Exception;

class AuthTransferController extends Controller
{
    public function verify(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return response('Missing authentication token.', 400);
        }

        try {
            // 🔐 Decrypt and decode
            $decoded = json_decode(Crypt::decryptString(urldecode($token)), true);

            $email = trim($decoded['email'] ?? '');
            $position = trim(strtolower($decoded['position'] ?? ''));

            if (!$email || !$position) {
                throw new Exception('Invalid token payload.');
            }

            // 🧾 Store basic session data
            session([
                'user_email' => $email,
                'user_position' => ucfirst($position),
                'authenticated_at' => now(),
            ]);

            // ✅ Load permissions from the shared DB
            $allowedPermissions = PositionPermission::query()
                ->whereRaw('LOWER(position) = ?', [$position])
                ->where('is_allowed', true)
                ->with('permission')
                ->get()
                ->pluck('permission.permission_key')
                ->filter()
                ->values()
                ->toArray();

            Session::put('permissions', $allowedPermissions);
            Session::save();

            // ✅ Redirect to HR dashboard
            return redirect()->away('http://humanresource.test/HR');

        } catch (DecryptException | Exception $e) {
            return redirect()->away('http://login.test')->withErrors([
                'token' => 'Invalid or expired token.',
            ]);
        }
    }
}
