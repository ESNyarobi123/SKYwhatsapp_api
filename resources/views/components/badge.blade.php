@props(['variant' => 'default'])

@php
$classes = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';

$variantClasses = [
    'default' => 'bg-white/10 text-white',
    'success' => 'bg-[#00D9A5]/20 text-[#00D9A5]',
    'error' => 'bg-[#EA3943]/20 text-[#EA3943]',
    'warning' => 'bg-[#FFB800]/20 text-[#FFB800]',
    'gold' => 'bg-[#FCD535]/20 text-[#FCD535]',
];
@endphp

<span {{ $attributes->merge(['class' => $classes . ' ' . $variantClasses[$variant]]) }}>
    {{ $slot }}
</span>
