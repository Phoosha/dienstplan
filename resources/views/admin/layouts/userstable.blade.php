{{-- PARAMS: users, tablebuttons --}}
<table class="pure-table users-table">
    <thead>
        <th>Name</th>
        <th>Nutzername</th>
        <th>E-Mail</th>
        <th>Telefon</th>
        <th>AED</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr{!! tableOdd($loop->index) !!}>
            <td>{{ $user->last_name }}, {{ $user->first_name }}</td>
            <td>{{ $user->login }}</td>
            <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
            <td><a href="tel:{{ $user->phone }}">{!! str_replace(' ', '&nbsp;', e($user->phone)) !!}</a></td>
            <td class="{{ $user->lastTrainingClasses() }}">{{ empty($user->last_training) ? 'nie' : $user->last_training->diffForHumans() }}</td>
            <td>@include($tablebuttons)</td>
        </tr>
        @endforeach
    </tbody>
</table>