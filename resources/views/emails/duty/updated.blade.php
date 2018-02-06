{{-- PARAMS: event --}}
@extends('emails.layouts.master')

@include('emails.duty.layouts.dutydata', ['duty' => $event->duty ])

@section('body')
@if ($event->isSelfInitiated())
Du hast einen deiner Dienst geändert und bist jetzt @yield('with')eingetragen für folgenden Dienst:<br>
@else
Einer deiner Dienst wurde durch {{ $event->initiator->getFullName() }} geändert und du bist jetzt @yield('with')eingetragen für folgenden Dienst:<br>
@endif
@yield('dutydata')
<br>
Im Anhang findest du noch deinen aktualisierten Dienst als iCalendar-Datei, die du in gängige Kalenderanwendung importieren kannst.
@endsection