<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportResponseTemplate;
use Illuminate\Http\Request;

class ResponseTemplateController extends Controller
{
    /**
     * Display a listing of response templates.
     */
    public function index()
    {
        $templates = SupportResponseTemplate::latest()->paginate(20);

        return view('super-admin.support.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new response template.
     */
    public function create()
    {
        return view('super-admin.support.templates.create');
    }

    /**
     * Store a newly created response template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        SupportResponseTemplate::create($validated);

        return redirect()
            ->route('super-admin.support.templates.index')
            ->with('success', 'Response template created successfully!');
    }

    /**
     * Show the form for editing the specified response template.
     */
    public function edit(SupportResponseTemplate $template)
    {
        return view('super-admin.support.templates.edit', compact('template'));
    }

    /**
     * Update the specified response template in storage.
     */
    public function update(Request $request, SupportResponseTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $template->update($validated);

        return redirect()
            ->route('super-admin.support.templates.index')
            ->with('success', 'Response template updated successfully!');
    }

    /**
     * Remove the specified response template from storage.
     */
    public function destroy(SupportResponseTemplate $template)
    {
        $template->delete();

        return redirect()
            ->route('super-admin.support.templates.index')
            ->with('success', 'Response template deleted successfully!');
    }
}
