{{-- PARAMS: duties|duty --}}
@php
    $duties = $duties ?? [ $duty ];
@endphp

<div class="pure-g">
    @foreach ($duties as $duty)
        @can('impersonate', App\Duty::class)
            @section('duties.driver.disabled', '')
        @else
            @section('duties.driver.disabled', 'disabled')
        @endcan
        @include('duties.layouts.duty', [ 'index' => $loop->index ])
    @endforeach
</div>
<input type="hidden" id="min-date" value="{{ now()->diffInDays(App\Policies\DutyPolicy::store_start(Auth::user()), false) }}" disabled/>
<input type="hidden" id="max-date" value="{{ now()->diffInDays(App\Policies\DutyPolicy::store_end(Auth::user()), false) }}" disabled/>
