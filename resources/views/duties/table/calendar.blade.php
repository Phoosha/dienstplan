{{-- PARAMS: weeks, month_start, prev_month, prev, next_month, next --}}
<table class="pure-table" id="nav">
    <thead>
        <tr>
            {{-- prev month --}}
            <th>
                @if ($prev_month->isUsable() && Gate::allows('month.view', $prev_month))
                    <a href="{{ url('plan', [ $prev_month->year, $prev_month->month ]) }}">
                        &lt;&lt;
                    </a>
                @endif
            </th>

            {{-- current month --}}
            <th colspan="5"><a href="{{ url('plan') }}">
                <i class="fa fa-home fa-lg linked-icon"></i>
            </a></th>

            {{-- next month --}}
            <th>
                @if ($next_month->isUsable() && Gate::allows('month.view', $next_month))
                    <a href="{{ url('plan', [ $next_month->year, $next_month->month ]) }}">
                        &gt;&gt;
                    </a>
                @endif
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            @foreach (($cur_month->getWeeksAndDays())[0] as $day)
                <td>{{ dayname_short($day) }}</td>
            @endforeach
        </tr>
        @foreach ($cur_month->getWeeksAndDays() as $week)
            <tr>
                @foreach ($week as $day)
                    <td class="{{ $day->isToday() ? 'today' : '' }}{{ $day->isSameMonth($cur_month->start) ? '' : 'other' }}">
                        @if (( $day->gte($cur_month->start) ||
                                ( $prev_month->isUsable() && Gate::allows('month.view', $prev_month) )
                            ) && ( $day->lte($cur_month->end) ||
                                ( $next_month->isUsable() && Gate::allows('month.view', $next_month) )
                            ))
                            <a href="{{ planWithDay($day, $cur_month) }}">{{ $day->day }}</a>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>