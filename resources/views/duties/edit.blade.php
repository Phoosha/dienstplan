@extends('layouts.master')

@section('title', 'Dienst ändern')

@section('duties.head', 'Dienst ändern?')

@section('content')
    <form method="post" action="{{ url('duties', $duty->id) }}" class="pure-form pure-form-stacked" id="duty-form">
        {{ csrf_field() }}

        @include('duties.layouts.duties')

        <fieldset>
            <div class="pure-button-group" role="group">
                <button type="submit" name="_method" value="put" class="pure-button primary-button">
                    <i class="fa fa-save" aria-hidden="true"></i>&nbsp;Speichern
                </button>
                <button type="reset" class="pure-button secondary-button">
                    <i class="fa fa-undo" aria-hidden="true"></i>&nbsp;Zurücksetzen
                </button>
                <button type="submit" name="_method" value="delete" class="pure-button primary-button danger-button" onclick="return confirm('Dienst wirklich löschen?')">
                    <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;Löschen
                </button>
            </div>
        </fieldset>
    </form>
@endsection
