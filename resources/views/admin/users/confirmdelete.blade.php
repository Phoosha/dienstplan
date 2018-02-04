{{-- PARAMS: users--}}
@extends('layouts.master')

@section('title', 'Nutzer löschen')

@section('content')
<h2 class="content-subhead">
    Nutzer unwiederbringlich löschen
</h2>
<p class="remark">Du hast darum gebeten den Nutzer
    <b>{{ $user->getFullName() }}</b> unwiederbringlich zu löschen!</p>
<h3 class="warning">!!!Achtung!!!</h3>
<p class="remark">
    Bei dieser Operation wird nicht nur der Nutzer, sondern es werde
    insbesondere alle dessen Dienste (und alle andere verknüpften Objekte)
    ebenfalls unwiederbringlich gelöscht.</p>
@unless (count($user->duties) === 0)
<p class="remark warning">Wenn du jetzt auf Löschen klickst, werden auch
    <b>{{ count($user->duties) }}</b> von <b>{{ $user->getFullName() }}</b>
    eingetragene(r) Dienst(e) unwiederbringlich mitgelöscht.</p>
@endunless

<form method="post" action="{{ url('admin/users/trashed', $user->id) }}" class="pure-form">
    {{ csrf_field() }}
    {{ method_field('delete') }}
    <button type="submit" class="pure-button primary-button danger-button">
        <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;Alles&nbsp;Löschen
    </button>
    &emsp;
    <a href="{{ url()->previous() }}" class="pure-button primary-button">
        <i class="fa fa-ban"></i>&nbsp;Nicht&nbsp;Löschen
    </a>
</form>
@endsection
