<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminProfileUpdateRequest;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

        // Update admin profile
        $admin->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'profile_image' => $profileImage,
        ]);

        return redirect()->route('admin.profile.index')
            ->with('success', __('messages.profile_updated_successfully'));
    }
}
