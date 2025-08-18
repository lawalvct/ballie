<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    /**
     * Upload and process an image
     */
    public static function uploadImage(UploadedFile $file, string $directory = 'images', array $sizes = []): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;

        // Store original image
        $file->storeAs('public/' . $directory, $filename);

        // Create thumbnails if sizes are specified
        if (!empty($sizes)) {
            foreach ($sizes as $size => $dimensions) {
                $thumbnail = Image::make($file)
                    ->fit($dimensions['width'], $dimensions['height'])
                    ->encode();

                Storage::put(
                    'public/' . $directory . '/' . $size . '_' . $filename,
                    $thumbnail
                );
            }
        }

        return $path;
    }

    /**
     * Delete an image and its thumbnails
     */
    public static function deleteImage(string $path, array $sizes = []): void
    {
        if (Storage::exists('public/' . $path)) {
            Storage::delete('public/' . $path);
        }

        // Delete thumbnails
        if (!empty($sizes)) {
            $directory = dirname($path);
            $filename = basename($path);

            foreach ($sizes as $size => $dimensions) {
                $thumbnailPath = 'public/' . $directory . '/' . $size . '_' . $filename;
                if (Storage::exists($thumbnailPath)) {
                    Storage::delete($thumbnailPath);
                }
            }
        }
    }
}