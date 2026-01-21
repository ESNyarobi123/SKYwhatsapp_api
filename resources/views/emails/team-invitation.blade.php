@component('mail::message')
# You have been invited to join a team!

You have been invited to join the team **{{ $invitation->team->name }}** on {{ config('app.name') }}.

@component('mail::button', ['url' => route('team.invitation.show', $invitation->id)])
Accept Invitation
@endcomponent

If you did not expect this invitation, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
