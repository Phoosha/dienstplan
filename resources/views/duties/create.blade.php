{{-- PARAMS: duties|duty --}}
@extends('layouts.master')


@if (isset($duty) || count($duties) === 1)
    @section('title', 'Dienst anlegen')
    @if (isset($duty))
        @section('duties.head', 'Neuen Dienst anlegen?')
    @else
        @section('duties.head', 'Dienst so übernehmen?')
    @endif
    @section('duties.save', 'Speichern')
    @section('duties.reset', 'Zurücksetzen')
@else
    @section('title', 'Dienste anlegen')
    @section('duties.head', 'Dienste so übernehmen?')
    @section('duties.save', 'Alle speichern')
    @section('duties.reset', 'Alle zurücksetzen')
@endif

@section('content')
	<form method="post" action="{{ url('duties') }}" class="pure-form pure-form-stacked" id="duty-form">
        {{csrf_field() }}

        @include('duties.layouts.duties')

        @if (isset($duty) || count($duties) > 1)
            <div class="sep"></div>
        @endif

        <fieldset>
            <button type="submit" class="pure-button primary-button">
                <i class="fa fa-save" aria-hidden="true"></i>&nbsp;@yield('duties.save')
            </button>
            <button type="reset" class="pure-button secondary-button">
                <i class="fa fa-undo" aria-hidden="true"></i>&nbsp;@yield('duties.reset')
            </button>
        </fieldset>
    </form>
@endsection

@push('late')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/datepicker.js') }}"></script>
@endpush
