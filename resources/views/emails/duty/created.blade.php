{{-- PARAMS: event --}}
@extends('emails.layouts.master')

@include('emails.duty.layouts.dutydata', [ 'duty' => $event->duty ])

@section('body')
@if ($event->isSelfInitiated())
Du hast dich für folgenden Dienst @yield('with')eingetragen:<br>
@else
Du wurdest durch {{ $event->initiator->getFullName() }} für den folgenden Dienst @yield('with')eingetragen:<br>
@endif
@yield('dutydata')
<br>
Im Anhang findest du noch deinen neuen Dienst als iCalendar-Datei, die du in gängige Kalenderanwendung importieren kannst.
@endsection