{{-- PARAMS: duties|duty --}}
@php
    $duties = $duties ?? [ $duty ];
@endphp

<h2 class="content-subhead">@yield('duties.head')</h2>

<div class="pure-g">
    @foreach ($duties as $duty)
        @include('duties.layouts.duty', [ 'index' => $loop->index ])
    @endforeach
</div>
