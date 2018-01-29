@extends('layouts.master')

@section('title', 'Passwort ändern')

@section('content')
    <form method="post" action="{{ url('password/reset') }}" class="pure-form pure-form-stacked" id="reset-form">
        {{ csrf_field() }}
        <h2 class="content-subhead">Neues Passwort festlegen</h2>

        <div class="status">
            @foreach ($errors->all() as $message)
                <p class="error">{{ $message }}</p>
            @endforeach
        </div>

        <input type="hidden" name="token" value="{{ $token }}" />

        <input type="text" id="email" name="email" placeholder="E-Mail" value="{{ $email }}" size="25" tabindex="1" autofocus required />

        <fieldset class="pure-group">
            <input type="password" id="password" name="password" placeholder="Neues Passwort" size="25" tabindex="2" required />
            <input type="password" id="password_confirmation" name="password_confirmation" size="25" placeholder="Neues Passwort (Wdh.)" tabindex="3" required />
        </fieldset>

        <button type="submit" class="pure-button primary-button" tabindex="4">
            Passwort setzen
        </button>
    </form>
@endsection
