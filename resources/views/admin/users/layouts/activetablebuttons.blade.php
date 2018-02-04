{{-- PARAMS: user --}}
<form method="post" action="{{ url('users', $user->id) }}" class="td pure-button-group">
    <a href="{{ url('admin/users', $user->id) }}" title="Bearbeiten" class="pure-button primary-button icon-button">
        <i class="fa-fw fa fa-edit" aria-hidden="true"></i>
    </a>

    @can('delete', $user)
    {{ csrf_field() }}
    {{ method_field('delete') }}
    <button type="submit" title="Löschen" class="pure-button secondary-button danger-button icon-button" onclick="return confirm('Nutzer wirklich löschen?')">
        <i class="fa-fw fa fa-trash" aria-hidden="true"></i>
    </button>
    @endcan
</form>
