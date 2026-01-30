<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
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

        // Generate the content based on format and type
        $qrContent = $this->getQrContent($format, $content, $slug, $type);

        // Generate QR code as SVG (vector, no pixelation)
        $qrCode = QrCodeGenerator::format('svg')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($qrContent);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
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

        // For JPG, we need to generate PNG first then convert
        if ($format === 'jpg') {
            $qrCode = QrCodeGenerator::format('png')
                ->size(1000)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($qrContent);

            // Create image from PNG
            $image = imagecreatefromstring($qrCode);
            
            // Create white background
            $width = imagesx($image);
            $height = imagesy($image);
            $jpgImage = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($jpgImage, 255, 255, 255);
            imagefill($jpgImage, 0, 0, $white);
            
            // Copy PNG onto white background
            imagecopy($jpgImage, $image, 0, 0, 0, 0, $width, $height);
            
            // Output as JPEG
            ob_start();
            imagejpeg($jpgImage, null, 95);
            $qrCode = ob_get_clean();
            
            imagedestroy($image);
            imagedestroy($jpgImage);
            
            $mimeType = 'image/jpeg';
        } else {
            // Generate QR code in requested format
            $qrCode = QrCodeGenerator::format($format)
                ->size(1000)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($qrContent);
            
            $mimeType = $format === 'svg' ? 'image/svg+xml' : 'image/png';
        }

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
}
