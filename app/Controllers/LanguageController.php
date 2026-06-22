<?php

namespace App\Controllers;

class LanguageController extends BaseController
{
    public function switchLang($locale)
    {
        $supportedLocales = ['en', 'hi'];
        if (in_array($locale, $supportedLocales, true)) {
            session()->set('locale', $locale);
        }
        
        return redirect()->back();
    }
}
