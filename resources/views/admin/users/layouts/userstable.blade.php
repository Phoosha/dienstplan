{{-- PARAMS: users tablebuttons add checkboxes --}}
<div class="table pure-table users-table">
    <div class="thead">
        <div class="tr">
            @if ($checkboxes)
            <div class="th"></div>
            @endif
            <div class="th">Nachname</div>
            <div class="th">Vorname</div>
            <div class="th">Nutzername</div>
            <div class="th">E-Mail</div>
            <div class="th">Telefon</div>
            <div class="th">Unterweisung</div>
            <div class="th"></div>
        </div>
    </div>

    <div class="tbody">
        @includeWhen($add, 'admin.users.layouts.adduser')

        @foreach ($users as $user)
        <div class="tr {!! tableOdd($loop->index + $add) !!}">
            @if ($checkboxes)
            <div class="td clickable">
                <input type="checkbox" name="users[]" value="{{ $user->id }}" class="clickme"/>
            </div>
            @endif
            <div class="td clickable">{{ $user->last_name }}</div>
            <div class="td clickable">{{ $user->first_name }}</div>
            <div class="td clickable">{{ $user->login }}</div>
            <div class="td"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></div>
            <div class="td"><a href="tel:{{ $user->phone }}">{!! str_replace(' ', '&nbsp;', e($user->phone)) !!}</a></div>
            <div class="td {{ $user->lastTrainingClasses() }}">{{ $user->getLastTrainingForHumans(null) }}</div>
            @include($tablebuttons)
        </div>
        @endforeach
    </div>
</div>