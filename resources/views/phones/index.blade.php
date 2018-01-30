@extends('layouts.master')

@section('title', 'Telefonnummern')

@section('content')
    <h2 class="content-subhead">
        Wichtige Telefonnummern
        @can('edit', App\Phone::class)
        @if (! $edit)
            <a href="{{ url('phones/edit') }}" class="pure-button primary-button icon-button">
                <i class="fa-fw fa fa-edit" aria-hidden="true"></i>
            </a>
        @else
            <a href="{{ url('phones') }}" class="pure-button secondary-button icon-button">
                <i class="fa-fw fa fa-close" aria-hidden="true"></i>
            </a>
        @endif
        @endcan
    </h2>
    <div class="status">
        @foreach($errors->all() as $message)
        <p class="error">{{ $message }}</p>
        @endforeach
    </div>
    <table class="pure-table phonelist" id="phone">
        <thead>
            <tr>
                <th>Name</th>
                <th>Telefon</th>
                @if ($edit)
                <th></th>
                @endif
            </tr>
        </thead>

        <tbody>
            @foreach ($phones as $entry)
            <tr{!! tableOdd($loop->index) !!}>
                <td>{{ $entry->name }}</td>
                <td><a href="tel:{{ $entry->phone }}">{{ $entry->phone }}</a></td>
                @if($edit && Auth::user()->can('delete', App\Phone::class))
                <td><form method="post" action="{{ url('phones', $entry->id) }}">
                    {{ csrf_field() }}
                    {{ method_field('delete') }}
                    <button type="submit" title="Löschen" class="pure-button secondary-button icon-button danger-button" onclick="return confirm('Telefoneintrag wirklich löschen?')">
                        <i class="fa-fw fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                </form></td>
                @endif
            </tr>
            @endforeach
            @if ($edit && Auth::user()->can('store', App\Phone::class))
            <form method="post" action="{{ url('phones') }}" class="pure-form">
                {{ csrf_field() }}
                <tr{!! tableOdd(count($phones)) !!}>
                    <td><input type="text" id="name" name="name" placeholder="Name des Eintrags" value="{{ old('name') }}" required/></td>
                    <td><input type="tel" id="phone" name="phone" placeholder="Telefonnummer" value="{{ old('phone') }}" required/></td>
                    <td>
                        <button type="submit" title="Eintrag speichern" class="pure-button primary-button icon-button">
                            <i class="fa-fw fa fa-save" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            </form>
            @endif
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
            <tr{!! tableOdd($loop->index) !!}>
                <td>{{ $user->last_name }}</td>
                <td>{{ $user->first_name }}</td>
                <td><a href="tel:{{ $user->phone }}">{{ $user->phone }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection