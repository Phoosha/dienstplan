{{-- PARAMS: event --}}
@extends('emails.layouts.master')

@include('emails.duty.layouts.dutydata', ['duty' => $event->duty ])

@section('body')
@if ($event->isSelfInitiated())
Du hast einen Dienst übergeben an {{ $event->duty->user->getFullName() }} und bist jetzt selbst *nicht* mehr eingetragen für folgenden Dienst:<br>
@else
Einer deiner Dienst wurde durch {{ $event->initiator->getFullName() }} übergeben an {{ $event->duty->user->getFullName() }} und du bist jetzt selbst *nicht* mehr eingetragen für folgenden Dienst:<br>
@endif
@yield('dutydata')
@endsection