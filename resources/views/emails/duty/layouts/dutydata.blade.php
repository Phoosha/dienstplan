{{-- PARAMS: duty --}}
@if ($duty->type === App\Duty::WITH_INTERNEE)
    @section('with', '*mit Praktikant* ')
@endif

@section('dutydata')
__Anfang:__    _{{ $duty->start->format(config('dienstplan.datetime_format')) }}_<br>
__Ende:__      _{{ $duty->end->format(config('dienstplan.datetime_format')) }}_<br>
__Fahrzeug:__  _{{ $duty->slot->name }}_<br>
@unless (empty($duty->comment))
__Bemerkung:__
> {{ $duty->comment }}<br>
@endunless
@endsection