{{-- PARAMS: shift, month_start --}}
<tr class="{{ $shift->classes() }}">
    @if ($loop->first)
        <td rowspan="{{ $shift->shiftsPerDay() }}" class="day-name" id="day-{{ $shift->day }}">
            {{ dayname($shift->start) }},<br/>
            {{ $shift->start->format('j.n.Y') }}
        </td>
    @endif

    <td class="shift-name">{{ $shift->name() }}</td>
        @include('duties.table.shiftslot', [ 'slot' => 0 ])
        @include('duties.table.shiftslot', [ 'slot' => 1 ])
    <td>
        <button type="submit" title="Eintragen" class="pure-button secondary-button icon-button fa fa-paper-plane-o"></button>
    </td>
</tr>
