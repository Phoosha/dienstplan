@extends('layouts.master')

@section('title', 'Passwort zurücksetzen')

@section('content')
    <form method="post" action="{{ url('password/email') }}" class="pure-form" id="reset-form">
        {{ csrf_field() }}
        <h2 class="content-subhead">Passwort zurücksetzen</h2>

        @include('layouts.status')

        <p class="remark">Gib hier bitte die E-Mail, mit der du registriert bist, an.
            Dann schicken wir dir eine Nachricht mit einem Link,
            um deine Identität zu bestätigen und ein neues Passwort
            festzulegen.</p>

        <fieldset>
            <input type="text" id="email" name="email" placeholder="E-Mail" size="25" tabindex="1" autofocus required />

            <button type="submit" class="pure-button primary-button" tabindex="2">
                E-Mail schicken
            </button>
        </fieldset>
    </form>
@endsection
