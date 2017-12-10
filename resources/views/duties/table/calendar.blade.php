<table class="pure-table" id="nav">
    <thead>
        <tr>
            {{-- prev month --}}
            <th><a href="{{ url('plan', $prev) }}">
                &lt;&lt;
            </a></th>

            {{-- current month --}}
            <th colspan="5"><a href="{{ url('plan') }}">
                <i class="fa fa-home fa-lg linked-icon"></i>
            </a></th>

            {{-- next month --}}
            <th><a href="{{ url('plan', $next) }}">
                &gt;&gt;
            </a></th>
        </tr>
    </thead>

    <tbody>
        <tr>
            @foreach ($weeks[0] as $day)
                <td>{{ __('date.' .$day->format('D')) }}</td>
            @endforeach
        </tr>
        @foreach ($weeks as $week)
            <tr>
                @foreach ($week as $day)
                    <td class="{{ $day->isToday() ? 'today' : '' }}{{ $day->isSameMonth($month_start) ? '' : 'other' }}">
                        <a href="{{ $day->isSameMonth($month_start) ? '' : url('/plan') . $day->format('/Y/m') }}#day-{{ $day->format('j') }}">
                            {{ $day->format('j') }}
                        </a>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>