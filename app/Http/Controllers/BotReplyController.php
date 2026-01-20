<?php

namespace App\Http\Controllers;

use App\Models\BotReply;
use App\Models\Instance;
use Illuminate\Http\Request;

class BotReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $instances = $request->user()->instances()->get();
        
        // Filter by instance if provided, otherwise show all user's bot replies
        $query = BotReply::whereHas('instance', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        });

        if ($request->instance_id) {
            $query->where('instance_id', $request->instance_id);
        }

        $botReplies = $query->with('instance')->latest()->get();

        return view('dashboard.bot.index', compact('botReplies', 'instances'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'instance_id' => 'required|exists:instances,id',
            'keyword' => 'required|string|max:255',
            'reply_content' => 'required|string',
            'match_type' => 'required|in:exact,contains',
        ]);

        // Verify ownership
        $instance = Instance::where('id', $request->instance_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Check Plan Limits
        $user = $request->user();
        if ($user->hasActiveSubscription()) {
            $package = $user->activeSubscription->package;
            $limit = $package->getFeatureLimit('bot_rules');
            
            if ($limit !== null) {
                // Count total rules across all instances for this user
                // Or per instance? Usually per user or per package.
                // Let's assume the limit is "Total Bot Rules" per user.
                $currentCount = BotReply::whereHas('instance', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count();

                if ($currentCount >= $limit) {
                    return back()->withErrors(['error' => "You have reached your plan's limit of {$limit} auto-reply rules. Please upgrade your plan."]);
                }
            }
        }

        BotReply::create([
            'instance_id' => $instance->id,
            'keyword' => $request->keyword,
            'reply_content' => $request->reply_content,
            'match_type' => $request->match_type,
            'is_active' => true,
        ]);

        return redirect()->route('dashboard.bot.index')->with('success', 'Bot reply created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BotReply $botReply)
    {
        // Verify ownership
        if ($botReply->instance->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'keyword' => 'required|string|max:255',
            'reply_content' => 'required|string',
            'match_type' => 'required|in:exact,contains',
            'is_active' => 'boolean',
        ]);

        $botReply->update($request->only(['keyword', 'reply_content', 'match_type', 'is_active']));

        return redirect()->route('dashboard.bot.index')->with('success', 'Bot reply updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, BotReply $botReply)
    {
        // Verify ownership
        if ($botReply->instance->user_id !== $request->user()->id) {
            abort(403);
        }

        $botReply->delete();

        return redirect()->route('dashboard.bot.index')->with('success', 'Bot reply deleted successfully.');
    }
}
