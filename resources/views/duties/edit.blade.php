{{-- PARAMS: duty, readonly --}}
@extends('layouts.master')

@section('title', 'Dienst ändern')

@section('duties.head', 'Dienst ändern?')

@section('content')
    <h2 class="content-subhead">
        @yield('duties.head')
        @unless (empty($back))
            <a href="{{ $back }}" class="pure-button secondary-button icon-button">
                <i class="fa-fw fa fa-close" aria-hidden="true"></i>
            </a>
        @endunless
    </h2>

    <form method="post" action="{{ url('duties', $duty->id) }}" class="pure-form pure-form-stacked" id="duty-form">
        {{ csrf_field() }}

        @include('duties.layouts.duties', [ 'action' => 'edit' ])

        <fieldset>
            @can('update', $duty)
                <button type="submit" name="_method" value="put" class="pure-button primary-button">
                    <i class="fa fa-save" aria-hidden="true"></i>&nbsp;Speichern
                </button>
                <a href="{{ url()->current() }}" class="pure-button secondary-button">
                    <i class="fa fa-undo" aria-hidden="true"></i>&nbsp;Zurücksetzen
                </a>
            @endcan
            @can('delete', $duty)
                <button type="submit" name="_method" value="delete" class="pure-button primary-button danger-button" onclick="return confirm('Dienst wirklich löschen?')">
                    <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;Löschen
                </button>
            @endcan
        </fieldset>
    </form>
@endsection

@push('late')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/datepicker.js') }}"></script>
@endpush
