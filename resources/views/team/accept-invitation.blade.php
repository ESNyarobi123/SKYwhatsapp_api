@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Team Invitation') }}</div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3>You have been invited to join {{ $invitation->team->name }}</h3>
                        <p class="text-muted">
                            Invited by {{ $invitation->inviter->name }} ({{ $invitation->inviter->email }})
                        </p>
                        <p>
                            Role: <strong>{{ ucfirst($invitation->role) }}</strong>
                        </p>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <form method="POST" action="{{ route('dashboard.team.invitation.accept', $invitation->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                Accept Invitation
                            </button>
                        </form>

                        <form method="POST" action="{{ route('dashboard.team.invitation.decline', $invitation->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg">
                                Decline
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
