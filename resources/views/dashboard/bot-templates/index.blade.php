@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9] rounded-xl flex items-center justify-center text-xl">
                    🤖
                </div>
                Bot Templates
            </h1>
            <p class="text-white/60 mt-1">Pre-made bot templates to get you started quickly</p>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('dashboard.bot-templates.index') }}" 
           class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ !$category ? 'bg-[#FCD535] text-[#1A1A1A]' : 'bg-[#252525] text-white/70 hover:text-white border border-white/10' }}">
            All Templates
        </a>
        @foreach($categories as $key => $cat)
            <a href="{{ route('dashboard.bot-templates.index', ['category' => $key]) }}" 
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all flex items-center gap-2 {{ $category === $key ? 'text-[#1A1A1A]' : 'bg-[#252525] text-white/70 hover:text-white border border-white/10' }}"
               style="{{ $category === $key ? 'background: ' . $cat['color'] : '' }}">
                <span>{{ $cat['icon'] }}</span>
                {{ $cat['name'] }}
            </a>
        @endforeach
    </div>

    <!-- Templates Grid -->
    @if($templates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($templates as $template)
                @php
                    $catInfo = $categories[$template->category] ?? ['name' => 'Other', 'icon' => '📦', 'color' => '#6B7280'];
                @endphp
                <div class="bg-[#252525] border border-white/10 rounded-2xl overflow-hidden hover:border-{{ str_replace('#', '', $catInfo['color']) }}/30 transition-all group">
                    <!-- Header with gradient -->
                    <div class="h-24 relative" style="background: linear-gradient(135deg, {{ $catInfo['color'] }}40, {{ $catInfo['color'] }}10)">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-5xl opacity-50">{{ $template->icon }}</span>
                        </div>
                        @if($template->is_premium)
                            <div class="absolute top-3 right-3">
                                <span class="px-2 py-1 bg-[#FCD535] text-[#1A1A1A] text-xs font-bold rounded-full">PRO</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-white font-bold text-lg">{{ $template->name }}</h3>
                                <p class="text-white/50 text-xs flex items-center gap-1">
                                    <span>{{ $catInfo['icon'] }}</span>
                                    {{ $catInfo['name'] }}
                                </p>
                            </div>
                            <span class="px-2 py-1 bg-white/10 text-white/70 text-xs rounded-lg">
                                {{ $template->rules_count }} rules
                            </span>
                        </div>
                        
                        <p class="text-white/70 text-sm mb-4 line-clamp-2">{{ $template->description }}</p>
                        
                        <!-- Sample Rules Preview -->
                        <div class="bg-[#1A1A1A] rounded-xl p-3 mb-4">
                            <p class="text-white/50 text-xs mb-2">Sample Rules:</p>
                            @foreach(array_slice($template->rules ?? [], 0, 2) as $rule)
                                <div class="flex items-center gap-2 text-xs mb-1">
                                    <span class="text-[#8B5CF6]">"{{ Str::limit($rule['keyword'] ?? '', 15) }}"</span>
                                    <svg class="w-3 h-3 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                    <span class="text-white/60 truncate">{{ Str::limit($rule['reply_content'] ?? '', 20) }}</span>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-white/40 text-xs">Used {{ number_format($template->usage_count) }} times</span>
                            <button onclick="openImportModal({{ $template->id }}, '{{ $template->name }}')" 
                                    class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold text-sm rounded-xl hover:bg-[#F0C420] transition-all">
                                Use Template
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 bg-[#8B5CF6]/20 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">
                🤖
            </div>
            <h3 class="text-white font-bold text-lg mb-2">No Templates Found</h3>
            <p class="text-white/60">No templates available in this category yet</p>
        </div>
    @endif
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-[#252525] border border-white/10 rounded-2xl w-full max-w-md">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white">Import Template</h3>
            <p class="text-white/60 text-sm mt-1" id="templateName">-</p>
        </div>
        
        <form id="importForm" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Select Instance</label>
                <select name="instance_id" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] outline-none">
                    @forelse($instances as $instance)
                        <option value="{{ $instance->id }}">{{ $instance->name }}</option>
                    @empty
                        <option value="">No instances available</option>
                    @endforelse
                </select>
            </div>
            
            <div class="flex items-center gap-3 p-3 bg-[#1A1A1A] rounded-xl">
                <input type="checkbox" name="replace_existing" id="replaceExisting" class="w-4 h-4 rounded bg-[#252525] border-white/20 text-[#FCD535] focus:ring-[#FCD535]">
                <label for="replaceExisting" class="text-white/70 text-sm">Replace existing bot rules</label>
            </div>
            
            <div class="bg-[#F59E0B]/10 border border-[#F59E0B]/30 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-[#F59E0B] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-[#F59E0B] text-sm">This will add new bot rules to your selected instance. You can edit them later.</p>
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeImportModal()" class="flex-1 px-4 py-3 bg-white/10 text-white rounded-xl hover:bg-white/20 transition-all">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all" {{ $instances->isEmpty() ? 'disabled' : '' }}>
                    Import Rules
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const importModal = document.getElementById('importModal');
const importForm = document.getElementById('importForm');

function openImportModal(templateId, name) {
    document.getElementById('templateName').textContent = name;
    importForm.action = '/dashboard/bot-templates/' + templateId + '/import';
    importModal.classList.remove('hidden');
    importModal.classList.add('flex');
}

function closeImportModal() {
    importModal.classList.add('hidden');
    importModal.classList.remove('flex');
}

importModal.addEventListener('click', function(e) {
    if (e.target === importModal) closeImportModal();
});
</script>
@endpush
@endsection
