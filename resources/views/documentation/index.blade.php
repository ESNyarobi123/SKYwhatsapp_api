@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $apiKey = $user->apiKeys()->where('is_active', true)->first();
    $baseUrl = config('app.url') . '/api/v1';
@endphp

<div class="space-y-8">
    <!-- Modern Header with Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#3B82F6] via-[#2563EB] to-[#1D4ED8] p-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-20"></div>
        <div class="relative z-10">
            <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
                API Documentation
            </h1>
            <p class="text-white/80 text-lg">Integrate WhatsApp messaging into your applications</p>
        </div>
    </div>

    <!-- Quick Start -->
    <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-white mb-4">🚀 Quick Start</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Your API Key -->
            <div class="bg-[#1A1A1A] rounded-xl p-5 border border-white/5">
                <h3 class="text-white font-semibold mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Your API Key
                </h3>
                @if($apiKey)
                    <div class="flex items-center gap-2">
                        <code class="flex-1 bg-[#0D0D0D] px-4 py-3 rounded-lg text-[#00D9A5] font-mono text-sm overflow-x-auto">
                            {{ substr($apiKey->key, 0, 20) }}...{{ substr($apiKey->key, -8) }}
                        </code>
                        <a href="{{ route('dashboard.api-keys') }}" class="px-3 py-2 bg-[#FCD535]/20 text-[#FCD535] rounded-lg text-sm hover:bg-[#FCD535]/30 transition-colors">
                            View Full Key
                        </a>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <p class="text-white/50 text-sm">No API key found</p>
                        <a href="{{ route('dashboard.api-keys') }}" class="px-3 py-2 bg-[#FCD535] text-[#1A1A1A] font-medium rounded-lg text-sm hover:bg-[#F0C420] transition-colors">
                            Generate API Key
                        </a>
                    </div>
                @endif
            </div>
            
            <!-- Base URL -->
            <div class="bg-[#1A1A1A] rounded-xl p-5 border border-white/5">
                <h3 class="text-white font-semibold mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    Base URL
                </h3>
                <code class="block bg-[#0D0D0D] px-4 py-3 rounded-lg text-[#3B82F6] font-mono text-sm">
                    {{ $baseUrl }}
                </code>
            </div>
        </div>
    </div>

    <!-- Authentication -->
    <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-white mb-4">🔐 Authentication</h2>
        <p class="text-white/70 mb-4">Include your API key in the <code class="bg-[#1A1A1A] px-2 py-1 rounded text-[#FCD535]">Authorization</code> header with every request:</p>
        
        <div class="bg-[#0D0D0D] rounded-xl p-4 overflow-x-auto">
            <pre class="text-sm"><code class="text-white/90"><span class="text-[#FCD535]">Authorization:</span> Bearer <span class="text-[#00D9A5]">YOUR_API_KEY</span></code></pre>
        </div>
    </div>

    <!-- Send Message -->
    <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="px-3 py-1 bg-[#00D9A5]/20 text-[#00D9A5] text-sm font-bold rounded-lg">POST</span>
            <h2 class="text-2xl font-bold text-white">Send Message</h2>
        </div>
        
        <div class="mb-4">
            <code class="bg-[#1A1A1A] px-4 py-2 rounded-lg text-[#3B82F6] font-mono">{{ $baseUrl }}/messages/send</code>
        </div>
        
        <h4 class="text-white font-semibold mb-2">Parameters:</h4>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left py-2 text-white/60">Parameter</th>
                        <th class="text-left py-2 text-white/60">Type</th>
                        <th class="text-left py-2 text-white/60">Required</th>
                        <th class="text-left py-2 text-white/60">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr><td class="py-2 text-[#FCD535] font-mono">instance_id</td><td class="py-2 text-white/70">Integer</td><td class="py-2 text-[#00D9A5]">Yes</td><td class="py-2 text-white/70">Your WhatsApp instance ID</td></tr>
                    <tr><td class="py-2 text-[#FCD535] font-mono">to</td><td class="py-2 text-white/70">String</td><td class="py-2 text-[#00D9A5]">Yes</td><td class="py-2 text-white/70">Phone number with country code (e.g., 255712345678)</td></tr>
                    <tr><td class="py-2 text-[#FCD535] font-mono">body</td><td class="py-2 text-white/70">String</td><td class="py-2 text-[#00D9A5]">Yes</td><td class="py-2 text-white/70">Message content</td></tr>
                    <tr><td class="py-2 text-[#FCD535] font-mono">image</td><td class="py-2 text-white/70">File</td><td class="py-2 text-white/50">No</td><td class="py-2 text-white/70">Image to send (multipart/form-data)</td></tr>
                </tbody>
            </table>
        </div>
        
        <h4 class="text-white font-semibold mb-2">Example Request (cURL):</h4>
        <div class="bg-[#0D0D0D] rounded-xl p-4 overflow-x-auto mb-4">
            <pre class="text-sm"><code class="text-white/90">curl -X POST {{ $baseUrl }}/messages/send \
  -H "<span class="text-[#FCD535]">Authorization:</span> Bearer YOUR_API_KEY" \
  -H "<span class="text-[#FCD535]">Content-Type:</span> application/json" \
  -d '{
    "<span class="text-[#00D9A5]">instance_id</span>": 1,
    "<span class="text-[#00D9A5]">to</span>": "255712345678",
    "<span class="text-[#00D9A5]">body</span>": "Hello from API!"
  }'</code></pre>
        </div>
        
        <h4 class="text-white font-semibold mb-2">Example Response:</h4>
        <div class="bg-[#0D0D0D] rounded-xl p-4 overflow-x-auto">
            <pre class="text-sm"><code class="text-white/90">{
  "<span class="text-[#FCD535]">success</span>": true,
  "<span class="text-[#FCD535]">data</span>": {
    "<span class="text-[#00D9A5]">message</span>": {
      "id": 123,
      "status": "pending",
      "to": "255712345678"
    }
  },
  "<span class="text-[#FCD535]">message</span>": "Message queued for sending."
}</code></pre>
        </div>
    </div>

    <!-- List Instances -->
    <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="px-3 py-1 bg-[#3B82F6]/20 text-[#3B82F6] text-sm font-bold rounded-lg">GET</span>
            <h2 class="text-2xl font-bold text-white">List Instances</h2>
        </div>
        
        <div class="mb-4">
            <code class="bg-[#1A1A1A] px-4 py-2 rounded-lg text-[#3B82F6] font-mono">{{ $baseUrl }}/instances</code>
        </div>
        
        <h4 class="text-white font-semibold mb-2">Example Request:</h4>
        <div class="bg-[#0D0D0D] rounded-xl p-4 overflow-x-auto">
            <pre class="text-sm"><code class="text-white/90">curl {{ $baseUrl }}/instances \
  -H "<span class="text-[#FCD535]">Authorization:</span> Bearer YOUR_API_KEY"</code></pre>
        </div>
    </div>

    <!-- Get Messages -->
    <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="px-3 py-1 bg-[#3B82F6]/20 text-[#3B82F6] text-sm font-bold rounded-lg">GET</span>
            <h2 class="text-2xl font-bold text-white">Get Messages</h2>
        </div>
        
        <div class="mb-4">
            <code class="bg-[#1A1A1A] px-4 py-2 rounded-lg text-[#3B82F6] font-mono">{{ $baseUrl }}/messages</code>
        </div>
        
        <h4 class="text-white font-semibold mb-2">Query Parameters:</h4>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left py-2 text-white/60">Parameter</th>
                        <th class="text-left py-2 text-white/60">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr><td class="py-2 text-[#FCD535] font-mono">instance_id</td><td class="py-2 text-white/70">Filter by instance</td></tr>
                    <tr><td class="py-2 text-[#FCD535] font-mono">direction</td><td class="py-2 text-white/70">Filter by <code class="bg-[#0D0D0D] px-1 rounded">inbound</code> or <code class="bg-[#0D0D0D] px-1 rounded">outbound</code></td></tr>
                    <tr><td class="py-2 text-[#FCD535] font-mono">per_page</td><td class="py-2 text-white/70">Number of results per page (default: 50)</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Webhooks -->
    <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="px-3 py-1 bg-[#8B5CF6]/20 text-[#8B5CF6] text-sm font-bold rounded-lg">WEBHOOKS</span>
            <h2 class="text-2xl font-bold text-white">Real-time Events</h2>
        </div>
        
        <p class="text-white/70 mb-4">Configure webhooks to receive real-time notifications for incoming messages and status updates.</p>
        
        <div class="mb-4">
            <code class="bg-[#1A1A1A] px-4 py-2 rounded-lg text-[#3B82F6] font-mono">POST {{ $baseUrl }}/webhooks</code>
        </div>
        
        <h4 class="text-white font-semibold mb-2">Available Events:</h4>
        <ul class="list-disc list-inside text-white/70 mb-4 space-y-1">
            <li><code class="bg-[#0D0D0D] px-2 rounded text-[#00D9A5]">message.inbound</code> - New message received</li>
            <li><code class="bg-[#0D0D0D] px-2 rounded text-[#00D9A5]">message.status</code> - Message status changed (sent, delivered, read)</li>
            <li><code class="bg-[#0D0D0D] px-2 rounded text-[#00D9A5]">instance.connected</code> - WhatsApp connected</li>
        </ul>
        
        <h4 class="text-white font-semibold mb-2">Webhook Payload Example:</h4>
        <div class="bg-[#0D0D0D] rounded-xl p-4 overflow-x-auto">
            <pre class="text-sm"><code class="text-white/90">{
  "<span class="text-[#FCD535]">event</span>": "message.inbound",
  "<span class="text-[#FCD535]">data</span>": {
    "<span class="text-[#00D9A5]">from</span>": "255712345678",
    "<span class="text-[#00D9A5]">body</span>": "Hello!",
    "<span class="text-[#00D9A5]">timestamp</span>": 1700000000
  }
}</code></pre>
        </div>
    </div>

    <!-- Error Codes -->
    <div class="bg-[#252525] border border-white/10 rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-white mb-4">⚠️ Error Codes</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left py-2 text-white/60">Code</th>
                        <th class="text-left py-2 text-white/60">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr><td class="py-2 text-red-400 font-mono">401</td><td class="py-2 text-white/70">Unauthorized - Invalid or missing API key</td></tr>
                    <tr><td class="py-2 text-red-400 font-mono">403</td><td class="py-2 text-white/70">Forbidden - Subscription expired or limit reached</td></tr>
                    <tr><td class="py-2 text-red-400 font-mono">404</td><td class="py-2 text-white/70">Not Found - Resource doesn't exist</td></tr>
                    <tr><td class="py-2 text-red-400 font-mono">422</td><td class="py-2 text-white/70">Validation Error - Invalid parameters</td></tr>
                    <tr><td class="py-2 text-red-400 font-mono">429</td><td class="py-2 text-white/70">Rate Limit - Too many requests</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Need Help -->
    <div class="bg-gradient-to-br from-[#252525] to-[#1A1A1A] border border-[#FCD535]/30 rounded-2xl p-6 text-center">
        <h3 class="text-xl font-bold text-white mb-2">Need Help?</h3>
        <p class="text-white/70 mb-4">Check the examples above or contact our support team</p>
        <a href="{{ route('dashboard.support.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#FCD535] text-[#1A1A1A] font-bold rounded-xl hover:bg-[#F0C420] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            Contact Support
        </a>
    </div>
</div>
@endsection
