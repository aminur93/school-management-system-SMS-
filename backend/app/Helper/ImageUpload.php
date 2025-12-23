<?php

namespace App\Helper;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Encoders\JpegEncoder;
use Intervention\Image\Drivers\Gd\Encoders\PngEncoder;
use Intervention\Image\ImageManager;

class ImageUpload
{
    public static function uploadImageStorge(
        UploadedFile $imagePath, 
        $folder = null, 
        $width = null, 
        $height = null, 
        $format = 'png', 
        $quality = 100
    )
    {
        // Get original extension
        $extension = strtolower($imagePath->getClientOriginalExtension());

        // Generate a unique name for the image
        $imageName = time() . '_' . $imagePath->getClientOriginalName();

        // Define the storage path
        $storagePath = $folder ? "$folder/$imageName" : $imageName;

        // If it's a GIF, store it directly without processing
        if ($extension === 'gif') {
            Storage::disk('spaces')->put($storagePath, file_get_contents($imagePath));
            return $storagePath;
        }

        // If it's a GIF, store it directly without processing
        if ($extension === 'svg') {
            Storage::disk('spaces')->put($storagePath, file_get_contents($imagePath));
            return $storagePath;
        }

        // If it's a GIF, store it directly without processing
        if ($extension === 'webp') {
            Storage::disk('spaces')->put($storagePath, file_get_contents($imagePath));
            return $storagePath;
        }

        // For non-GIF images, process using GD
        $image = ImageManager::gd()->read($imagePath);

        // Resize only if width & height are provided
        if ($width && $height) {
            $image->resize($width, $height);
        }

        // Set appropriate encoder based on format
        $encoder = match ($format) {
            'jpg', 'jpeg' => new JpegEncoder($quality),
            'png' => new PngEncoder($quality),
            default => throw new \InvalidArgumentException("Unsupported format: $format"),
        };

        // Encode the image using the specified encoder
        $encodedImage = $image->encode($encoder);

        // Save the image to DigitalOcean Spaces
        Storage::disk('spaces')->put($storagePath, (string) $encodedImage);

        // Return the path of the uploaded image
        return $storagePath;
    }

    public static function deleteStorageImage($path)
    {
        if (Storage::disk('spaces')->exists($path)) {
            Storage::disk('spaces')->delete($path);
        }
    }

    public static function uploadImageApplicationStorage(
        UploadedFile $imageFile,
        $folder = null,
        $width = null,
        $height = null,
        $format = 'png',
        $quality = 100
    )
    {
        $extension = strtolower($imageFile->getClientOriginalExtension());

        $imageName = time() . '_' . preg_replace('/\s+/', '_', $imageFile->getClientOriginalName());

        // Relative path for DB
        $relativePath = $folder
            ? "{$folder}/{$imageName}"
            : $imageName;

        // Storage path
        $storagePath = "public/{$relativePath}";

        // Direct upload (no processing)
        if (in_array($extension, ['gif', 'svg', 'webp'])) {
            Storage::put($storagePath, file_get_contents($imageFile));
            return $relativePath;
        }

        $image = ImageManager::gd()->read($imageFile->getRealPath());

        if ($width && $height) {
            $image->resize($width, $height);
        }

        $encoder = match ($format) {
            'jpg', 'jpeg' => new JpegEncoder($quality),
            'png' => new PngEncoder($quality),
            default => throw new \InvalidArgumentException("Unsupported format: {$format}"),
        };

        Storage::put($storagePath, (string) $image->encode($encoder));

        return $relativePath;
    }

    public static function deleteApplicationStorage(?string $path) : bool
    {
        // if path not exist
        if (!$path) {
            return false;
        }

        // if public storage have path
        if (!str_starts_with($path, 'public/')) {
            $path = 'public/' . ltrim($path, '/');
        }

        // file exists or not
        if (Storage::exists($path)) {
            Storage::delete($path);
            return true;
        }

        return false;
    }
}