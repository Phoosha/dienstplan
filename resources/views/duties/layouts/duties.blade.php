{{-- PARAMS: duties|duty [readonly] --}}
@php
    $duties = $duties ?? [ $duty ];
    $readonly = $readonly ?? false
@endphp

<div class="pure-g">
    @foreach ($duties as $duty)
        @can('impersonate', App\Duty::class)
            @section('duties.driver.readonly', '')
        @else
            @section('duties.driver.readonly', 'readonly')
        @endcan
        @unless($readonly)
            @section('duties.readonly', '')
        @else
            @section('duties.readonly', 'readonly')
        @endunless
        @include('duties.layouts.duty', [ 'index' => $loop->index ])
    @endforeach
</div>
<input type="hidden" id="min-date" value="{{ App\Policies\DutyPolicy::store_start(Auth::user())->format('d.m.Y') }}" disabled/>
<input type="hidden" id="max-date" value="{{ App\Policies\DutyPolicy::store_end(Auth::user())->format('d.m.Y') }}" disabled/>
