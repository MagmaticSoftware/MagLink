<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\QrCode;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;

class RedirectController extends Controller
{
    /**
     * Handle link redirect with optional consent page.
     */
    public function link(Request $request, string $slug)
    {
        $link = Link::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Se richiede consenso e non è ancora stato dato, mostra pagina consenso
        if ($link->require_consent && !$request->has('consent')) {
            return inertia('ConsentPage', [
                'type' => 'link',
                'slug' => $slug,
                'destination' => $link->url,
                'title' => $link->title,
                'privacyVersion' => config('privacy.version'),
                'privacyText' => $this->getPrivacyText($request),
            ]);
        }

        // Registra la view
        if ($link->require_consent && $request->input('consent') === 'true') {
            // Con consenso: raccoglie tutti i dati + privacy compliance
            $privacyVersion = config('privacy.version');
            $privacyText = $this->getPrivacyText($request);
            View::createWithConsent(
                $link, 
                $this->extractTrackingData($request),
                $privacyVersion,
                $privacyText
            );
        } else {
            // Senza consenso: solo conteggio
            View::createWithoutConsent($link);
        }

        return redirect()->away($link->url);
    }

    /**
     * Handle QR code redirect with optional consent page.
     */
    public function qrCode(Request $request, string $slug)
    {
        $qrCode = QrCode::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Determina destination URL dal QR code
        $destination = $this->getQrCodeDestination($qrCode);

        // Se richiede consenso e non è ancora stato dato, mostra pagina consenso
        if ($qrCode->require_consent && !$request->has('consent')) {
            return inertia('ConsentPage', [
                'type' => 'qrcode',
                'slug' => $slug,
                'destination' => $destination,
                'title' => $qrCode->name,
                'privacyVersion' => config('privacy.version'),
                'privacyText' => $this->getPrivacyText($request),
            ]);
        }

        // Registra la view
        if ($qrCode->require_consent && $request->input('consent') === 'true') {
            $privacyVersion = config('privacy.version');
            $privacyText = $this->getPrivacyText($request);
            View::createWithConsent(
                $qrCode, 
                $this->extractTrackingData($request),
                $privacyVersion,
                $privacyText
            );
        } else {
            View::createWithoutConsent($qrCode);
        }

        // Incrementa scan counter
        $qrCode->increment('scans');
        $qrCode->update(['last_scanned_at' => now()]);

        return redirect()->away($destination);
    }

    /**
     * Extract tracking data from request.
     */
    private function extractTrackingData(Request $request): array
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        $agent->setHttpHeaders($request->headers->all());

        $data = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'language' => $request->getPreferredLanguage(),
            'browser' => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()),
            'platform' => $agent->platform(),
            'platform_version' => $agent->version($agent->platform()),
            'device_type' => $this->getDeviceType($agent),
            'device_model' => $agent->device(),
        ];

        // Screen resolution from query param (sent by frontend)
        if ($request->has('screen')) {
            $data['screen_resolution'] = $request->input('screen');
        }

        // Geolocation using ip-api.com (free, 45 requests/minute)
        try {
            $ip = $request->ip();
            // Skip geolocation for local/private IPs
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new \Exception('Private IP address');
            }

            /** @var \Illuminate\Http\Client\Response $geoResponse */
            $geoResponse = Http::timeout(2)
                ->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,city");
            
            if ($geoResponse->successful()) {
                $geo = $geoResponse->json();
                if (isset($geo['status']) && $geo['status'] === 'success') {
                    $data['country'] = $geo['country'] ?? null;
                    $data['country_code'] = $geo['countryCode'] ?? null;
                    $data['city'] = $geo['city'] ?? null;
                }
            }
        } catch (\Exception $e) {
            // Geolocation failed, continue without it
            // Could log this for monitoring: \Log::warning('Geolocation failed', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        return $data;
    }

    /**
     * Get device type from Agent instance.
     */
    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isTablet()) {
            return 'tablet';
        }
        if ($agent->isMobile()) {
            return 'mobile';
        }
        if ($agent->isDesktop()) {
            return 'desktop';
        }
        return 'unknown';
    }

    /**
     * Get destination URL from QR code based on type.
     */
    private function getQrCodeDestination(QrCode $qrCode): string
    {
        // Adatta in base al tuo sistema di QR code
        // Esempio base: se il payload contiene un URL
        if (isset($qrCode->payload['url'])) {
            return $qrCode->payload['url'];
        }

        // Default: converti il payload in stringa
        return is_array($qrCode->payload) 
            ? ($qrCode->payload['data'] ?? $qrCode->payload['content'] ?? '')
            : (string) $qrCode->payload;
    }

    /**
     * Get privacy policy text based on request language.
     */
    private function getPrivacyText(Request $request): string
    {
        $locale = $request->getPreferredLanguage(['it', 'en']) ?? config('privacy.default_language');
        
        return config("privacy.text.{$locale}") 
            ?? config('privacy.text.' . config('privacy.default_language'));
    }
}
