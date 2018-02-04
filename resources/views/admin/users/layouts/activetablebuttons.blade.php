{{-- PARAMS: user --}}
<div class="td pure-button-group">
    <a href="{{ url('admin/users', $user->id) }}" title="Bearbeiten" class="pure-button primary-button icon-button">
        <i class="fa-fw fa fa-edit" aria-hidden="true"></i>
    </a>

    @can('delete', $user)
    <a href="{{ url('admin/users', [ $user->id, 'delete' ]) }}" title="Löschen" class="pure-button secondary-button danger-button icon-button">
        <i class="fa-fw fa fa-trash" aria-hidden="true"></i>
    </a>
    @endcan
</div>
