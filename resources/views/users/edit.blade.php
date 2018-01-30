@extends('layouts.master')

@section('title', 'Mein Konto')

@section('content')
    <h2 class="content-subhead">Deine Nutzerdaten</h2>

    @include('layouts.status', [ 'errors' => null ])

    <form method="post" action="{{ url('users', $user->id) }}" class="pure-form pure-form-aligned">
        {{ csrf_field() }}
        <fieldset>
            <div class="pure-control-group">
                <label for="first_name">Vorname:</label>
                <input type="text" id="first_name" name="first_name" placeholder="Vorname" value="{{ old('first_name') ?? $user->first_name }}" size="25" required/>
                @if ($errors->has('first_name'))
                    <span class="pure-form-message-inline error">{{ $errors->first('first_name') }}</span>
                @endif
            </div>
            <div class="pure-control-group">
                <label for="last_name">Nachname:</label>
                <input type="text" id="last_name" name="last_name" placeholder="Nachname" value="{{ old('last_name') ?? $user->last_name }}" size="25" required/>
                @if ($errors->has('last_name'))
                    <span class="pure-form-message-inline error">{{ $errors->first('last_name') }}</span>
                @endif
            </div>
            @can('changeLogin', $user)
                <div class="pure-control-group">
                    <label for="login">Nutzername:</label>
                    <input type="text" id="login" name="login" placeholder="Nutzername" value="{{ old('login') ?? $user->login }}" size="25" required/>
                    @if ($errors->has('login'))
                        <span class="pure-form-message-inline error">{{ $errors->first('login') }}</span>
                    @endif
                </div>
            @endcan
            <div class="pure-control-group">
                <label for="email">E-Mail:</label>
                <input type="email" id="email" name="email" placeholder="E-Mail" value="{{ old('email') ?? $user->email }}" size="25" required/>
                @if ($errors->has('email'))
                    <span class="pure-form-message-inline error">{{ $errors->first('email') }}</span>
                @endif
            </div>
            <div class="pure-control-group">
                <label for="phone">Telefonnummer:</label>
                <input type="tel" id="phone" name="phone" placeholder="Telefonnummer" value="{{ old('phone') ?? $user->phone }}" size="25" required/>
                @if ($errors->has('phone'))
                    <span class="pure-form-message-inline error">{{ $errors->first('phone') }}</span>
                @endif
            </div>
            @can('promote', $user)
                <div class="pure-control-group">
                    <label for="is_admin">Rolle:</label>
                    <select id="is_admin" name="is_admin" style="width: 14.5125em">
                        <option {!! selected(old('is_admin') ?? $user->is_admin, 0) !!}>Normaler Nutzer</option>
                        <option {!! selected(old('is_admin') ?? $user->is_admin, 1) !!}>Administrator</option>
                    </select>
                    @if ($errors->has('is_admin'))
                        <span class="pure-form-message-inline error">{{ $errors->first('is_admin') }}</span>
                    @endif
                </div>
            @endcan

            <div class="pure-controls">
                <button type="submit" class="pure-button primary-button">
                    <i class="fa fa-save" aria-hidden="true"></i>&nbsp;Speichern
                </button>
                <a href="{{ url()->current() }}" class="pure-button secondary-button">
                    <i class="fa fa-undo" aria-hidden="true"></i>&nbsp;Zurücksetzen
                </a>
                @can('delete', $user)
                    <button type="submit" name="_method" value="delete" class="pure-button primary-button danger-button" onclick="return confirm('Nutzer wirklich löschen?')">
                        <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;Löschen
                    </button>
                @endcan
            </div>
        </fieldset>
    </form>

    <h2 class="content-subhead">Passwort ändern</h2>

    @include('layouts.status', [ 'errors' => null, 'statusKey' => 'password-status' ])

    <form method="post" action="{{ url('users', [ $user->id, 'password' ]) }}" class="pure-form pure-form-aligned">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <fieldset>
            <div class="pure-control-group">
                <label for="password">Passwort:</label>
                <input type="password" id="password" name="password" placeholder="Neues Passwort" size="25" required/>
                @if ($errors->has('password'))
                    <span class="pure-form-message-inline error">{{ $errors->first('password') }}</span>
                @endif
            </div>
            <div class="pure-control-group">
                <label for="password_confirmation">Passwort bestätigen:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" size="25" placeholder="Neues Passwort (Wdh.)" required/>
                @if ($errors->has('password_confirmation'))
                    <span class="pure-form-message-inline error">{{ $errors->first('password_confirmation') }}</span>
                @endif
            </div>

            <div class="pure-controls">
                <button type="submit" class="pure-button primary-button danger-button">
                    <i class="fa fa-save" aria-hidden="true"></i>&nbsp;Passwort setzen
                </button>
            </div>
        </fieldset>
    </form>
@endsection
