{{-- PARAMS: duties|duty [method] [source] --}}
@php
    $duties = $duties ?? [ $duty ];
@endphp
BEGIN:VCALENDAR
PRODID:{{ config('ics.fpi') }}
VERSION:2.0
@unless (empty($method))
METHOD:{{ $method }}
@endunless
@unless (empty($source))
SOURCE;VALUE=URI:{{ $source }}
REFRESH-INTERVAL;VALUE=DURATION:P{{ config('ics.refresh') }}
@endunless
@unless (empty(config('ics.color')))
COLOR:firebrick
@endunless
CALSCALE:GREGORIAN
X-WR-CALNAME:{{ $cal_name }}
X-WR-TIMEZONE:{{ config('app.timezone')  }}
@foreach ($duties as $duty)
BEGIN:VEVENT
UID:{{ $duty->uuid }}
DTSTAMP:{{ icsZuluDateTime($duty->updated_at) }}
DTSTART:{{ icsZuluDateTime($duty->start) }}
DTEND:{{ icsZuluDateTime($duty->end) }}
LAST-MODIFIED:{{ icsZuluDateTime($duty->updated_at) }}
CREATED:{{ icsZuluDateTime($duty->created_at) }}
SEQUENCE:{{ $duty->sequence }}
SUMMARY:{{ icsEscapeText(config('ics.summary')) }}
ORGANIZER:{{ config('ics.organizer') }}
LOCATION:{{ icsEscapeText(str_replace(':slot', $duty->slot->name, config('ics.location'))) }}
RESOURCES:{{ $duty->slot->name }}
DESCRIPTION:{{ $duty->type === App\Duty::WITH_INTERNEE ? 'Dienst mit Praktikant' : '' }}
URL:{{ url('duties', $duty->id) }}
STATUS:CONFIRMED
TRANSP:TRANSPARENT
END:VEVENT
@endforeach
END:VCALENDAR