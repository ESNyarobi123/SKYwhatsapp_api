@props(['value', 'label', 'icon' => null, 'trend' => null])

<div class="bg-[#252525] rounded-lg border border-white/5 p-6 transition-all duration-200 hover:border-[#FCD535]/30">
    @if($icon)
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-[#FCD535]/10 rounded-lg">
                {!! $icon !!}
            </div>
            @if($trend)
                <span class="text-sm {{ $trend > 0 ? 'text-[#00D9A5]' : 'text-[#EA3943]' }}">
                    {{ $trend > 0 ? '+' : '' }}{{ $trend }}%
                </span>
            @endif
        </div>
    @endif
    
    <div class="mb-1">
        <p class="text-3xl font-bold text-[#FCD535]">{{ $value }}</p>
    </div>
    
    <p class="text-sm text-white/70">{{ $label }}</p>
</div>
