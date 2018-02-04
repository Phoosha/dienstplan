{{-- PARAMS: users --}}
@extends('layouts.master')

@section('title', 'Nutzerverwaltung')

@section('content')
<div class="mobile-small">
    @include('admin.layouts.adminmenu')

    <h2 class="content-subhead">
        Aktive Nutzer
        @component('layouts.editable', [
            'edit' => $add,
            'editUrl' => url('admin/users/create'),
            'viewUrl' => url('admin/users'),
            'modeTitle' => 'Hinzufügemodus'
        ])
            <i class="fa-fw fa fa-plus" aria-hidden="true"></i>
        @endcomponent
    </h2>

    @include('layouts.status')

    @unless ($add)
    <form method="post" action="{{ url('admin/users') }}" class="pure-form" id="users-form">
        {{ csrf_field() }}
        {{ method_field('patch') }}
    @endunless

        @include('admin.users.layouts.userstable', [
            'tablebuttons' => 'admin.users.layouts.activetablebuttons',
            'checkboxes' => ! $add ])

    @unless ($add)
        <fieldset>
            &ensp;
            <label for="last_training">
                Unterweisung:
                <input type="text" id="last_training" name="last_training" value="{{ old('last_training') ?? now()->format(config('dienstplan.date_format')) }}" size="9" class="date start-date" required/>
                <input type="hidden" id="min-date" value="{{ now()->diffInDays(config('dienstplan.min_date'), false) }}" disabled/>
                <input type="hidden" id="max-date" value="{{ now()->diffInDays(config('dienstplan.max_date')) }}" disabled/>
            </label>
            &ensp;
            <button type="submit" title="Unterweisung für Auswahl setzen" class="pure-button primary-button">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;Setzen für Auswahl
            </button>
        </fieldset>
    </form>
    @endunless


    @unless ($trashed->isEmpty() && ! session()->has('trash-status'))
    <h2 class="content-subhead" id="trash">Gelöschte Nutzer</h2>
    @endunless

    @include('layouts.status', [ 'statusKey' => 'trash-status' ])

    @unless ($trashed->isEmpty())
    @include('admin.users.layouts.userstable', [
        'users' => $trashed,
        'tablebuttons' => 'admin.users.layouts.trashedtablebuttons',
        'add' => false,
         'checkboxes' => false])
    @endunless
</div>
@endsection

@unless ($add)
@push('late')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/adminui.js') }}"></script>
    <script src="{{ mix('js/datepicker.js') }}"></script>
@endpush
@endunless
