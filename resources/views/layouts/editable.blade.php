{{-- PARAMS: edit editUrl viewUrl --}}
@if(! $edit)
    <a href="{{ $editUrl }}" class="pure-button primary-button icon-button">
        <i class="fa-fw fa fa-edit" aria-hidden="true"></i>
    </a>
@else
    <a href="{{ $viewUrl }}" class="pure-button secondary-button icon-button">
        <i class="fa-fw fa fa-close" aria-hidden="true"></i>
    </a>
@endif