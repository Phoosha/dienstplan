@extends('layouts.master')

@section('title', 'Dienstplan')

@php
    \Carbon\Carbon::setLocale(config('app.locale'));
@endphp

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
            <th>Schicht</th>
            <th>79/1</th>
            <th>10/1</th>
        </tr></thead>

        <tbody>
        </tbody>
    </table>
@endsection