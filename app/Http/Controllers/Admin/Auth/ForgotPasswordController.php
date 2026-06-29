<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendResetLinkRequest;
use App\Mail\AdminSendResetLinkMail;
use App\Models\Admin;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.forgot-password');
    }

    public function store(SendResetLinkRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'Email not found.']);
        }

        $token = Str::random(64);

        $admin->update(['remember_token' => $token]);

        Mail::to($request->email)->send(new AdminSendResetLinkMail($token, $request->email));

        return back()->with('status', 'A password reset link has been sent to your email address.');
    }
}
