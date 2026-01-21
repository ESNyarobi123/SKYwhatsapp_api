@component('mail::message')
# Welcome to {{ config('app.name') }}!

Thanks for signing up. Please confirm your account to get started.

@component('mail::button', ['url' => $url])
Confirm Account
@endcomponent

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
