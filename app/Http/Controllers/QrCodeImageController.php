<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeImageController extends Controller
{
    /**
     * Generate QR code preview image (SVG for crisp rendering)
     */
    public function preview(Request $request)
    {
        $slug = $request->query('slug');
        $format = $request->query('format', 'url');
        $content = $request->query('content', '');
        $type = $request->query('type', 'static'); // dynamic or static

        // Get customization options from request
        $customization = [
            'colors' => [
                'foreground' => $request->query('color_fg', '#000000'),
                'background' => $request->query('color_bg', '#FFFFFF'),
            ],
            'logo' => $request->query('logo'),
            'logo_size' => $request->query('logo_size', 'medium'),
            'logo_rounded' => $request->query('logo_rounded', false),
            'remove_background' => $request->query('remove_background', false),
            'body_shape' => $request->query('body_shape', 'square'),
            'eye_shape' => $request->query('eye_shape', 'square'),
        ];

        // Generate the content based on format and type
        $qrContent = $this->getQrContent($format, $content, $slug, $type);

        // Use PNG if logo is specified (SVG doesn't support logo overlay)
        $previewFormat = !empty($customization['logo']) ? 'png' : 'svg';
        $mimeType = $previewFormat === 'png' ? 'image/png' : 'image/svg+xml';

        // Generate QR code with customizations
        $qrCode = $this->generateQrCodeWithCustomization($qrContent, $customization, $previewFormat, 300);

        return response($qrCode)->header('Content-Type', $mimeType);
    }

    /**
     * Download QR code image
     */
    public function download(QrCode $qrcode, $format = 'png')
    {
        $validFormats = ['png', 'svg', 'jpg', 'jpeg'];
        if (!in_array($format, $validFormats)) {
            $format = 'png';
        }

        // Normalize jpeg to jpg
        if ($format === 'jpeg') {
            $format = 'jpg';
        }

        // Generate the QR content
        $qrContent = $this->getQrContentFromModel($qrcode);

        // Get customization from qrcode options
        $customization = $qrcode->options['customization'] ?? [];

        // Determine size based on user plan
        $user = $qrcode->user;
        $maxSize = $user ? $user->getQrMaxSize() : 512;

        // Generate QR code with customizations
        $qrCode = $this->generateQrCodeWithCustomization($qrContent, $customization, $format, $maxSize);

        $mimeType = match($format) {
            'svg' => 'image/svg+xml',
            'jpg' => 'image/jpeg',
            default => 'image/png',
        };

        $fileName = ($qrcode->name ?? $qrcode->slug) . '-qrcode.' . $format;

        return response($qrCode)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Get QR content from query parameters
     */
    private function getQrContent($format, $content, $slug = null, $type = 'static'): string
    {
        $baseUrl = config('app.short_url');
        
        // For DYNAMIC QR codes, always use the short URL (redirect controllato)
        if ($type === 'dynamic') {
            return "$baseUrl/q/$slug";
        }
        
        // For STATIC QR codes, embed the actual content directly
        switch ($format) {
            case 'url':
                return $content ?: 'https://example.com';
            case 'text':
                return $content;
            case 'email':
                return $content ? "mailto:$content" : 'mailto:example@email.com';
            case 'phone':
                return $content ? "tel:$content" : 'tel:+1234567890';
            case 'sms':
                return $content ? "sms:$content" : 'sms:+1234567890';
            case 'vcard':
                // TODO: Parse vcard data and format properly
                return $content;
            default:
                return $content;
        }
    }

    /**
     * Get QR content from QrCode model
     */
    private function getQrContentFromModel(QrCode $qrcode): string
    {
        $baseUrl = config('app.short_url');
        $payload = $qrcode->payload;

        switch ($qrcode->format) {
            case 'url':
                // For dynamic QR codes, use the short URL
                if ($qrcode->type === 'dynamic') {
                    return "$baseUrl/q/{$qrcode->slug}";
                }
                // For static, use the direct URL from payload
                return $payload['url'] ?? $payload['content'] ?? "$baseUrl/q/{$qrcode->slug}";
            
            case 'text':
                return $payload['content'] ?? $payload['text'] ?? '';
            
            case 'email':
                $email = $payload['email'] ?? $payload['content'] ?? '';
                $subject = $payload['subject'] ?? '';
                $body = $payload['body'] ?? '';
                
                $mailto = "mailto:$email";
                $params = [];
                if ($subject) $params[] = "subject=" . urlencode($subject);
                if ($body) $params[] = "body=" . urlencode($body);
                if ($params) $mailto .= '?' . implode('&', $params);
                
                return $mailto;
            
            case 'phone':
                return "tel:" . ($payload['phone'] ?? $payload['content'] ?? '');
            
            case 'sms':
                $phone = $payload['phone'] ?? $payload['content'] ?? '';
                $message = $payload['message'] ?? '';
                return "sms:$phone" . ($message ? "?body=" . urlencode($message) : '');
            
            case 'vcard':
                return $this->generateVCard($payload);
            
            default:
                return "$baseUrl/q/{$qrcode->slug}";
        }
    }

    /**
     * Generate vCard format
     */
    private function generateVCard(array $data): string
    {
        $vcard = "BEGIN:VCARD\nVERSION:3.0\n";
        
        if (isset($data['name'])) {
            $vcard .= "FN:{$data['name']}\n";
        }
        if (isset($data['organization'])) {
            $vcard .= "ORG:{$data['organization']}\n";
        }
        if (isset($data['phone'])) {
            $vcard .= "TEL:{$data['phone']}\n";
        }
        if (isset($data['email'])) {
            $vcard .= "EMAIL:{$data['email']}\n";
        }
        if (isset($data['url'])) {
            $vcard .= "URL:{$data['url']}\n";
        }
        
        $vcard .= "END:VCARD";
        
        return $vcard;
    }

    /**
     * Generate QR code with customizations
     */
    private function generateQrCodeWithCustomization(string $content, array $customization, string $format, int $size = 1000): string
    {
        $generator = QrCodeGenerator::format($format)
            ->size($size)
            ->margin(2)
            ->errorCorrection('H');

        // Apply color customization
        $fgColor = $customization['colors']['foreground'] ?? '#000000';
        $bgColor = $customization['colors']['background'] ?? '#FFFFFF';
        
        // Convert hex to RGB
        $fgRgb = $this->hexToRgb($fgColor);
        $bgRgb = $this->hexToRgb($bgColor);
        
        $generator->color($fgRgb[0], $fgRgb[1], $fgRgb[2]);
        $generator->backgroundColor($bgRgb[0], $bgRgb[1], $bgRgb[2]);

        // Generate base QR code
        $qrCode = $generator->generate($content);

        // Apply logo if specified (only for PNG/JPG)
        if (($format === 'png' || $format === 'jpg') && !empty($customization['logo'])) {
            $qrCode = $this->applyLogoToQrCode(
                $qrCode, 
                $customization['logo'], 
                $customization['logo_size'] ?? 'medium', 
                $customization['logo_rounded'] ?? false,
                $customization['remove_background'] ?? false,
                $size
            );
        }

        // For JPG format, ensure white background
        if ($format === 'jpg') {
            $qrCode = $this->convertToJpeg($qrCode, $bgColor);
        }

        return $qrCode;
    }

    /**
     * Convert hex color to RGB array
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Apply logo to QR code image
     */
    private function applyLogoToQrCode(string $qrImageData, string $logoPath, string $logoSize, bool $rounded, bool $removeBackground, int $qrSize): string
    {
        // Create QR code image from string
        $qrImage = imagecreatefromstring($qrImageData);
        if (!$qrImage) {
            return $qrImageData;
        }

        // Load logo from storage
        $logoFullPath = Storage::disk('user_uploads')->path($logoPath);
        
        if (!file_exists($logoFullPath)) {
            imagedestroy($qrImage);
            return $qrImageData;
        }

        // Determine logo file type and create image
        $imageInfo = getimagesize($logoFullPath);
        $logoImage = match($imageInfo['mime']) {
            'image/png' => imagecreatefrompng($logoFullPath),
            'image/jpeg' => imagecreatefromjpeg($logoFullPath),
            'image/jpg' => imagecreatefromjpeg($logoFullPath),
            default => null,
        };

        if (!$logoImage) {
            imagedestroy($qrImage);
            return $qrImageData;
        }

        // Calculate logo size (percentage of QR code)
        $logoSizePercentage = match($logoSize) {
            'small' => 0.15,
            'large' => 0.25,
            default => 0.20, // medium
        };

        $logoWidth = (int)($qrSize * $logoSizePercentage);
        $logoHeight = (int)($logoWidth * (imagesy($logoImage) / imagesx($logoImage)));

        // Calculate center position
        $logoX = (int)(($qrSize - $logoWidth) / 2);
        $logoY = (int)(($qrSize - $logoHeight) / 2);

        // Create a temporary image with transparency support for resizing
        $resizedLogo = imagecreatetruecolor($logoWidth, $logoHeight);
        
        // Preserve transparency for PNG images
        if ($imageInfo['mime'] === 'image/png') {
            imagealphablending($resizedLogo, false);
            imagesavealpha($resizedLogo, true);
            $transparent = imagecolorallocatealpha($resizedLogo, 0, 0, 0, 127);
            imagefill($resizedLogo, 0, 0, $transparent);
            imagealphablending($resizedLogo, true);
        }
        
        // Resize logo with transparency preserved
        imagecopyresampled($resizedLogo, $logoImage, 0, 0, 0, 0, $logoWidth, $logoHeight, imagesx($logoImage), imagesy($logoImage));
        
        // Check if we need to add white background (ONLY for transparent images)
        // Don't add background if user explicitly requested to remove it
        $needsBackground = false;
        if (!$removeBackground && $imageInfo['mime'] === 'image/png') {
            // For PNG, add background only if it has significant transparency
            $needsBackground = $this->imageHasSignificantTransparency($resizedLogo, $logoWidth, $logoHeight);
        }
        // JPG images never need background (they're always opaque)

        // If background needed, create composite image with logo + white background
        if ($needsBackground) {
            $padding = max(2, (int)($logoWidth * 0.02));
            $compositeWidth = $logoWidth + ($padding * 2);
            $compositeHeight = $logoHeight + ($padding * 2);
            
            // Create composite image with white background
            $composite = imagecreatetruecolor($compositeWidth, $compositeHeight);
            imagealphablending($composite, false);
            imagesavealpha($composite, true);
            
            // Fill with white
            $white = imagecolorallocate($composite, 255, 255, 255);
            imagefill($composite, 0, 0, $white);
            
            // Copy logo onto white background (centered) - NO rounded corners yet
            imagealphablending($composite, true);
            imagecopy($composite, $resizedLogo, $padding, $padding, 0, 0, $logoWidth, $logoHeight);
            
            // Replace resizedLogo with composite
            imagedestroy($resizedLogo);
            $resizedLogo = $composite;
            $logoWidth = $compositeWidth;
            $logoHeight = $compositeHeight;
            
            // Recalculate center position for larger composite
            $logoX = (int)(($qrSize - $logoWidth) / 2);
            $logoY = (int)(($qrSize - $logoHeight) / 2);
        }
        
        // Copy final logo onto QR code
        if ($rounded) {
            // Copy with rounded corners (pixel by pixel to respect corners)
            $this->copyImageWithRoundedCorners($qrImage, $resizedLogo, $logoX, $logoY, $logoWidth, $logoHeight);
        } else {
            // Standard copy with alpha blending
            imagealphablending($qrImage, true);
            imagecopy($qrImage, $resizedLogo, $logoX, $logoY, 0, 0, $logoWidth, $logoHeight);
        }
        
        imagedestroy($resizedLogo);

        // Output modified image
        ob_start();
        imagepng($qrImage, null, 9);
        $result = ob_get_clean();

        imagedestroy($qrImage);
        imagedestroy($logoImage);

        return $result;
    }

    /**
     * Check if image has significant transparency
     * Samples pixels to determine if transparency is meaningful
     */
    private function imageHasSignificantTransparency($image, int $width, int $height): bool
    {
        $transparentPixels = 0;
        $sampleSize = min(1000, $width * $height); // Sample up to 1000 pixels
        $step = max(1, (int)sqrt(($width * $height) / $sampleSize));
        
        for ($x = 0; $x < $width; $x += $step) {
            for ($y = 0; $y < $height; $y += $step) {
                $colorIndex = imagecolorat($image, $x, $y);
                $color = imagecolorsforindex($image, $colorIndex);
                
                // Check if pixel is transparent (alpha > 64 means somewhat transparent)
                if (isset($color['alpha']) && $color['alpha'] > 64) {
                    $transparentPixels++;
                }
            }
        }
        
        // If more than 10% of sampled pixels are transparent, consider it significant
        $totalSampled = (int)(($width / $step) * ($height / $step));
        return ($transparentPixels / max(1, $totalSampled)) > 0.10;
    }

    /**
     * Copy image onto another with rounded corners (pixel by pixel)
     */
    private function copyImageWithRoundedCorners($destImage, $srcImage, int $destX, int $destY, int $width, int $height): void
    {
        // Calculate corner radius (15% of the smallest dimension)
        $radius = (int)(min($width, $height) * 0.15);
        
        // First, copy the entire image using standard method for better color preservation
        imagealphablending($destImage, true);
        
        // For each line, copy in segments to avoid corners
        for ($y = 0; $y < $height; $y++) {
            // Determine the X range to copy for this line (skip corners)
            $startX = 0;
            $endX = $width - 1;
            
            if ($y < $radius) {
                // Top section - calculate X offset based on circle
                $dy = $radius - $y;
                $dx = (int)sqrt(max(0, $radius * $radius - $dy * $dy));
                $startX = $radius - $dx;
                $endX = $width - $startX - 1;
            } else if ($y >= $height - $radius) {
                // Bottom section - calculate X offset based on circle
                $dy = $y - ($height - $radius);
                $dx = (int)sqrt(max(0, $radius * $radius - $dy * $dy));
                $startX = $radius - $dx;
                $endX = $width - $startX - 1;
            }
            
            // Copy the valid segment of this line
            if ($startX <= $endX) {
                imagecopy(
                    $destImage, 
                    $srcImage, 
                    $destX + $startX, 
                    $destY + $y, 
                    $startX, 
                    $y, 
                    $endX - $startX + 1, 
                    1
                );
            }
        }
    }

    /**
     * Apply rounded corners to an image
     */
    private function applyRoundedCorners($image, int $width, int $height)
    {
        // Calculate corner radius (15% of the smallest dimension)
        $radius = (int)(min($width, $height) * 0.15);
        
        // Create a new image with transparency
        $rounded = imagecreatetruecolor($width, $height);
        imagealphablending($rounded, false);
        imagesavealpha($rounded, true);
        
        // Fill with transparent color
        $transparent = imagecolorallocatealpha($rounded, 0, 0, 0, 127);
        imagefill($rounded, 0, 0, $transparent);
        
        // Enable alpha blending for drawing
        imagealphablending($rounded, true);
        
        // Copy the original image
        imagecopy($rounded, $image, 0, 0, 0, 0, $width, $height);
        
        // Create mask for corners
        imagealphablending($rounded, false);
        
        // Draw transparent rounded corners
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                // Check if pixel is in corner radius
                $inCorner = false;
                
                // Top-left corner
                if ($x < $radius && $y < $radius) {
                    $dx = $radius - $x;
                    $dy = $radius - $y;
                    if (($dx * $dx + $dy * $dy) > ($radius * $radius)) {
                        $inCorner = true;
                    }
                }
                // Top-right corner
                else if ($x >= $width - $radius && $y < $radius) {
                    $dx = $x - ($width - $radius - 1);
                    $dy = $radius - $y;
                    if (($dx * $dx + $dy * $dy) > ($radius * $radius)) {
                        $inCorner = true;
                    }
                }
                // Bottom-left corner
                else if ($x < $radius && $y >= $height - $radius) {
                    $dx = $radius - $x;
                    $dy = $y - ($height - $radius - 1);
                    if (($dx * $dx + $dy * $dy) > ($radius * $radius)) {
                        $inCorner = true;
                    }
                }
                // Bottom-right corner
                else if ($x >= $width - $radius && $y >= $height - $radius) {
                    $dx = $x - ($width - $radius - 1);
                    $dy = $y - ($height - $radius - 1);
                    if (($dx * $dx + $dy * $dy) > ($radius * $radius)) {
                        $inCorner = true;
                    }
                }
                
                // Make corner pixels transparent
                if ($inCorner) {
                    imagesetpixel($rounded, $x, $y, $transparent);
                }
            }
        }
        
        // Destroy original image and return rounded version
        if ($image !== $rounded) {
            imagedestroy($image);
        }
        
        return $rounded;
    }

    /**
     * Convert PNG to JPEG with proper background
     */
    private function convertToJpeg(string $pngData, string $bgColor): string
    {
        $image = imagecreatefromstring($pngData);
        if (!$image) {
            return $pngData;
        }
        
        $width = imagesx($image);
        $height = imagesy($image);
        $jpgImage = imagecreatetruecolor($width, $height);
        
        // Use custom background color
        $bgRgb = $this->hexToRgb($bgColor);
        $bg = imagecolorallocate($jpgImage, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
        imagefill($jpgImage, 0, 0, $bg);
        
        // Copy PNG onto background
        imagecopy($jpgImage, $image, 0, 0, 0, 0, $width, $height);
        
        // Output as JPEG
        ob_start();
        imagejpeg($jpgImage, null, 95);
        $result = ob_get_clean();
        
        imagedestroy($image);
        imagedestroy($jpgImage);
        
        return $result;
    }
}
