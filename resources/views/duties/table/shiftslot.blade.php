{{-- PARAMS: shiftslot, shift, slot, duties --}}
<td class="shift-slot {{ $shiftslot->classes() }}" data-shift="{{ $shift->day }}-{{ $shift->shift }}">
    <input type="radio" name="shifts[{{ $shift->day }}][{{ $shift->shift }}]" value="{{ $slot->id }}" class="shift-slot-select" />
    @foreach ($duties as $duty)
        <div class="duty">
            @if ($duty->start > $shift->start || $duty->end < $shift->end)
                <div class="duty-time">
                    @if ($duty->start > $shift->start && $duty->end < $shift->end)
                        <div class="duty-time-between">{{ minTime($duty->start) }} &ndash; {{ minTime($duty->end) }} Uhr</div>
                    @elseif ($duty->start > $shift->start)
                        <div class="duty-time-start">Ab {{ minTime($duty->start) }} Uhr</div>
                    @elseif ($duty->end < $shift->end)
                        <div class="duty-time-end">Bis {{ minTime($duty->end) }} Uhr</div>
                    @endif
                </div>
            @endif
            <div class="{{ $duty->type === App\Duty::SERVICE ? 'duty-service' : 'duty-user' }}">
                @can('edit', $duty)
                <a href="{{ url('duties', [ $duty->id ]) }}">
                @endcan
                    @if ($duty->type === App\Duty::SERVICE)
                        <i class="fa fa-wrench" aria-hidden="true"></i>&nbsp;Außer&nbsp;Dienst&nbsp;<i class="fa fa-wrench" aria-hidden="true"></i>
                    @else
                        {{ $duty->user->getFullName() }}
                    @endif
                @can('edit', $duty)
                </a>
                @endcan
            </div>
            <div class="duty-comment">
                @if ($duty->type === App\Duty::WITH_INTERNEE)
                    mit Praktikant<br/>
                @endif
                {{ $duty->comment }}
            </div>
        </div>
    @endforeach
</td>
