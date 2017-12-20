{{-- PARAMS: day, month_start --}}
@includeWhen($month_start->isSameMonth() && $day[0]->isFirstNowish(), 'duties.table.hider')

@foreach ($day as $shift)
    @include('duties.table.shift')
@endforeach
