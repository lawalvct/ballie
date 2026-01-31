<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function welcome()
    {
        $brand = app()->bound('brand') ? app('brand') : null;
        $view = $brand['landing_view'] ?? 'welcome';

        if (!view()->exists($view)) {
            $view = 'welcome';
        }

        return view($view, compact('brand'));
    }

    public function features()
    {
        return view('features');
    }

    public function pricing()
    {
        return view('pricing');
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function demo()
    {
        return view('demo');
    }

    public function terms()
    {
        return view('legal.terms');
    }

    public function privacy()
    {
        return view('legal.privacy');
    }

    public function cookies()
    {
        return view('legal.cookies');
    }
}
