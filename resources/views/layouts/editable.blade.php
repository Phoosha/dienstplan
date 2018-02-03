{{-- PARAMS: edit editUrl viewUrl modeTitle --}}
@php
    $modeTitle = $modeTitle ?? 'Bearbeitungsmodus';
@endphp
@if(! $edit)
    <a href="{{ $editUrl }}" title="{{ $modeTitle }} starten" class="pure-button primary-button icon-button">
        @if (isset($slot))
        {{ $slot }}
        @else
        <i class="fa-fw fa fa-edit" aria-hidden="true"></i>
        @endif
    </a>
@else
    <a href="{{ $viewUrl }}" title="{{ $modeTitle }} beenden" class="pure-button secondary-button icon-button">
        <i class="fa-fw fa fa-close" aria-hidden="true"></i>
    </a>
@endif