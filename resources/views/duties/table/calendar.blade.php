{{-- PARAMS: weeks, month_start, prev_month, prev, next_month, next --}}
<table class="pure-table" id="nav">
    <thead>
        <tr>
            {{-- prev month --}}
            <th>
                @if (isset($prev))
                    <a href="{{ url('plan', $prev) }}">
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
                @if (isset($next))
                    <a href="{{ url('plan', $next) }}">
                        &gt;&gt;
                    </a>
                @endif
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            @foreach ($weeks[0] as $day)
                <td>{{ dayname_short($day) }}</td>
            @endforeach
        </tr>
        @foreach ($weeks as $week)
            <tr>
                @foreach ($week as $day)
                    <td class="{{ $day->isToday() ? 'today' : '' }}{{ $day->isSameMonth($month_start) ? '' : 'other' }}">
                        @if (( isset($prev) || ! $day->isSameMonth($prev_month) )
                            && ( isset($next) || ! $day->isSameMonth($next_month) ) )
                            <a href="{{ $day->isSameMonth($month_start)
                                          ? ''
                                          : url('/plan') . $day->format('/Y/m')
                                     }}#day-{{ $day->format('j') }}">
                                {{ $day->day }}
                            </a>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>