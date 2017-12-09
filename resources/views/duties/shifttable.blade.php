@foreach ($days as $day)
    {{-- HIDE/SHOW ROWS --}}
    @if ($day[0]->start->isSameDay($past_threshold))
        <tr><td colspan="5" class="hidden hider" id="hider-show"><a href="#">
            <i class="fa fa-plus-square-o inline-icon" aria-hidden="true"></i>Alle anzeigen
        </a></td></tr>
        <tr><td colspan="5" class="hidden hider" id="hider-hide"><a href="#">
            <i class="fa fa-minus-square-o inline-icon" aria-hidden="true"></i>Vergangene ausblenden</a>
        </td></tr>
    @endif

    @foreach ($day as $shift)
        <tr class="{{ $shift->classes($past_threshold) }}">
            @if ($loop->first)
                <td rowspan="{{ $shiftsPerDay }}" class="day-name" id="day-{{ $day[0]->start->format('j') }}">
                    {{ __('date.' . $day[0]->start->format('l')) }},<br/>{{ $day[0]->start->format('j.n.Y') }}
                </td>
            @endif

            <td class="shift-name">{{ $shift->name() }}</td>
            @include('duties.shiftslot')
            @include('duties.shiftslot')
            <td></td>
        </tr>
    @endforeach
@endforeach
