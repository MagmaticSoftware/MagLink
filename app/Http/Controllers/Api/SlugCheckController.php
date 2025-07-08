<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Tenant;

class SlugCheckController extends Controller
{
    public function __invoke(Request $request)
    {
        $slug = $request->query('slug');
        $exists = false;
        if ($slug && is_string($slug)) {
            $exists = Company::where('slug', $slug)->exists();
            if (!$exists) {
                $exists = Tenant::where('id', $slug)->exists();
            }
        }
        return response()->json(['exists' => $exists]);
    }
}
