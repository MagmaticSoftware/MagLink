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
        $qrCodes = QrCode::orderBy('created_at', 'desc')->get();
        return Inertia::render('tenant/qrcodes/Index', [
            'qrCodes' => $qrCodes,
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
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQrCodeRequest $request)
    {
        $validated = $request->validated();
        $qrCode = QrCode::create($validated);
        return redirect()->route('qrcodes.index')->with('success', 'QR Code created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(QrCode $qrCode)
    {
        return Inertia::render('tenant/qrcodes/Show', [
            'qrCode' => $qrCode,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QrCode $qrCode)
    {
        return Inertia::render('tenant/qrcodes/Edit', [
            'qrCode' => $qrCode,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQrCodeRequest $request, QrCode $qrCode)
    {
        $validated = $request->validated();
        $qrCode->update($validated);
        return redirect()->route('qrcodes.index')->with('success', 'QR Code updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QrCode $qrCode)
    {
        $qrCode->delete();
        return redirect()->route('qrcodes.index');
    }

    /**
     * Generate a random slug unique in database.
     */
    private function generateRandomSlug(): string
    {
        do {
            $slug = Str::random(8);
        } while (QrCode::where('slug', $slug)->exists());

        return $slug;
    }
}
