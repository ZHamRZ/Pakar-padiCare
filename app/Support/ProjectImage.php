<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;

class ProjectImage
{
    public static function store(UploadedFile $file, string $directory): string
    {
        $directory = trim('uploads/'.trim($directory, '/'), '/');
        $targetDirectory = public_path($directory);

        if (! is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'bin';
        $filename = Str::uuid()->toString().'.'.strtolower($extension);

        $file->move($targetDirectory, $filename);

        return $directory.'/'.$filename;
    }

    /**
     * Store a cropped image from base64 data
     */
    public static function storeCropped(string $base64Image, string $directory): string
    {
        $directory = trim('uploads/'.trim($directory, '/'), '/');
        $targetDirectory = public_path($directory);

        if (! is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        // Generate unique filename
        $filename = Str::uuid()->toString().'.jpg';
        $filepath = $targetDirectory.'/'.$filename;

        // Decode base64 image
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif, etc.

            $image = base64_decode($base64Image);

            if ($image === false) {
                throw new \Exception('Base64 decode failed');
            }

            // Save the image
            file_put_contents($filepath, $image);

            // Optional: Use Intervention Image to optimize/resize if needed
            if (class_exists(InterventionImage::class)) {
                try {
                    $img = InterventionImage::read($filepath);
                    $img->scale(width: 400, height: 400);
                    $img->save($filepath, quality: 85);
                } catch (\Exception $e) {
                    // If intervention fails, keep original
                }
            }
        } else {
            throw new \Exception('Invalid base64 image data');
        }

        return $directory.'/'.$filename;
    }

    public static function delete(?string $path): void
    {
        $normalized = self::normalize($path);

        if ($normalized === null) {
            return;
        }

        $publicFile = public_path($normalized);
        if (is_file($publicFile)) {
            @unlink($publicFile);

            return;
        }

        $legacyPath = self::legacyStoragePath($normalized);
        if ($legacyPath !== null) {
            Storage::disk('public')->delete($legacyPath);
        }
    }

    public static function url(?string $path): ?string
    {
        $normalized = self::normalize($path);

        if ($normalized === null) {
            return null;
        }

        if (Str::startsWith($normalized, ['http://', 'https://', '//'])) {
            return $normalized;
        }

        if (is_file(public_path($normalized))) {
            return asset($normalized);
        }

        $legacyPath = self::legacyStoragePath($normalized);
        if ($legacyPath !== null && Storage::disk('public')->exists($legacyPath)) {
            return asset('storage/'.$legacyPath);
        }

        return asset($normalized);
    }

    public static function normalize(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        return $normalized !== '' ? $normalized : null;
    }

    public static function legacyStoragePath(?string $path): ?string
    {
        $normalized = self::normalize($path);

        if ($normalized === null) {
            return null;
        }

        if (Str::startsWith($normalized, 'uploads/')) {
            return null;
        }

        if (Str::startsWith($normalized, 'storage/')) {
            return ltrim(Str::after($normalized, 'storage/'), '/');
        }

        return $normalized;
    }
}
