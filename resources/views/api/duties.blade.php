{{-- PARAMS: duties|duty [method] --}}
@php
    $duties = $duties ?? [ $duty ];
@endphp
BEGIN:VCALENDAR
PRODID:{{ config('ics.fpi') }}
VERSION:2.0
@unless (empty($method))
METHOD:{{ $method }}
@endunless
CALSCALE:GREGORIAN
X-WR-CALNAME:{{ $cal_name }}
X-WR-TIMEZONE:{{ config('app.timezone')  }}
@foreach ($duties as $duty)
BEGIN:VEVENT
UID:{{ sha1($duty->id.$duty->created_on) }}{{ '@' . config('ics.domain') }}
DTSTAMP:{{ icsZuluDateTime($duty->updated_at) }}
DTSTART:{{ icsZuluDateTime($duty->start) }}
DTEND:{{ icsZuluDateTime($duty->end) }}
LAST-MODIFIED:{{ icsZuluDateTime($duty->updated_at) }}
CREATED:{{ icsZuluDateTime($duty->created_at) }}
SEQUENCE:{{ $duty->sequence }}
SUMMARY:{{ config('ics.summary') }}
ORGANIZER:{{ config('ics.organizer') }}
LOCATION:{{ $duty->slot->name }}, {{ config('ics.location') }}
RESOURCES:{{ $duty->slot->name }}
DESCRIPTION:{{ $duty->type === App\Duty::WITH_INTERNEE ? 'Dienst mit Praktikant' : '' }}
URL:{{ url('duties', $duty->id) }}
STATUS:CONFIRMED
TRANSP:TRANSPARENT
END:VEVENT
@endforeach
END:VCALENDAR