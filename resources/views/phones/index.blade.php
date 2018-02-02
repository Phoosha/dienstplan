@extends('layouts.master')

@section('title', 'Telefonnummern')

@section('content')
<h2 class="content-subhead">
    Wichtige Telefonnummern
    @can('edit', App\Phone::class)
    @include('layouts.editable', [ 'editUrl' => url('phones/edit'), 'viewUrl' => url('phones') ])
    @endcan
</h2>
@include('layouts.status')
<div class="table pure-table phonelist" id="phone">
    <div class="thead">
        <div class="tr">
            <div class="th">Name</div>
            <div class="th">Telefon</div>
            @if ($edit)
            <div class="th"></div>
            @endif
        </div>
    </div>

    <div class="tbody">
        @foreach ($phones as $entry)
        <div class="tr {!! tableOdd($loop->index) !!}">
            <div class="td">{{ $entry->name }}</div>
            <div class="td"><a href="tel:{{ $entry->phone }}">{!! str_replace(' ', '&nbsp', e($entry->phone)) !!}</a></div>
            @if($edit && Auth::user()->can('delete', App\Phone::class))
            <div class="td">
                <form method="post" action="{{ url('phones', $entry->id) }}">
                    {{ csrf_field() }}
                    {{ method_field('delete') }}
                    <button type="submit" title="Löschen" class="pure-button secondary-button icon-button danger-button" onclick="return confirm('Telefoneintrag wirklich löschen?')">
                        <i class="fa-fw fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endforeach

        {{-- ADD PHONE ENTRY --}}
        @if ($edit && Auth::user()->can('store', App\Phone::class))
        <form method="post" action="{{ url('phones') }}" class="tr pure-form {!! tableOdd(count($phones)) !!}">
            {{ csrf_field() }}
            <div class="td">
                <input type="text" id="name" name="name" placeholder="Name des Eintrags" value="{{ old('name') }}" required/>
            </div>
            <div class="td">
                <input type="tel" id="phone" name="phone" placeholder="Telefonnummer" value="{{ old('phone') }}" required/>
            </div>
            <div class="td">
                <button type="submit" title="Eintrag speichern" class="pure-button primary-button icon-button">
                    <i class="fa-fw fa fa-save" aria-hidden="true"></i>
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

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
        <tr class="{!! tableOdd($loop->index) !!}">
            <td>{{ $user->last_name }}</td>
            <td>{{ $user->first_name }}</td>
            <td><a href="tel:{{ $user->phone }}">{!! str_replace(' ', '&nbsp', e($user->phone)) !!}</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection