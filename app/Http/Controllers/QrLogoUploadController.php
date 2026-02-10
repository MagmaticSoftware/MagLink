<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrLogoUploadController extends Controller
{
    /**
     * Upload logo for QR code customization
     */
    public function upload(Request $request)
    {
        $user = $request->user();

        // Check if user has access to logo feature
        if (!$user->canUseQrFeature('logo')) {
            return response()->json([
                'error' => 'Logo upload is not available in your plan',
                'upgrade_required' => true,
            ], 403);
        }

        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048', // max 2MB
        ]);

        $file = $request->file('logo');
        $uploadPath = $user->getUploadPath('qr-logos');
        
        // Generate unique filename
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        
        // Store file
        $path = $file->storeAs($uploadPath, $filename, 'user_uploads');

        // If remove_background is available and requested, process it
        if ($request->boolean('remove_background') && $user->canUseQrFeature('remove_background')) {
            $path = $this->removeBackground($path);
        }

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::disk('user_uploads')->url($path),
        ]);
    }

    /**
     * Delete uploaded logo
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $user = $request->user();
        $path = $request->input('path');

        // Verify the path belongs to this user
        if (!str_starts_with($path, $user->id . '/')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete file
        Storage::disk('user_uploads')->delete($path);

        return response()->json(['success' => true]);
    }

    /**
     * Remove background from image using GD (simple approach)
     * For better results, consider using a service like remove.bg API
     */
    private function removeBackground(string $path): string
    {
        $fullPath = Storage::disk('user_uploads')->path($path);
        $imageInfo = getimagesize($fullPath);
        
        if (!$imageInfo || $imageInfo['mime'] !== 'image/png') {
            // Only works with PNG
            return $path;
        }

        $image = imagecreatefrompng($fullPath);
        if (!$image) {
            return $path;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Create a new image with transparency
        $newImage = imagecreatetruecolor($width, $height);
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparent);
        
        // Copy non-white pixels to new image
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($image, $x, $y);
                $colors = imagecolorsforindex($image, $rgb);
                
                // Calculate brightness (weighted average for human perception)
                $brightness = (0.299 * $colors['red']) + (0.587 * $colors['green']) + (0.114 * $colors['blue']);
                
                // If pixel is not close to white (brightness < 240), keep it
                // Also preserve existing transparency
                if ($brightness < 240 || (isset($colors['alpha']) && $colors['alpha'] > 10)) {
                    if (isset($colors['alpha'])) {
                        // Preserve original alpha
                        $newColor = imagecolorallocatealpha($newImage, $colors['red'], $colors['green'], $colors['blue'], $colors['alpha']);
                    } else {
                        // Fully opaque
                        $newColor = imagecolorallocatealpha($newImage, $colors['red'], $colors['green'], $colors['blue'], 0);
                    }
                    imagesetpixel($newImage, $x, $y, $newColor);
                }
            }
        }

        // Save modified image
        imagepng($newImage, $fullPath, 9);
        imagedestroy($image);
        imagedestroy($newImage);

        return $path;
    }
}
