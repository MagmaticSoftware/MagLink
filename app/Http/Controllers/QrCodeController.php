<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQrCodeRequest;
use App\Http\Requests\UpdateQrCodeRequest;
use App\Models\QrCode;
use Inertia\Inertia;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $qrcodes = QrCode::orderBy('created_at', 'desc')->get();
        return Inertia::render('tenant/qrcodes/Index', [
            'qrcodes' => $qrcodes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $slug = $this->generateRandomSlug();
        return Inertia::render('tenant/qrcodes/Create', [
            'slug' => $slug,
            'shortUrl' => config('app.short_url'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQrCodeRequest $request)
    {
        $validated = $request->validated();
        $qrcode = QrCode::create($validated);
        return redirect()->route('qrcodes.index')->with('success', 'QR Code created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(QrCode $qrcode)
    {
        return Inertia::render('tenant/qrcodes/Show', [
            'qrcode' => $qrcode,
            'shortUrl' => config('app.short_url'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QrCode $qrcode)
    {
        return Inertia::render('tenant/qrcodes/Edit', [
            'qrcode' => $qrcode,
            'shortUrl' => config('app.short_url'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQrCodeRequest $request, QrCode $qrcode)
    {
        $validated = $request->validated();
        $qrcode->update($validated);
        return redirect()->route('qrcodes.index')->with('success', 'QR Code updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QrCode $qrcode)
    {
        $qrcode->delete();
        return redirect()->route('qrcodes.index');
    }

    /**
     * Handle public QR code scan and redirect
     */
    public function showPublicQr(QrCode $qrcode)
    {
        // Increment scan counter
        $qrcode->increment('scans');
        $qrcode->update(['last_scanned_at' => now()]);

        // Get payload
        $payload = $qrcode->payload;

        // TODO: Add tracking statistics (IP, device, location, etc.)
        // TODO: Add optional consent page before redirect

        // Redirect based on format
        switch ($qrcode->format) {
            case 'url':
                $url = $payload['url'] ?? $payload['content'] ?? '/';
                return redirect($url);
            
            case 'text':
                // Show text content page
                return Inertia::render('QrContent/Text', [
                    'content' => $payload['content'] ?? '',
                    'qrcode' => $qrcode
                ]);
            
            case 'email':
                $email = $payload['email'] ?? $payload['content'] ?? '';
                return redirect("mailto:$email");
            
            case 'phone':
                $phone = $payload['phone'] ?? $payload['content'] ?? '';
                return redirect("tel:$phone");
            
            case 'sms':
                $phone = $payload['phone'] ?? $payload['content'] ?? '';
                return redirect("sms:$phone");
            
            case 'vcard':
                // Return vCard file download
                // TODO: Implement vCard download
                return response('vCard download coming soon', 501);
            
            default:
                return redirect('/');
        }
    }

    /**
     * Generate a random slug unique in database.
     */
    private function generateRandomSlug(): string
    {
        do {
            $slug = strtolower(Str::random(8)); // 8 caratteri alfanumerici
        } while (QrCode::where('slug', $slug)->exists());

        return $slug;
    }
}
