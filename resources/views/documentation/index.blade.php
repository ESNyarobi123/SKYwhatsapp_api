@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">API Documentation</h1>
            <p class="text-white/70">Official guide to integrating with our WhatsApp API</p>
        </div>
        @if($docUrl)
            <a href="{{ $docUrl }}" download class="inline-flex items-center justify-center px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-medium rounded-lg hover:bg-[#F0C420] transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF
            </a>
        @endif
    </div>

    <x-card class="min-h-[600px] flex flex-col">
        @if($docUrl)
            <div class="flex-1 w-full h-full rounded-lg overflow-hidden bg-white">
                <iframe src="{{ $docUrl }}" class="w-full h-[800px]" frameborder="0">
                    <div class="flex flex-col items-center justify-center h-full text-gray-500">
                        <p class="mb-4">Your browser does not support PDF embedding.</p>
                        <a href="{{ $docUrl }}" class="text-[#00D9A5] hover:underline">Click here to download the PDF</a>
                    </div>
                </iframe>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Documentation Not Available</h3>
                <p class="text-white/50 max-w-md">The API documentation has not been uploaded yet. Please check back later or contact support.</p>
            </div>
        @endif
    </x-card>
</div>
@endsection
