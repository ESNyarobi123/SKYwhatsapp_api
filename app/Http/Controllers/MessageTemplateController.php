<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    /**
     * Display a listing of message templates.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $category = $request->get('category');

        $templates = MessageTemplate::where('user_id', $user->id)
            ->when($category, fn($q) => $q->category($category))
            ->orderBy('usage_count', 'desc')
            ->paginate(12);

        $categories = [
            'general' => ['name' => 'General', 'icon' => '📝', 'color' => '#6B7280'],
            'marketing' => ['name' => 'Marketing', 'icon' => '📢', 'color' => '#EC4899'],
            'support' => ['name' => 'Support', 'icon' => '🎧', 'color' => '#3B82F6'],
            'notification' => ['name' => 'Notification', 'icon' => '🔔', 'color' => '#F59E0B'],
        ];

        return view('dashboard.templates.index', compact('templates', 'categories', 'category'));
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:4096',
            'category' => 'required|string|in:general,marketing,support,notification',
        ]);

        $template = new MessageTemplate($validated);
        $template->user_id = auth()->id();
        
        // Extract variables from content
        preg_match_all('/\{\{(\w+)\}\}/', $validated['content'], $matches);
        $template->variables = $matches[1] ?? [];
        
        $template->save();

        return redirect()->route('dashboard.templates.index')
            ->with('success', 'Template created successfully!');
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, MessageTemplate $template)
    {
        // Ensure user owns the template
        if ($template->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:4096',
            'category' => 'required|string|in:general,marketing,support,notification',
            'is_active' => 'boolean',
        ]);

        // Extract variables from content
        preg_match_all('/\{\{(\w+)\}\}/', $validated['content'], $matches);
        $validated['variables'] = $matches[1] ?? [];

        $template->update($validated);

        return redirect()->route('dashboard.templates.index')
            ->with('success', 'Template updated successfully!');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(MessageTemplate $template)
    {
        // Ensure user owns the template
        if ($template->user_id !== auth()->id()) {
            abort(403);
        }

        $template->delete();

        return redirect()->route('dashboard.templates.index')
            ->with('success', 'Template deleted successfully!');
    }

    /**
     * Use a template (increment usage count and return content).
     */
    public function use(Request $request, MessageTemplate $template)
    {
        // Ensure user owns the template
        if ($template->user_id !== auth()->id()) {
            abort(403);
        }

        $variables = $request->get('variables', []);
        $content = $template->parseContent($variables);
        $template->incrementUsage();

        return response()->json([
            'success' => true,
            'content' => $content,
            'variables' => $template->variables,
        ]);
    }

    /**
     * Duplicate a template.
     */
    public function duplicate(MessageTemplate $template)
    {
        // Ensure user owns the template
        if ($template->user_id !== auth()->id()) {
            abort(403);
        }

        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->usage_count = 0;
        $newTemplate->save();

        return redirect()->route('dashboard.templates.index')
            ->with('success', 'Template duplicated successfully!');
    }
}
