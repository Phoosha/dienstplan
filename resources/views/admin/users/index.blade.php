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

    @include('admin.users.layouts.userstable', [ 'tablebuttons' => 'admin.users.layouts.activetablebuttons' ])


    @unless ($trashed->isEmpty() && ! session()->has('trash-status'))
    <h2 class="content-subhead" id="trash">Gelöschte Nutzer</h2>
    @endunless

    @include('layouts.status', [ 'statusKey' => 'trash-status' ])

    @unless ($trashed->isEmpty())
    @include('admin.users.layouts.userstable', [
        'users' => $trashed,
        'tablebuttons' => 'admin.users.layouts.trashedtablebuttons',
        'add' => false ])
    @endunless
</div>
@endsection