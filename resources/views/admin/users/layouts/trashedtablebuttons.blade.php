{{-- PARAMS: user --}}
<form method="post" action="{{ url('admin/users/trashed', [ $user->id, 'restore' ]) }}" class="td pure-button-group">
    <button type="submit" title="Wiederherstellen" class="pure-button primary-button icon-button">
        <i class="fa-fw fa fa-undo" aria-hidden="true"></i>
    </button>

    {{-- can't go first or :first-child pseudo element breaks --}}
    {{ csrf_field() }}
    {{ method_field('put') }}

    <a href="{{ url('admin/users/trashed', [ $user->id, 'delete' ]) }}" title="Endgültig Löschen" class="pure-button secondary-button danger-button icon-button">
        <i class="fa-fw fa fa-trash" aria-hidden="true"></i>
    </a>
</form>