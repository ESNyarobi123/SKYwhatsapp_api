@props(['type' => 'button', 'variant' => 'primary', 'size' => 'md'])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#1A1A1A] disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = [
    'primary' => 'bg-[#FCD535] text-[#1A1A1A] hover:bg-[#F0C420] focus:ring-[#FCD535]',
    'secondary' => 'border border-[#FCD535] text-[#FCD535] hover:bg-[#FCD535] hover:text-[#1A1A1A] focus:ring-[#FCD535]',
    'outline' => 'border border-white/20 text-white hover:bg-white/10 focus:ring-white/50',
    'danger' => 'bg-[#EA3943] text-white hover:bg-[#D1323A] focus:ring-[#EA3943]',
    'warning' => 'bg-[#FFB800] text-[#1A1A1A] hover:bg-[#E5A500] focus:ring-[#FFB800]',
];

$sizeClasses = [
    'sm' => 'px-4 py-2 text-sm',
    'md' => 'px-6 py-3 text-base',
    'lg' => 'px-8 py-4 text-lg',
];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size]]) }}
>
    {{ $slot }}
</button>
