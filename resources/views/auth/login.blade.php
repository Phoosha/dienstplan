@extends('layouts.master')

@section('title', 'Anmeldung')

@section('content')
    <form method="post" url="{{ url('login') }}" class="pure-form pure-form-stacked" id="login-form">
        {{ csrf_field() }}
        <h2 class="content-subhead">Anmeldung</h2>

        <div id="infoMessage">
            @foreach ($errors->all() as $message)
                <p class="error">{{ $message }}</p>
            @endforeach
        </div>

        <fieldset>
            <label for="user">Nutzername</label>
            <input type="text" id="user" name="login" tabindex="1" autofocus required />

            <label for="password">Passwort
                <span id="forgot">
                    (<a href="{{ url('password/reset') }}">vergessen?</a>)
                </span>
            </label>
            <input type="password" id="password" name="password" tabindex="2" required />

            <label for="remember" class="pure-checkbox">
                <input type="checkbox" id="remember" name="remember" /> Angemeldet bleiben?
            </label>

            <button type="submit" class="pure-button primary-button" tabindex="3">
                Anmelden
            </button>
        </fieldset>
    </form>
@endsection
