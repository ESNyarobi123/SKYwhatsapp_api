@component('mail::message')
# Hello there!

We're excited to have you on board. To ensure we can stay in touch, please confirm that this is your correct email address.

It only takes a second:

@component('mail::button', ['url' => $url])
Yes, this is my email
@endcomponent

If you didn't sign up for an account, you can safely ignore this message.

Cheers,<br>
The {{ config('app.name') }} Team
@endcomponent
