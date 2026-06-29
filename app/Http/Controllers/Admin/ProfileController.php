<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminProfileUpdateRequest;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    use FileUploadTrait;

    /**
     * Display the admin's profile.
     */
    public function index(): View
    {
        $admin = Auth::guard('admin')->user();

        return view('admin.profile.index', ['admin' => $admin]);
    }

    /**
     * Update the admin's profile.
     */
    public function update(AdminProfileUpdateRequest $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        // Handle file upload if image is provided
        $profileImage = $request->file('profile_image')
            ? $this->uploadFile($request, 'profile_image', 'uploads/profiles', $admin->profile_image)
            : $admin->profile_image;

        // Prepare update data
        $updateData = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'profile_image' => $profileImage,
        ];

        // Hash password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->input('password'));
        }

        // Update admin profile
        $admin->update($updateData);

        // Show SweetAlert toast notification
        Alert::toast(__('messages.profile_updated_successfully'), 'success');

        return redirect()->route('admin.profile.index');
    }
}
