@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Bot Builder</h1>
            <p class="text-white/70">Create smart auto-reply rules for your WhatsApp instances.</p>
        </div>
        <button onclick="openCreateModal()" class="inline-flex items-center justify-center px-6 py-3 bg-[#FCD535] text-[#1A1A1A] font-bold rounded-xl hover:bg-[#F0C420] transition-all transform hover:scale-105 shadow-lg shadow-[#FCD535]/20">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New Rule
        </button>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/10 border border-[#00D9A5]/20 rounded-xl p-4 flex items-center space-x-3 text-[#00D9A5]">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-[#EA3943]/10 border border-[#EA3943]/20 rounded-xl p-4">
            <div class="flex items-center space-x-3 text-[#EA3943] mb-2">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-semibold">Something went wrong</span>
            </div>
            <ul class="list-disc list-inside text-sm text-[#EA3943]/80 ml-9">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Rules Grid -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($botReplies as $reply)
            <div class="group bg-[#252525] border border-white/5 rounded-2xl p-6 hover:border-[#FCD535]/30 transition-all duration-300 relative overflow-hidden">
                <!-- Hover Glow -->
                <div class="absolute inset-0 bg-gradient-to-br from-[#FCD535]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                
                <div class="relative flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 bg-[#FCD535]/10 text-[#FCD535] text-sm font-mono font-semibold rounded-lg border border-[#FCD535]/20">
                                {{ $reply->keyword }}
                            </span>
                            <span class="text-xs text-white/40 uppercase tracking-wider font-medium px-2 py-1 bg-white/5 rounded">
                                {{ $reply->match_type }} Match
                            </span>
                            @if(!$reply->is_active)
                                <span class="text-xs text-[#EA3943] bg-[#EA3943]/10 px-2 py-1 rounded font-medium">Inactive</span>
                            @endif
                        </div>
                        
                        <div class="bg-[#1A1A1A] rounded-xl p-4 border border-white/5 mb-4">
                            <p class="text-white/80 text-sm whitespace-pre-wrap font-mono">{{ Str::limit($reply->reply_content, 150) }}</p>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-white/40">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>Instance: {{ $reply->instance->name }}</span>
                        </div>
                    </div>

                    <div class="flex md:flex-col gap-2">
                        <button onclick="openEditModal({{ $reply->id }}, '{{ $reply->keyword }}', '{{ $reply->match_type }}', `{{ $reply->reply_content }}`, {{ $reply->is_active }}, {{ $reply->instance_id }})" 
                                class="p-2 text-white/50 hover:text-white hover:bg-white/10 rounded-lg transition-colors" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <form action="{{ route('dashboard.bot.destroy', $reply) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this rule?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-[#EA3943]/70 hover:text-[#EA3943] hover:bg-[#EA3943]/10 rounded-lg transition-colors" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-[#252525] rounded-2xl border border-white/5 border-dashed">
                <div class="w-20 h-20 bg-[#FCD535]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No Rules Yet</h3>
                <p class="text-white/50 max-w-md mx-auto mb-8">Create your first auto-reply rule to start automating your customer conversations.</p>
                <button onclick="openCreateModal()" class="inline-flex items-center justify-center px-6 py-3 bg-[#FCD535] text-[#1A1A1A] font-bold rounded-xl hover:bg-[#F0C420] transition-colors">
                    Create First Rule
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Modern Modal -->
<div id="botModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/90 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-[#252525] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-white/10">
                <form id="botForm" method="POST" action="{{ route('dashboard.bot.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="px-6 py-6 border-b border-white/5 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white" id="modalTitle">Add New Rule</h3>
                        <button type="button" onclick="closeModal()" class="text-white/50 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="px-6 py-6 space-y-6">
                        <!-- Instance Select -->
                        <div id="instanceSelectWrapper">
                            <label for="instance_id" class="block text-sm font-medium text-white/90 mb-2">Select Instance</label>
                            <div class="relative">
                                <select name="instance_id" id="instance_id" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535] appearance-none">
                                    @foreach($instances as $instance)
                                        <option value="{{ $instance->id }}">{{ $instance->name }} ({{ $instance->phone_number }})</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-white/50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Keyword Input -->
                        <div>
                            <label for="keyword" class="block text-sm font-medium text-white/90 mb-2">Trigger Keyword</label>
                            <input type="text" name="keyword" id="keyword" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="e.g., Price, Hello, Support">
                            <p class="mt-2 text-xs text-white/50">The word or phrase that triggers this reply.</p>
                        </div>

                        <!-- Match Type -->
                        <div>
                            <label class="block text-sm font-medium text-white/90 mb-2">Match Type</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="match_type" value="exact" class="peer sr-only" checked>
                                    <div class="p-3 rounded-xl border border-white/10 bg-[#1A1A1A] peer-checked:border-[#FCD535] peer-checked:bg-[#FCD535]/10 transition-all text-center">
                                        <span class="block text-sm font-medium text-white peer-checked:text-[#FCD535]">Exact Match</span>
                                        <span class="block text-xs text-white/50 mt-1">Must match exactly</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="match_type" value="contains" class="peer sr-only">
                                    <div class="p-3 rounded-xl border border-white/10 bg-[#1A1A1A] peer-checked:border-[#FCD535] peer-checked:bg-[#FCD535]/10 transition-all text-center">
                                        <span class="block text-sm font-medium text-white peer-checked:text-[#FCD535]">Contains</span>
                                        <span class="block text-xs text-white/50 mt-1">Part of sentence</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Reply Content -->
                        <div>
                            <label for="reply_content" class="block text-sm font-medium text-white/90 mb-2">Reply Message</label>
                            <textarea name="reply_content" id="reply_content" rows="5" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="Type your auto-reply message here..."></textarea>
                        </div>

                        <!-- Active Status (Edit only) -->
                        <div id="activeStatusWrapper" class="hidden">
                            <label class="flex items-center space-x-3 cursor-pointer p-3 rounded-xl bg-[#1A1A1A] border border-white/10">
                                <div class="relative inline-block w-10 h-6 align-middle select-none transition duration-200 ease-in">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"/>
                                    <label for="is_active" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                                </div>
                                <span class="text-sm font-medium text-white">Rule Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-[#1A1A1A] border-t border-white/5 flex flex-row-reverse gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-[#FCD535] text-[#1A1A1A] font-bold rounded-xl hover:bg-[#F0C420] transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FCD535] focus:ring-offset-[#1A1A1A]">
                            Save Rule
                        </button>
                        <button type="button" onclick="closeModal()" class="px-6 py-2.5 bg-transparent border border-white/10 text-white font-medium rounded-xl hover:bg-white/5 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .toggle-checkbox:checked {
        right: 0;
        border-color: #FCD535;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #FCD535;
    }
    .toggle-checkbox {
        right: 0;
        z-index: 1;
        border-color: #4B5563;
        transition: all 0.3s;
    }
    .toggle-label {
        width: 100%;
        height: 100%;
        background-color: #374151;
        transition: all 0.3s;
    }
</style>

<script>
    function openCreateModal() {
        const modal = document.getElementById('botModal');
        modal.classList.remove('hidden');
        
        // Reset Form
        document.getElementById('modalTitle').innerText = 'Add New Rule';
        document.getElementById('botForm').action = "{{ route('dashboard.bot.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('instanceSelectWrapper').classList.remove('hidden');
        document.getElementById('activeStatusWrapper').classList.add('hidden');
        
        document.getElementById('keyword').value = '';
        document.getElementById('reply_content').value = '';
        
        // Reset radio buttons
        const radios = document.getElementsByName('match_type');
        for(let i = 0; i < radios.length; i++) {
            if(radios[i].value === 'exact') radios[i].checked = true;
        }
    }

    function openEditModal(id, keyword, matchType, replyContent, isActive, instanceId) {
        const modal = document.getElementById('botModal');
        modal.classList.remove('hidden');
        
        document.getElementById('modalTitle').innerText = 'Edit Rule';
        document.getElementById('botForm').action = `/dashboard/bot/${id}`;
        document.getElementById('formMethod').value = 'PUT';
        
        // Hide instance select as it shouldn't be changed during edit to avoid confusion
        // Or we can disable it. Let's hide it for simplicity as per previous logic.
        document.getElementById('instanceSelectWrapper').classList.add('hidden');
        
        document.getElementById('activeStatusWrapper').classList.remove('hidden');

        document.getElementById('keyword').value = keyword;
        document.getElementById('reply_content').value = replyContent;
        document.getElementById('is_active').checked = isActive;
        
        const radios = document.getElementsByName('match_type');
        for(let i = 0; i < radios.length; i++) {
            if(radios[i].value === matchType) radios[i].checked = true;
        }
    }

    function closeModal() {
        document.getElementById('botModal').classList.add('hidden');
    }

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });
</script>
@endsection
