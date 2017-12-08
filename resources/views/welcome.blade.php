@extends('layouts.master')

@section('title', 'Willkommen')

@section('content')
    <h1>Willkommen Blub Blob!</h1>

    <h2 class="content-subhead">Ankündigungen</h2>

    @include('posts.index')
@endsection