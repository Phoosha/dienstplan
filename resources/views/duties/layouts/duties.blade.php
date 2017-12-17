{{-- PARAMS: duties|duty --}}
@php
    $duties = $duties ?? [ $duty ];
@endphp

<h2 class="content-subhead">@yield('duties.head')</h2>

<div class="pure-g">
    @if (count($duties) == 1)
        @include('duties.layouts.duty', [ 'id' => '', 'duty' => $duties[0] ])
    @else
        @foreach ($duties as $duty)
            @include('duties.layouts.duty', [ 'id' => "{$loop->index}-", 'index' => $loop->index ])
        @endforeach
    @endif
</div>
