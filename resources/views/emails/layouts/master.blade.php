{{-- PARAMS: [rcpt] --}}
@component('mail::message')
@if (isset($rcpt))
# Hallo {{ $rcpt->first_name }}!
@else
# Hallo!
@endif
<br>
@yield('body')
<br>
<br>
Viele Grüße,<br>
{{ config('app.name') }}
@endcomponent