@extends('layouts.master')

@section('title', 'Dienstplan')

@section('content')
    <h1>
        {{ __('date.' . $month_start->format('F')) }}
        {{ $month_start->format('Y') }}
    </h1>

    {{-- NAVIGATION --}}
    @include('duties.table.calendar')

    <br />
    <br />

    <form method="POST" action="{{ url('duties/create') }}" class="pure-form">
        {{ csrf_field() }}
        <input type="hidden" name="year" value="{{ $month_start->year }}">
        <input type="hidden" name="month" value="{{ $month_start->month }}">
        <table class="pure-table pure-table-bordered tight-table" id="plan">
            <thead><tr>
                <th>Tag</th>
                <th>Schichtbegin</th>
                <th>79/1</th>
                <th>10/1</th>
                <th></th>
            </tr></thead>

            <tbody>
                @foreach ($days as $day)
                    @include('duties.table.day')
                @endforeach
            </tbody>
        </table>
    </form>
@endsection

@push('late')
    <script src="{{ asset('js/ui.js') }}"></script>
@endpush