@extends('layouts.master')

@section('title', 'Willkommen')

@section('content')
    <h1>Willkommen {{ Auth::user()->first_name }}!</h1>
    @include('layouts.status', [ 'errors' => null ])
    @include('posts.index')
@endsection