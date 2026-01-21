@component('mail::message')
# Welcome!

You have successfully created an account on {{ config('app.name') }}.

To get started with your account, please click the button below.

@component('mail::button', ['url' => $url])
Get Started
@endcomponent

If you did not create an account, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
