@extends('layouts.master')

@section('title', 'Willkommen')

@section('content')
    <h1>Willkommen {{ Auth::user()->first_name }}!</h1>

    <div class="status">
        @if (session()->has('status'))
            <p class="success">{{ session('status') }}</p>
        @endif
    </div>

    <h2 class="content-subhead">Ankündigungen</h2>

    @include('posts.index')
@endsection