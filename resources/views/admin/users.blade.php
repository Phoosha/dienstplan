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
    @include('admin.layouts.userstable', [ 'tablebuttons' => 'admin.layouts.activetablebuttons' ])


    @unless (empty($trashed))
    <h2 class="content-subhead">Gelöschte Nutzer</h2>
    @include('admin.layouts.userstable', [ 'users' => $trashed, 'tablebuttons' => 'admin.layouts.trashedtablebuttons', 'add' => false ])
    @endunless
</div>
@endsection