@props(['hover' => false])

@php
$classes = 'bg-[#252525] rounded-lg border border-white/5 p-6';
if ($hover) {
    $classes .= ' transition-all duration-200 hover:border-[#FCD535]/30 hover:shadow-lg hover:shadow-[#FCD535]/10';
}
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
