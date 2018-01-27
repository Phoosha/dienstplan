{{-- PARAMS: cur_month, next_month, prev_month --}}
@extends('layouts.master')

@section('title', 'Dienstplan')

@section('content')
    <div class="mobile-small">
        <h1>
            {{ monthname($cur_month->start) }}
            {{ $cur_month->year }}
        </h1>

        {{-- NAVIGATION --}}
        @include('duties.table.calendar')

        <br/>
        <br/>

        <form method="get" action="{{ url('duties/create') }}" class="pure-form">
            <input type="hidden" name="year" value="{{ $cur_month->year }}"/>
            <input type="hidden" name="month" value="{{ $cur_month->month }}"/>
            <table class="pure-table pure-table-bordered tight-table" id="plan">
                <thead><tr>
                    <th>Tag</th>
                    <th>Beginn</th>
                    @foreach ($cur_month->slots as $slot)
                        <th>{{ $slot->name }}</th>
                    @endforeach
                    <th></th>
                </tr></thead>

                <tbody>
                    @foreach ($cur_month->getDaysAndShifts() as $day)
                        @includeWhen($cur_month->start->isSameMonth() && $day[0]->isFirstNowish(), 'duties.table.hider')

                        @foreach ($day as $shift)
                            @include('duties.table.shift')
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
@endsection

@push('late')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/ui.js') }}"></script>
@endpush
