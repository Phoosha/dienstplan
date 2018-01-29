@extends('layouts.master')

@section('title', 'Telefonnummern')

@section('content')
    <h2 class="content-subhead">Wichtige Telefonnummern</h2>
    <table class="pure-table phonelist">
        <thead>
            <tr>
                <th>Name</th>
                <th>Telefon</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($phones as $entry)
                <tr {!! $loop->index % 2 === 0 ? '' : 'class="pure-table-odd"' !!}>
                    <td>{{ $entry->name }}</td>
                    <td><a href="tel:{{ $entry->phone }}">{{ $entry->phone }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="content-subhead">Alle Mitglieder</h2>
    <table class="pure-table phonelist">
        <thead>
            <tr>
                <th>Name</th>
                <th>Vorname</th>
                <th>Telefon</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr {!! $loop->index % 2 === 0 ? '' : 'class="pure-table-odd"' !!}>
                    <td>{{ $user->last_name }}</td>
                    <td>{{ $user->first_name }}</td>
                    <td><a href="tel:{{ $user->phone }}">{{ $user->phone }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection