<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'subject' => 'required|string|in:general,sales,support,billing,partnership,other',
            'message' => 'required|string|max:2000',
            'newsletter' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contactData = $validator->validated();

        try {
            // Send email to admin
            Mail::to(config('mail.admin_email', 'admin@ballie.ng'))
                ->send(new ContactFormMail($contactData));

            // Optionally, send confirmation email to user
            if (config('mail.send_contact_confirmation', true)) {
                Mail::to($contactData['email'])
                    ->send(new ContactFormMail($contactData, true));
            }

            return redirect()->back()
                ->with('success', 'Thank you for your message! We\'ll get back to you within 24 hours.');

        } catch (\Exception $e) {
            \Log::error('Contact form submission failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Sorry, there was an error sending your message. Please try again or contact us directly.')
                ->withInput();
        }
    }
}
