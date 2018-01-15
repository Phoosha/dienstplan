{{-- PARAMS: shiftslot, shift, slot, duties --}}
<td class="shift-slot {{ $shiftslot->classes() }}" data-shift="{{ $shift->day }}-{{ $shift->shift }}">
    @foreach ($duties as $duty)
        <p>
            <span class="duty-time">
                @if ($duty->start > $shift->start && $duty->end < $shift->end)
                    Von {{ minTime($duty->start) }} bis {{ minTime($duty->end) }} Uhr<br/>
                @elseif ($duty->start > $shift->start)
                    Von {{ minTime($duty->start) }} Uhr<br/>
                @elseif ($duty->end < $shift->end)
                    Bis {{ minTime($duty->end) }} Uhr<br/>
                @endif
            </span>
            <span class="{{ $duty->type === Duty::SERVICE ? 'duty-service' : 'duty-user' }}">
                <a href="{{ url('duties', [ $duty->id ]) }}">
                    @if ($duty->type === Duty::SERVICE)
                        <i class="fa fa-wrench inline-icon" aria-hidden="true"></i>Außer Dienst<i class="fa fa-wrench inline-icon" aria-hidden="true"></i>
                    @else
                        {{ $duty->user->getFullName() }}
                    @endif
                </a>
            </span>
            <br/>
            <span class="duty-comment">
                @if ($duty->type === Duty::WITH_INTERNEE)
                    mit Praktikant<br/>
                @endif
                {{ $duty->comment }}
            </span>
        </p>
    @endforeach
    <input type="radio" name="shifts[{{ $shift->day }}][{{ $shift->shift }}]" value="{{ $slot->id }}" class="shift-slot-select" />
</td>
