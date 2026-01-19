@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Edit Subscription</h1>
        <p class="text-white/70">Update subscription details</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4">
                    <ul class="text-sm text-[#EA3943] space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="package_id" class="block text-sm font-medium text-white/90 mb-2">Package</label>
                    <select id="package_id" name="package_id" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                        <option value="">No Package</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ old('package_id', $subscription->package_id) == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="plan_name" class="block text-sm font-medium text-white/90 mb-2">Plan Name</label>
                    <input type="text" id="plan_name" name="plan_name" value="{{ old('plan_name', $subscription->plan_name) }}" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-white/90 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                        <option value="active" {{ old('status', $subscription->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ old('status', $subscription->status) === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ old('status', $subscription->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-white/90 mb-2">Amount</label>
                    <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount', $subscription->amount) }}" min="0" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-medium text-white/90 mb-2">Expires At</label>
                    <input type="datetime-local" id="expires_at" name="expires_at" value="{{ old('expires_at', $subscription->expires_at->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                <x-button type="submit" variant="primary" size="md">Update Subscription</x-button>
                <a href="{{ route('admin.subscriptions.index') }}">
                    <x-button type="button" variant="outline" size="md">Cancel</x-button>
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
