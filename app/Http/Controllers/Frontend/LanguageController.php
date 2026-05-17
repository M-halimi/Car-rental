<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (! in_array($locale, ['en', 'fr', 'ar'])) {
            return redirect()->back();
        }

        session(['locale' => $locale]);
        Cookie::queue('locale', $locale, 60 * 24 * 30);
        app()->setLocale($locale);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'direction' => $locale === 'ar' ? 'rtl' : 'ltr',
            ]);
        }

        return redirect()->back();
    }
}
