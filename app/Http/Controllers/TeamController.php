<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamInvitationMail;

class TeamController extends Controller
{
    /**
     * Display team management page.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user's own team or team they're part of
        $ownedTeam = Team::where('owner_id', $user->id)->first();
        $memberOf = TeamMember::where('user_id', $user->id)
            ->with('team.owner', 'team.members.user')
            ->get();

        // Pending invitations for this user
        $pendingInvitations = TeamInvitation::pending()
            ->forEmail($user->email)
            ->with('team', 'inviter')
            ->get();

        $roles = TeamMember::getRoles();

        return view('dashboard.team.index', compact('ownedTeam', 'memberOf', 'pendingInvitations', 'roles'));
    }

    /**
     * Create a new team.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Check if user already owns a team
        if (Team::where('owner_id', $user->id)->exists()) {
            return back()->withErrors(['error' => 'You already own a team.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => $user->id,
        ]);

        // Add owner as a member with owner role
        $team->addMember($user, 'owner');

        // Set as current team
        $user->update(['current_team_id' => $team->id]);

        return redirect()->route('dashboard.team.index')
            ->with('success', 'Team created successfully!');
    }

    /**
     * Update team details.
     */
    public function update(Request $request, Team $team)
    {
        // Ensure user is owner
        if (!$team->isOwner(auth()->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team->update($validated);

        return redirect()->route('dashboard.team.index')
            ->with('success', 'Team updated successfully!');
    }

    /**
     * Send team invitation.
     */
    public function invite(Request $request, Team $team)
    {
        $user = auth()->user();

        // Check permissions
        $member = $team->members()->where('user_id', $user->id)->first();
        if (!$member || !$member->hasPermission('team.invite')) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'role' => 'required|in:admin,member,viewer',
        ]);

        // Check if already a member
        $existingUser = User::where('email', $validated['email'])->first();
        if ($existingUser && $team->hasMember($existingUser)) {
            return back()->withErrors(['email' => 'This user is already a team member.']);
        }

        // Check for pending invitation
        $existingInvitation = TeamInvitation::where('team_id', $team->id)
            ->where('email', $validated['email'])
            ->pending()
            ->first();

        if ($existingInvitation) {
            return back()->withErrors(['email' => 'An invitation is already pending for this email.']);
        }

        // Create invitation
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'invited_by' => $user->id,
        ]);

        // Send invitation email
        Mail::to($validated['email'])->send(new TeamInvitationMail($invitation));

        return redirect()->route('dashboard.team.index')
            ->with('success', "Invitation sent to {$validated['email']}!");
    }

    /**
     * Show the invitation acceptance page.
     */
    public function showInvitation(TeamInvitation $invitation)
    {
        if ($invitation->hasExpired()) {
             // If user is logged in, redirect to dashboard with error
             if (auth()->check()) {
                 return redirect()->route('dashboard.team.index')
                    ->withErrors(['error' => 'This invitation has expired.']);
             }
             // If guest, show a generic expired view or just the login page with error
             return redirect()->route('login')->withErrors(['error' => 'This invitation has expired.']);
        }

        if (!auth()->check()) {
            return view('team.accept-invitation', compact('invitation'));
        }

        $user = auth()->user();

        // Verify invitation is for this user
        if ($invitation->email !== $user->email) {
            // Instead of aborting, we can show the view with a warning message
            // or just let them see the view but disable the accept button (handled in view)
            // But for security, maybe we just show the view but warn them.
             return view('team.accept-invitation', compact('invitation'))
                ->with('warning', 'You are logged in as ' . $user->email . ' but this invitation is for ' . $invitation->email . '.');
        }

        return view('team.accept-invitation', compact('invitation'));
    }

    /**
     * Accept a team invitation.
     */
    public function acceptInvitation(TeamInvitation $invitation)
    {
        $user = auth()->user();

        // Verify invitation is for this user
        if ($invitation->email !== $user->email) {
            abort(403);
        }

        if (!$invitation->isValid()) {
            return back()->withErrors(['error' => 'This invitation has expired or was already used.']);
        }

        // Accept invitation
        $invitation->accept($user);

        // Switch user's current team to the new team
        $user->update(['current_team_id' => $invitation->team_id]);

        return redirect()->route('dashboard')
            ->with('success', "You've joined {$invitation->team->name}!");
    }

    /**
     * Decline a team invitation.
     */
    public function declineInvitation(TeamInvitation $invitation)
    {
        $user = auth()->user();

        // Verify invitation is for this user
        if ($invitation->email !== $user->email) {
            abort(403);
        }

        $invitation->delete();

        return redirect()->route('dashboard.team.index')
            ->with('success', 'Invitation declined.');
    }

    /**
     * Update a team member's role.
     */
    public function updateMember(Request $request, Team $team, TeamMember $member)
    {
        $user = auth()->user();

        // Only owner can change roles
        if (!$team->isOwner($user)) {
            abort(403);
        }

        // Cannot change owner's role
        if ($member->role === 'owner') {
            return back()->withErrors(['error' => 'Cannot change owner role.']);
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,member,viewer',
        ]);

        $member->update(['role' => $validated['role']]);

        return redirect()->route('dashboard.team.index')
            ->with('success', 'Member role updated!');
    }

    /**
     * Remove a team member.
     */
    public function removeMember(Team $team, TeamMember $member)
    {
        $user = auth()->user();

        // Only owner or admin can remove members
        $currentMember = $team->members()->where('user_id', $user->id)->first();
        if (!$currentMember || !$currentMember->isAdminOrOwner()) {
            abort(403);
        }

        // Cannot remove owner
        if ($member->role === 'owner') {
            return back()->withErrors(['error' => 'Cannot remove team owner.']);
        }

        // Admin cannot remove other admins
        if ($currentMember->role === 'admin' && $member->role === 'admin') {
            return back()->withErrors(['error' => 'Admins cannot remove other admins.']);
        }

        $member->delete();

        return redirect()->route('dashboard.team.index')
            ->with('success', 'Member removed from team.');
    }

    /**
     * Leave a team.
     */
    public function leave(Team $team)
    {
        $user = auth()->user();

        // Owner cannot leave their own team
        if ($team->isOwner($user)) {
            return back()->withErrors(['error' => 'Team owners cannot leave. Transfer ownership or delete the team.']);
        }

        $team->removeMember($user);

        // Clear current team if this was it
        if ($user->current_team_id === $team->id) {
            $user->update(['current_team_id' => null]);
        }

        return redirect()->route('dashboard.team.index')
            ->with('success', "You've left {$team->name}.");
    }

    /**
     * Delete a team.
     */
    public function destroy(Team $team)
    {
        $user = auth()->user();

        // Only owner can delete
        if (!$team->isOwner($user)) {
            abort(403);
        }

        $team->delete();

        $user->update(['current_team_id' => null]);

        return redirect()->route('dashboard.team.index')
            ->with('success', 'Team deleted successfully.');
    }

    /**
     * Cancel a pending invitation.
     */
    public function cancelInvitation(Team $team, TeamInvitation $invitation)
    {
        $user = auth()->user();

        // Only team admin/owner can cancel
        $member = $team->members()->where('user_id', $user->id)->first();
        if (!$member || !$member->isAdminOrOwner()) {
            abort(403);
        }

        $invitation->delete();

        return redirect()->route('dashboard.team.index')
            ->with('success', 'Invitation cancelled.');
    }
}
