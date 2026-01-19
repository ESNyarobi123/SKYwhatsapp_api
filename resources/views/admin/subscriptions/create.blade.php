@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Create Subscription</h1>
        <p class="text-white/70">Create a new subscription for a user</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.subscriptions.store') }}" class="space-y-6">
            @csrf

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
                    <label for="user_id" class="block text-sm font-medium text-white/90 mb-2">User *</label>
                    <select id="user_id" name="user_id" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="package_id" class="block text-sm font-medium text-white/90 mb-2">Package (Optional)</label>
                    <select id="package_id" name="package_id" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                        <option value="">No Package</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }} data-price="{{ $package->price }}" data-duration="{{ $package->duration_days }}">{{ $package->name }} - {{ number_format($package->price, 2) }} {{ $package->currency }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="plan_name" class="block text-sm font-medium text-white/90 mb-2">Plan Name *</label>
                    <input type="text" id="plan_name" name="plan_name" value="{{ old('plan_name') }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-white/90 mb-2">Amount *</label>
                    <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" required min="0" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-medium text-white/90 mb-2">Expires At *</label>
                    <input type="datetime-local" id="expires_at" name="expires_at" value="{{ old('expires_at') }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="payment_provider" class="block text-sm font-medium text-white/90 mb-2">Payment Provider</label>
                    <select id="payment_provider" name="payment_provider" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                        <option value="">None</option>
                        <option value="mpesa" {{ old('payment_provider') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="tigopesa" {{ old('payment_provider') === 'tigopesa' ? 'selected' : '' }}>TigoPesa</option>
                        <option value="airtelmoney" {{ old('payment_provider') === 'airtelmoney' ? 'selected' : '' }}>AirtelMoney</option>
                        <option value="stripe" {{ old('payment_provider') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                <x-button type="submit" variant="primary" size="md">Create Subscription</x-button>
                <a href="{{ route('admin.subscriptions.index') }}">
                    <x-button type="button" variant="outline" size="md">Cancel</x-button>
                </a>
            </div>
        </form>
    </x-card>
</div>

@push('scripts')
<script>
document.getElementById('package_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        const price = selectedOption.dataset.price;
        const duration = selectedOption.dataset.duration;
        const expiresAt = new Date();
        expiresAt.setDate(expiresAt.getDate() + parseInt(duration));
        
        document.getElementById('amount').value = price;
        document.getElementById('expires_at').value = expiresAt.toISOString().slice(0, 16);
    }
});
</script>
@endpush
@endsection
