<?php

namespace App\Helper;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoUpload
{
    public static function uploadVideoStorage(UploadedFile $videoPath, $folder = null)
    {
        // Get original extension
        $extension = strtolower($videoPath->getClientOriginalExtension());
    
        // Generate a unique name for the video
        $videoName = time() . '_' . $videoPath->getClientOriginalName();
    
        // Define the storage path
        $storagePath = $folder ? "$folder/$videoName" : $videoName;
    
        // Upload video directly to DigitalOcean Spaces
        Storage::disk('spaces')->put($storagePath, file_get_contents($videoPath));
    
        // Return the path of the uploaded video
        return $storagePath;
    }

    public static function deleteUploadVideoStorage($path)
    {
        if (Storage::disk('spaces')->exists($path)) {
            Storage::disk('spaces')->delete($path);
        }
    }

    public static function uploadApplicationVideoStorage(
        UploadedFile $videoFile,
        $folder = null
    )
    {
        // Extension
        $extension = strtolower($videoFile->getClientOriginalExtension());

        // Safe & unique file name
        $videoName = time() . '_' . uniqid() . '.' . $extension;

        // Storage path (storage/app/public)
        $storagePath = $folder
            ? "public/{$folder}/{$videoName}"
            : "public/{$videoName}";

        // Store video directly (no processing)
        Storage::put($storagePath, file_get_contents($videoFile));

        return $storagePath;
    }

    public static function deleteApplicationVideoStorage(?string $path) : bool
    {
        if (!$path) {
            return false;
        }

        // Ensure public path
        if (!str_starts_with($path, 'public/')) {
            $path = 'public/' . ltrim($path, '/');
        }

        if (Storage::exists($path)) {
            Storage::delete($path);
            return true;
        }

        return false;
    }
}