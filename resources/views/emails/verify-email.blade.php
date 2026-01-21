@component('mail::message')
# Verify Your Email Address

Hello!

Please click the button below to verify your email address and activate your account.

@component('mail::button', ['url' => $url])
Verify Email Address
@endcomponent

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
