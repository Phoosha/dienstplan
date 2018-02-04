{{-- PARAMS: users --}}
@extends('layouts.master')

@section('title', 'Nutzer löschen')

@section('content')
<h2 class="content-subhead">
    Nutzer löschen
</h2>
<p class="remark">Du hast darum gebeten den Nutzer
    <b>{{ $user->getFullName() }}</b> zu löschen!</p>
<p class="remark">
    Bei dieser Operation würde nicht nur der Nutzer sondern auch alle dessen
    Dienste gelöscht werden. Wenn du jetzt auf löschen klickst, wird deshalb der
    Nutzer ersteinmal nur als gelöscht markiert und somit deaktiviert.</p>

<form method="post" action="{{ url('admin/users', $user->id) }}" class="pure-form">
    {{ csrf_field() }}
    {{ method_field('delete') }}
    <button type="submit" class="pure-button primary-button danger-button">
        <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;Nutzer&nbsp;Löschen
    </button>
    &emsp;
    <a href="{{ url()->previous() }}" class="pure-button primary-button">
        <i class="fa fa-ban"></i>&nbsp;Nicht&nbsp;Löschen
    </a>
</form>
@endsection
