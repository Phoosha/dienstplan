{{-- HIDE/SHOW ROWS --}}
@includeWhen($month_start->isSameMonth() && $day[0]->isFirstNowish(), 'duties.table.hider')

@foreach ($day as $shift)
    @include('duties.table.shift', [ 'shift_id' => "{$shift->day}-{$shift->shift}" ])
@endforeach
