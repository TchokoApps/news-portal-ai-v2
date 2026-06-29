<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminResetPasswordRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function create($token): View
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => request()->query('email'),
        ]);
    }

    public function store(AdminResetPasswordRequest $request)
    {
        $admin = Admin::where('email', $request->email)
            ->where('remember_token', $request->token)
            ->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'Invalid reset link or token expired.']);
        }

        $admin->update([
            'password' => Hash::make($request->password),
            'remember_token' => null,
        ]);

        return redirect()->route('admin.login')->with('status', 'Password reset successfully. Please login with your new password.');
    }
}
