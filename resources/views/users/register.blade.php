@extends('layouts.master')

@section('title', 'Registrieren')

@section('content')
    <h2 class="content-subhead">Herzlich willkommen, {{ $user->first_name }}!</h2>

    <p class="remark">Bitte setze jetzt erstmalig dein Passwort. Danach wirst
        du direkt angemeldet und auf die Startseite weitergeleitet. In Zukunft
        kannst du dich dann mit diesem Passwort und deinem Nutzernamen
        <b>{{ $user->login }}</b> anmelden.</p>

    <form method="post" action="{{ url('users', [ $user->id, 'password' ]) . "?register_token={$register_token}" }}" class="pure-form pure-form-aligned">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="pure-control-group">
            <label for="login">Dein Nutzername:</label>
            <input type="text" id="login" name="login" value="{{ $user->login }}" size="25" readonly>
        </div>
        @include('users.layouts.password')
    </form>
@endsection
