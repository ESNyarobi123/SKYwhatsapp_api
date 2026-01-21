<?php

namespace App\Http\Controllers;

use App\Models\BotReply;
use App\Models\Instance;
use Illuminate\Http\Request;

class BotReplyController extends Controller
{
    /**
     * Check if user is authorized to perform action on bot reply.
     */
    private function authorizeBotAction($user, $permission, $botReply = null)
    {
        // 1. Check direct ownership (if reply provided)
        if ($botReply && $botReply->instance->user_id === $user->id) {
            return true;
        }

        // 2. Check team membership
        if ($user->current_team_id) {
            $team = $user->currentTeam;
            
            // If reply provided, it must belong to team owner
            if ($botReply && $team && $botReply->instance->user_id !== $team->owner_id) {
                return false;
            }

            // Check if user has permission in this team
            if ($team) {
                $member = $team->members()->where('user_id', $user->id)->first();
                if ($member && $member->hasPermission($permission)) {
                    return true;
                }
            }
        }

        // If no team and no direct ownership (or reply not provided but checking general permission)
        if (!$botReply && !$user->current_team_id) {
            return true;
        }

        return false;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        // Check permission
        if (!$this->authorizeBotAction($user, 'bot.view')) {
            abort(403, 'Unauthorized');
        }

        $instances = $owner->instances()->get();
        
        // Filter by instance if provided, otherwise show all owner's bot replies
        $query = BotReply::whereHas('instance', function ($q) use ($owner) {
            $q->where('user_id', $owner->id);
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
        $user = $request->user();

        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        // Check permission
        if (!$this->authorizeBotAction($user, 'bot.edit')) {
            return back()->withErrors(['error' => 'You do not have permission to create bot rules.']);
        }

        $request->validate([
            'instance_id' => 'required|exists:instances,id',
            'keyword' => 'required|string|max:255',
            'reply_content' => 'required|string',
            'match_type' => 'required|in:exact,contains',
        ]);

        // Verify ownership (must belong to OWNER)
        $instance = Instance::where('id', $request->instance_id)
            ->where('user_id', $owner->id)
            ->firstOrFail();

        // Check Plan Limits (check OWNER's plan)
        if ($owner->hasActiveSubscription()) {
            $package = $owner->activeSubscription->package;
            $limit = $package->getFeatureLimit('bot_rules');
            
            if ($limit !== null) {
                $currentCount = BotReply::whereHas('instance', function ($q) use ($owner) {
                    $q->where('user_id', $owner->id);
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
        $user = $request->user();
        
        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        // Verify ownership (must belong to OWNER)
        if ($botReply->instance->user_id !== $owner->id) {
            abort(403);
        }

        // Check permission
        if (!$this->authorizeBotAction($user, 'bot.edit', $botReply)) {
             return back()->withErrors(['error' => 'You do not have permission to edit bot rules.']);
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
        $user = $request->user();
        
        // Determine owner (Team Owner or Self)
        $owner = $user;
        if ($user->current_team_id && $user->currentTeam) {
            $owner = $user->currentTeam->owner;
        }

        // Verify ownership (must belong to OWNER)
        if ($botReply->instance->user_id !== $owner->id) {
            abort(403);
        }

        // Check permission
        if (!$this->authorizeBotAction($user, 'bot.edit', $botReply)) {
             return back()->withErrors(['error' => 'You do not have permission to delete bot rules.']);
        }

        $botReply->delete();

        return redirect()->route('dashboard.bot.index')->with('success', 'Bot reply deleted successfully.');
    }
}
