{{-- PARAMS: event --}}
@extends('emails.layouts.master')

@include('emails.duty.layouts.dutydata', [ 'duty' => $event->duty ])

@section('body')
@if ($event->isSelfInitiated())
Du hast den nachfolgenden Dienst gelöscht und bist dafür *nicht* mehr eingetragen:<br>
@else
Der nachfolgende Dienst wurde durch {{ $event->initiator->getFullName() }} gelöscht und du bist dafür *nicht* mehr eingetragen:<br>
@endif
@yield('dutydata')
@endsection