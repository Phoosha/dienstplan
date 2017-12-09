@extends('layouts.master')

@section('title', 'Dienstplan')

@section('content')
    <h1>
        {{ __('date.' . $month_start->format('F')) }}
        {{ $month_start->format('Y') }}
    </h1>

    {{-- NAVIGATION --}}
    @include('duties.calendar')

    <br />
    <br />

    <table class="pure-table pure-table-bordered tight-table" id="plan">
        <thead><tr>
            <th>Tag</th>
            <th>Schichtbegin</th>
            <th>79/1</th>
            <th>10/1</th>
            <th></th>
        </tr></thead>

        <tbody>
            @include('duties.shifttable')
        </tbody>
    </table>
@endsection

@push('late')
    <script src="{{ asset('js/ui.js') }}"></script>
@endpush