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

                    @if(session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @auth
                        @if(auth()->user()->email === $invitation->email)
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
                        @else
                            <div class="alert alert-danger text-center">
                                This invitation is for <strong>{{ $invitation->email }}</strong>, but you are logged in as <strong>{{ auth()->user()->email }}</strong>.
                                <br><br>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">
                                        Logout and Login as {{ $invitation->email }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="text-center">
                            <p class="mb-4">Please log in or create an account to accept this invitation.</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Log In</a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Create Account</a>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
