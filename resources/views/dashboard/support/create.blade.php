@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Create Support Ticket</h1>
                <p class="text-white/70">Submit a new support ticket and our team will help you</p>
            </div>
            <a href="{{ route('dashboard.support.index') }}" class="px-4 py-2 border border-white/20 text-white rounded-lg hover:bg-white/10 transition-colors">
                Back to Tickets
            </a>
        </div>
    </div>

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
        <form method="POST" action="{{ route('dashboard.support.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="subject" class="block text-sm font-medium text-white/90 mb-2">
                    Subject <span class="text-[#EA3943]">*</span>
                </label>
                <input 
                    type="text" 
                    id="subject" 
                    name="subject" 
                    value="{{ old('subject') }}" 
                    required 
                    maxlength="255"
                    class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    placeholder="Brief description of your issue"
                >
                @error('subject')
                    <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-white/90 mb-2">
                    Description <span class="text-[#EA3943]">*</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    required 
                    maxlength="5000"
                    rows="8"
                    class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] resize-none"
                    placeholder="Please provide detailed information about your issue..."
                >{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-white/50">Maximum 5000 characters</p>
                @error('description')
                    <p class="mt-1 text-xs text-[#EA3943]">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-white/90 mb-2">
                        Category
                    </label>
                    <select 
                        id="category" 
                        name="category" 
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                    >
                        <option value="other" {{ old('category', 'other') === 'other' ? 'selected' : '' }}>Other</option>
                        <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                        <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Billing</option>
                        <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Account</option>
                        <option value="feature_request" {{ old('category') === 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                    </select>
                    @error('category')
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

            <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                <button type="submit" class="px-6 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                    Create Ticket
                </button>
                <a href="{{ route('dashboard.support.index') }}" class="px-6 py-3 border border-white/20 text-white rounded-lg hover:bg-white/10 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </x-card>

    {{-- WhatsApp Link Section --}}
    <x-card>
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-[#25D366]/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-white font-semibold mb-1">Need Immediate Help?</h3>
                <p class="text-white/70 text-sm mb-2">Contact us directly on WhatsApp for faster support</p>
                <a href="https://wa.me/255123456789" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 bg-[#25D366] text-white font-semibold rounded-lg hover:bg-[#20BA5A] transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Chat on WhatsApp
                </a>
            </div>
        </div>
    </x-card>
</div>
@endsection
