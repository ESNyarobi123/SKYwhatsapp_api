@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-[#EC4899] to-[#DB2777] rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                Team Management
            </h1>
            <p class="text-white/60 mt-1">Collaborate with your team members</p>
        </div>
        
        @if(!$ownedTeam)
            <button onclick="openCreateTeamModal()" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create Team
            </button>
        @endif
    </div>

    <!-- Pending Invitations -->
    @if($pendingInvitations->count() > 0)
        <div class="bg-gradient-to-r from-[#8B5CF6]/20 to-[#EC4899]/20 border border-[#8B5CF6]/30 rounded-2xl p-6">
            <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Pending Invitations
            </h3>
            <div class="space-y-3">
                @foreach($pendingInvitations as $invitation)
                    <div class="bg-[#252525] rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#8B5CF6]/20 rounded-full flex items-center justify-center">
                                <span class="text-[#8B5CF6] font-bold">{{ strtoupper(substr($invitation->team->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $invitation->team->name }}</p>
                                <p class="text-white/50 text-sm">Invited by {{ $invitation->inviter->name }} • Role: {{ ucfirst($invitation->role) }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('dashboard.team.invitation.decline', $invitation) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-white/10 text-white/70 rounded-lg hover:bg-white/20 transition-all text-sm">
                                    Decline
                                </button>
                            </form>
                            <form action="{{ route('dashboard.team.invitation.accept', $invitation) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-[#10B981] text-white rounded-lg hover:bg-[#059669] transition-all text-sm">
                                    Accept
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Your Team (if owner) -->
    @if($ownedTeam)
        <div class="bg-[#252525] border border-white/10 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-[#FCD535] to-[#F59E0B] rounded-xl flex items-center justify-center">
                            <span class="text-2xl font-bold text-[#1A1A1A]">{{ strtoupper(substr($ownedTeam->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $ownedTeam->name }}</h3>
                            <p class="text-white/50 text-sm">{{ $ownedTeam->members->count() }} members</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openInviteModal({{ $ownedTeam->id }})" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Invite Member
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Team Members -->
            <div class="p-6">
                <h4 class="text-white/70 text-sm font-medium mb-4 uppercase tracking-wider">Team Members</h4>
                <div class="space-y-3">
                    @foreach($ownedTeam->members as $member)
                        <div class="bg-[#1A1A1A] rounded-xl p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center" 
                                     style="background: {{ $roles[$member->role]['color'] ?? '#6B7280' }}20">
                                    <span class="font-bold" style="color: {{ $roles[$member->role]['color'] ?? '#6B7280' }}">
                                        {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $member->user->name }}</p>
                                    <p class="text-white/50 text-sm">{{ $member->user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-xs font-medium" 
                                      style="background: {{ $roles[$member->role]['color'] ?? '#6B7280' }}20; color: {{ $roles[$member->role]['color'] ?? '#6B7280' }}">
                                    {{ ucfirst($member->role) }}
                                </span>
                                @if($member->role !== 'owner')
                                    <div class="flex gap-1">
                                        <form action="{{ route('dashboard.team.member.update', [$ownedTeam, $member]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" onchange="this.form.submit()" 
                                                    class="bg-transparent border border-white/10 rounded-lg px-2 py-1 text-white/70 text-sm">
                                                @foreach(['admin', 'member', 'viewer'] as $role)
                                                    <option value="{{ $role }}" {{ $member->role === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                        <form action="{{ route('dashboard.team.member.remove', [$ownedTeam, $member]) }}" method="POST" 
                                              onsubmit="return confirm('Remove this member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-white/50 hover:text-[#EF4444] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Pending Invitations for this team -->
            @if($ownedTeam->pendingInvitations->count() > 0)
                <div class="p-6 border-t border-white/10">
                    <h4 class="text-white/70 text-sm font-medium mb-4 uppercase tracking-wider">Pending Invitations</h4>
                    <div class="space-y-2">
                        @foreach($ownedTeam->pendingInvitations as $invitation)
                            <div class="bg-[#1A1A1A] rounded-xl p-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-[#F59E0B]/20 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-white text-sm">{{ $invitation->email }}</p>
                                        <p class="text-white/50 text-xs">Role: {{ ucfirst($invitation->role) }} • Expires {{ $invitation->expires_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <form action="{{ route('dashboard.team.invitation.cancel', [$ownedTeam, $invitation]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white/50 hover:text-[#EF4444] text-sm">Cancel</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Teams You're Part Of -->
    @if($memberOf->count() > 0)
        <div>
            <h3 class="text-white font-bold mb-4">Teams You're Part Of</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($memberOf as $membership)
                    @if($membership->team->owner_id !== auth()->id())
                        <div class="bg-[#252525] border border-white/10 rounded-2xl p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-[#8B5CF6]/20 rounded-xl flex items-center justify-center">
                                        <span class="text-[#8B5CF6] font-bold text-lg">{{ strtoupper(substr($membership->team->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold">{{ $membership->team->name }}</h4>
                                        <p class="text-white/50 text-sm">Owner: {{ $membership->team->owner->name }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium" 
                                      style="background: {{ $roles[$membership->role]['color'] ?? '#6B7280' }}20; color: {{ $roles[$membership->role]['color'] ?? '#6B7280' }}">
                                    {{ ucfirst($membership->role) }}
                                </span>
                            </div>
                            <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between">
                                <span class="text-white/50 text-sm">{{ $membership->team->members->count() }} members</span>
                                <form action="{{ route('dashboard.team.leave', $membership->team) }}" method="POST" 
                                      onsubmit="return confirm('Leave this team?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[#EF4444] text-sm hover:underline">Leave Team</button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- No Teams State -->
    @if(!$ownedTeam && $memberOf->where('team.owner_id', '!=', auth()->id())->count() === 0 && $pendingInvitations->count() === 0)
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 bg-[#EC4899]/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#EC4899]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h3 class="text-white font-bold text-lg mb-2">No Team Yet</h3>
            <p class="text-white/60 mb-4">Create a team to collaborate with others</p>
            <button onclick="openCreateTeamModal()" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all">
                Create Your Team
            </button>
        </div>
    @endif
</div>

<!-- Create Team Modal -->
<div id="createTeamModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-[#252525] border border-white/10 rounded-2xl w-full max-w-md">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white">Create Team</h3>
        </div>
        
        <form action="{{ route('dashboard.team.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Team Name</label>
                <input type="text" name="name" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none" placeholder="My Awesome Team">
            </div>
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Description (optional)</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none resize-none" placeholder="What does your team do?"></textarea>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeCreateTeamModal()" class="flex-1 px-4 py-3 bg-white/10 text-white rounded-xl hover:bg-white/20 transition-all">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all">
                    Create Team
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Invite Modal -->
<div id="inviteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-[#252525] border border-white/10 rounded-2xl w-full max-w-md">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white">Invite Team Member</h3>
        </div>
        
        <form id="inviteForm" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none" placeholder="colleague@example.com">
            </div>
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Role</label>
                <select name="role" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none">
                    @foreach($roles as $key => $role)
                        <option value="{{ $key }}">{{ $role['name'] }} - {{ $role['description'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeInviteModal()" class="flex-1 px-4 py-3 bg-white/10 text-white rounded-xl hover:bg-white/20 transition-all">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all">
                    Send Invitation
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const createTeamModal = document.getElementById('createTeamModal');
const inviteModal = document.getElementById('inviteModal');
const inviteForm = document.getElementById('inviteForm');

function openCreateTeamModal() {
    createTeamModal.classList.remove('hidden');
    createTeamModal.classList.add('flex');
}

function closeCreateTeamModal() {
    createTeamModal.classList.add('hidden');
    createTeamModal.classList.remove('flex');
}

function openInviteModal(teamId) {
    inviteForm.action = '/dashboard/team/' + teamId + '/invite';
    inviteModal.classList.remove('hidden');
    inviteModal.classList.add('flex');
}

function closeInviteModal() {
    inviteModal.classList.add('hidden');
    inviteModal.classList.remove('flex');
}

createTeamModal.addEventListener('click', function(e) {
    if (e.target === createTeamModal) closeCreateTeamModal();
});

inviteModal.addEventListener('click', function(e) {
    if (e.target === inviteModal) closeInviteModal();
});
</script>
@endpush
@endsection
