@component('mail::message')
# Welcome to {{ config('app.name') }}

Hi there,

Thanks for joining {{ config('app.name') }}.

To continue using all features, there’s one final step waiting for you.
You can review it anytime by visiting your account page.

This message is for information purposes only.

@component('mail::button', ['url' => $url])
Open my account
@endcomponent

You are receiving this email because an account was created using this address.
No personal information is requested by email.

&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
@endcomponent
