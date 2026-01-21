<?php

namespace App\Http\Controllers;

use App\Models\BotTemplate;
use App\Models\BotReply;
use App\Models\Instance;
use Illuminate\Http\Request;

class BotTemplateController extends Controller
{
    /**
     * Display a listing of bot templates.
     */
    public function index(Request $request)
    {
        $category = $request->get('category');
        
        $templates = BotTemplate::active()
            ->when($category, fn($q) => $q->category($category))
            ->orderBy('usage_count', 'desc')
            ->get();

        $categories = BotTemplate::getCategories();
        
        $user = auth()->user();
        $instances = Instance::where('user_id', $user->id)->get();

        return view('dashboard.bot-templates.index', compact('templates', 'categories', 'category', 'instances'));
    }

    /**
     * Show details of a specific template.
     */
    public function show(BotTemplate $botTemplate)
    {
        $user = auth()->user();
        $instances = Instance::where('user_id', $user->id)->get();
        $categories = BotTemplate::getCategories();

        return view('dashboard.bot-templates.show', compact('botTemplate', 'instances', 'categories'));
    }

    /**
     * Import a bot template to an instance.
     */
    public function import(Request $request, BotTemplate $botTemplate)
    {
        $validated = $request->validate([
            'instance_id' => 'required|exists:instances,id',
            'replace_existing' => 'boolean',
        ]);

        $user = auth()->user();
        
        // Verify instance belongs to user
        $instance = Instance::where('id', $validated['instance_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Check if user has premium access for premium templates
        if ($botTemplate->is_premium) {
            $subscription = $user->activeSubscription;
            if (!$subscription || !$subscription->package) {
                return back()->withErrors(['error' => 'Premium templates require an active subscription.']);
            }
        }

        // Optionally delete existing rules
        if ($request->boolean('replace_existing')) {
            BotReply::where('instance_id', $instance->id)->delete();
        }

        // Import rules
        $rules = $botTemplate->rules ?? [];
        $imported = 0;

        foreach ($rules as $rule) {
            BotReply::create([
                'instance_id' => $instance->id,
                'keyword' => $rule['keyword'] ?? '',
                'match_type' => $rule['match_type'] ?? 'contains',
                'reply_type' => $rule['reply_type'] ?? 'text',
                'reply_content' => $rule['reply_content'] ?? '',
                'is_active' => true,
            ]);
            $imported++;
        }

        // Increment template usage
        $botTemplate->incrementUsage();

        return redirect()->route('dashboard.bot.index')
            ->with('success', "Successfully imported {$imported} bot rules from '{$botTemplate->name}' template!");
    }

    /**
     * Preview template rules.
     */
    public function preview(BotTemplate $botTemplate)
    {
        return response()->json([
            'success' => true,
            'template' => [
                'name' => $botTemplate->name,
                'description' => $botTemplate->description,
                'category' => $botTemplate->category,
                'rules_count' => $botTemplate->rules_count,
                'rules' => $botTemplate->rules,
            ],
        ]);
    }
}
