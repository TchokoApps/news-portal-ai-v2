<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait FileUploadTrait
{
    /**
     * Upload a file and return the path.
     *
     * @param Request $request The request object
     * @param string $fieldName The name of the file input field
     * @param string $uploadDir The directory to store files (e.g., 'uploads/profiles')
     * @param string|null $oldPath The path of the old file to delete (optional)
     *
     * @return string|null The path to the uploaded file, or null if no file was uploaded
     */
    public function uploadFile(Request $request, string $fieldName, string $uploadDir, ?string $oldPath = null): ?string
    {
        // Check if the file exists in the request
        if (!$request->hasFile($fieldName)) {
            return null;
        }

        // Get the file from the request
        $file = $request->file($fieldName);

        // Validate that the file is valid
        if (!$file->isValid()) {
            return null;
        }

        // Delete the old file if it exists and a path was provided
        if ($oldPath && File::exists(public_path($oldPath))) {
            File::delete(public_path($oldPath));
        }

        // Create the upload directory if it doesn't exist
        $uploadPath = public_path($uploadDir);
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true, true);
        }

        // Generate a unique filename
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();

        // Move the file to the upload directory
        $file->move($uploadPath, $filename);

        // Return the path relative to public directory
        return $uploadDir . '/' . $filename;
    }

    /**
     * Delete a file if it exists.
     *
     * @param string|null $filePath The path to the file (relative to public directory)
     * @return bool True if deleted, false otherwise
     */
    public function deleteFile(?string $filePath): bool
    {
        if (!$filePath || !File::exists(public_path($filePath))) {
            return false;
        }

        return File::delete(public_path($filePath));
    }
}
