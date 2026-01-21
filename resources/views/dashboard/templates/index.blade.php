@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-[#10B981] to-[#059669] rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                    </svg>
                </div>
                Message Templates
            </h1>
            <p class="text-white/60 mt-1">Create and manage reusable message templates</p>
        </div>
        
        <button onclick="openCreateModal()" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Create Template
        </button>
    </div>

    <!-- Category Filter -->
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('dashboard.templates.index') }}" 
           class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ !$category ? 'bg-[#FCD535] text-[#1A1A1A]' : 'bg-[#252525] text-white/70 hover:text-white border border-white/10' }}">
            All
        </a>
        @foreach($categories as $key => $cat)
            <a href="{{ route('dashboard.templates.index', ['category' => $key]) }}" 
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all flex items-center gap-2 {{ $category === $key ? 'bg-[#FCD535] text-[#1A1A1A]' : 'bg-[#252525] text-white/70 hover:text-white border border-white/10' }}">
                <span>{{ $cat['icon'] }}</span>
                {{ $cat['name'] }}
            </a>
        @endforeach
    </div>

    <!-- Templates Grid -->
    @if($templates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $template)
                <div class="bg-[#252525] border border-white/10 rounded-2xl p-5 hover:border-[#FCD535]/30 transition-all group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background: {{ $categories[$template->category]['color'] ?? '#6B7280' }}20">
                                {{ $categories[$template->category]['icon'] ?? '📝' }}
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">{{ $template->name }}</h3>
                                <p class="text-white/50 text-xs">{{ $categories[$template->category]['name'] ?? 'General' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openEditModal({{ $template->toJson() }})" class="p-2 text-white/50 hover:text-[#FCD535] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <form action="{{ route('dashboard.templates.duplicate', $template) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-white/50 hover:text-[#8B5CF6] transition-colors" title="Duplicate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </form>
                            <form action="{{ route('dashboard.templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Delete this template?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-white/50 hover:text-[#EF4444] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="bg-[#1A1A1A] rounded-xl p-3 mb-3">
                        <p class="text-white/70 text-sm line-clamp-3">{{ $template->content }}</p>
                    </div>
                    
                    @if(count($template->variables ?? []) > 0)
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($template->variables as $var)
                                <span class="px-2 py-1 bg-[#8B5CF6]/20 text-[#8B5CF6] text-xs rounded-lg font-mono">&#123;&#123;{{ $var }}&#125;&#125;</span>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="flex items-center justify-between text-xs text-white/50">
                        <span>Used {{ number_format($template->usage_count) }} times</span>
                        <span>{{ $template->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $templates->links() }}
        </div>
    @else
        <div class="bg-[#252525] border border-white/10 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 bg-[#10B981]/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z" />
                </svg>
            </div>
            <h3 class="text-white font-bold text-lg mb-2">No Templates Yet</h3>
            <p class="text-white/60 mb-4">Create your first message template to save time</p>
            <button onclick="openCreateModal()" class="px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all">
                Create Template
            </button>
        </div>
    @endif
</div>

<!-- Create/Edit Modal -->
<div id="templateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-[#252525] border border-white/10 rounded-2xl w-full max-w-lg">
        <div class="p-6 border-b border-white/10">
            <h3 id="modalTitle" class="text-xl font-bold text-white">Create Template</h3>
        </div>
        
        <form id="templateForm" method="POST" action="{{ route('dashboard.templates.store') }}" class="p-6 space-y-4">
            @csrf
            <div id="methodField"></div>
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Template Name</label>
                <input type="text" name="name" id="templateName" required
                       class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] focus:ring-1 focus:ring-[#FCD535] outline-none transition-all"
                       placeholder="e.g., Welcome Message">
            </div>
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Category</label>
                <select name="category" id="templateCategory" required
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] focus:ring-1 focus:ring-[#FCD535] outline-none transition-all">
                    @foreach($categories as $key => $cat)
                        <option value="{{ $key }}">{{ $cat['icon'] }} {{ $cat['name'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-white/70 text-sm mb-2">Message Content</label>
                <textarea name="content" id="templateContent" required rows="4"
                          class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-xl text-white focus:border-[#FCD535] focus:ring-1 focus:ring-[#FCD535] outline-none transition-all resize-none"
                          placeholder="Hello &#123;&#123;name&#125;&#125;! Thank you for contacting us..."></textarea>
                <p class="text-white/50 text-xs mt-1">Use &#123;&#123;variable&#125;&#125; for dynamic content</p>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-3 bg-white/10 text-white rounded-xl hover:bg-white/20 transition-all">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-xl hover:bg-[#F0C420] transition-all">
                    Save Template
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const modal = document.getElementById('templateModal');
const form = document.getElementById('templateForm');

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Create Template';
    form.action = '{{ route('dashboard.templates.store') }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('templateName').value = '';
    document.getElementById('templateCategory').value = 'general';
    document.getElementById('templateContent').value = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function openEditModal(template) {
    document.getElementById('modalTitle').textContent = 'Edit Template';
    form.action = '/dashboard/templates/' + template.id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    document.getElementById('templateName').value = template.name;
    document.getElementById('templateCategory').value = template.category;
    document.getElementById('templateContent').value = template.content;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

modal.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
});
</script>
@endpush
@endsection
