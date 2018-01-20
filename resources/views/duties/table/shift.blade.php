{{-- PARAMS: shift, loop --}}
<tr class="{{ $shift->classes() }}">
    @if ($loop->first)
        <td rowspan="{{ $shift->shiftsPerDay() }}" class="day-name {{ $shift->start->isToday() ? 'today' : '' }}" id="day-{{ $shift->day }}">
            {{ dayname($shift->start) }},<br/>
            {{ $shift->start->format('j.n.Y') }}
        </td>
    @endif

    <td class="shift-name">{{ $shift->name() }}</td>
        @foreach ($shift->shiftslots as $shiftslot)
            @include('duties.table.shiftslot', [ 'slot' => $shiftslot->slot, 'duties' => $shiftslot->duties ])
        @endforeach
    <td>
        <button type="submit" title="Eintragen" class="pure-button secondary-button icon-button fa fa-paper-plane-o"></button>
    </td>
</tr>
