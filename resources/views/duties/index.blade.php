{{-- PARAMS: weeks, days, slots, month_start, prev_month, prev, next_month, next --}}
@extends('layouts.master')

@section('title', 'Dienstplan')

@section('content')
    <h1>
        {{ monthname($month_start) }}
        {{ $month_start->year }}
    </h1>

    {{-- NAVIGATION --}}
    @include('duties.table.calendar')

    <br />
    <br />

    <form method="get" action="{{ url('duties/create') }}" class="pure-form">
        <input type="hidden" name="year" value="{{ $month_start->year }}" />
        <input type="hidden" name="month" value="{{ $month_start->month }}" />
        <table class="pure-table pure-table-bordered tight-table" id="plan">
            <thead><tr>
                <th>Tag</th>
                <th>Beginn</th>
                @foreach ($slots as $slot)
                    <th>{{ $slot->name }}</th>
                @endforeach
                <th></th>
            </tr></thead>

            <tbody>
                @foreach ($days as $day)
                    @includeWhen($month_start->isSameMonth() && $day[0]->isFirstNowish(), 'duties.table.hider')

                    @foreach ($day as $shift)
                        @include('duties.table.shift')
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </form>
@endsection

@push('late')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/ui.js') }}"></script>
@endpush