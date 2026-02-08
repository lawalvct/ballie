<?php

namespace App\Http\Controllers;

use App\Models\CustomPlanInquiry;
use App\Models\SuperAdmin;
use App\Notifications\CustomPlanInquiryNotification;
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

    public function submitCustomPlanInquiry(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'num_companies' => 'nullable|integer|min:1|max:9999',
            'interest' => 'nullable|string|in:lifetime,custom_app,both,other',
            'requirements' => 'nullable|string|max:2000',
        ]);

        $inquiry = CustomPlanInquiry::create([
            ...$validated,
            'ip_address' => $request->ip(),
        ]);

        // Notify all superadmins
        $superAdmins = SuperAdmin::where('is_active', true)->get();
        foreach ($superAdmins as $admin) {
            $admin->notify(new CustomPlanInquiryNotification($inquiry));
        }

        return response()->json([
            'success' => true,
            'message' => 'Your inquiry has been submitted successfully. We will get back to you within 24 hours.',
        ]);
    }
}
