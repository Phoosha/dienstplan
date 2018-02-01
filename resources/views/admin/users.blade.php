{{-- PARAMS: users --}}
@extends('layouts.master')

@section('title', 'Nutzerverwaltung')

@section('content')
<div class="mobile-small">
    @include('admin.layouts.adminmenu')

    <h2 class="content-subhead">
        Aktive Nutzer
        <a href="{{ url('admin/users/create') }}" title="Nutzer anlegen" class="pure-button primary-button icon-button">
            <i class="fa-fw fa fa-plus" aria-hidden="true"></i>
        </a>
    </h2>
    @include('admin.layouts.userstable', [ 'tablebuttons' => 'admin.layouts.activetablebuttons' ])


    @unless (empty($trashed))
    <h2 class="content-subhead">Gelöschte Nutzer</h2>
    @include('admin.layouts.userstable', [ 'users' => $trashed, 'tablebuttons' => 'admin.layouts.trashedtablebuttons' ])
    @endunless
</div>
@endsection