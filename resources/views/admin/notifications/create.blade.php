@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Send Notification</h1>
                <p class="text-white/70">Send a notification to all users</p>
            </div>
            <a href="{{ route('admin.dashboard') }}">
                <x-button variant="outline" size="md">Back to Dashboard</x-button>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-[#EA3943] mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-[#EA3943] font-semibold mb-1">Validation Errors</h4>
                    <ul class="text-[#EA3943] text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <x-card>
        <form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-white/90 mb-2">
                    Title <span class="text-[#EA3943]">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}" 
                    required 
                    maxlength="255"
                    class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    placeholder="Enter notification title"
                >
                @error('title')
                    <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-white/90 mb-2">
                    Message <span class="text-[#EA3943]">*</span>
                </label>
                <textarea 
                    id="message" 
                    name="message" 
                    required 
                    maxlength="2000"
                    rows="5"
                    class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] resize-none"
                    placeholder="Enter notification message"
                >{{ old('message') }}</textarea>
                <p class="mt-1 text-xs text-white/50">Maximum 2000 characters</p>
                @error('message')
                    <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-white/90 mb-2">
                        Type <span class="text-[#EA3943]">*</span>
                    </label>
                    <select 
                        id="type" 
                        name="type" 
                        required 
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    >
                        <option value="admin_message" {{ old('type') === 'admin_message' ? 'selected' : '' }}>Admin Message</option>
                        <option value="info" {{ old('type') === 'info' ? 'selected' : '' }}>Information</option>
                        <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>Success</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-white/90 mb-2">
                        Priority
                    </label>
                    <select 
                        id="priority" 
                        name="priority" 
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    >
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="icon" class="block text-sm font-medium text-white/90 mb-2">
                    Icon (Optional)
                </label>
                <input 
                    type="text" 
                    id="icon" 
                    name="icon" 
                    value="{{ old('icon') }}" 
                    maxlength="10"
                    class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    placeholder="🔔 (emoji or icon)"
                >
                <p class="mt-1 text-xs text-white/50">Enter an emoji or icon (e.g., 🔔, 📢, ✅)</p>
                @error('icon')
                    <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="action_url" class="block text-sm font-medium text-white/90 mb-2">
                    Action URL (Optional)
                </label>
                <input 
                    type="url" 
                    id="action_url" 
                    name="action_url" 
                    value="{{ old('action_url') }}" 
                    maxlength="500"
                    class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    placeholder="https://example.com/page"
                >
                <p class="mt-1 text-xs text-white/50">URL to redirect users when they click the action button</p>
                @error('action_url')
                    <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="action_text" class="block text-sm font-medium text-white/90 mb-2">
                    Action Button Text (Optional)
                </label>
                <input 
                    type="text" 
                    id="action_text" 
                    name="action_text" 
                    value="{{ old('action_text') }}" 
                    maxlength="50"
                    class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    placeholder="View Details"
                >
                <p class="mt-1 text-xs text-white/50">Text for the action button (e.g., "View Details", "Learn More")</p>
                @error('action_text')
                    <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                <button type="submit" class="px-6 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                    Send to All Users
                </button>
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 border border-white/20 text-white rounded-lg hover:bg-white/10 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
