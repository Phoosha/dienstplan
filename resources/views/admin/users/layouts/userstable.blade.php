{{-- PARAMS: users, tablebuttons --}}
<div class="table pure-table users-table">
    <div class="thead">
        <div class="tr">
            <div class="th">Nachname</div>
            <div class="th">Vorname</div>
            <div class="th">Nutzername</div>
            <div class="th">E-Mail</div>
            <div class="th">Telefon</div>
            <div class="th">AED</div>
            <div class="th"></div>
        </div>
    </div>

    <div class="tbody">
        @includeWhen($add, 'admin.layouts.addusers')

        @foreach ($users as $user)
        <div class="tr {!! tableOdd($loop->index + $add) !!}">
            <div class="td">{{ $user->last_name }}</div>
            <div class="td">{{ $user->first_name }}</div>
            <div class="td">{{ $user->login }}</div>
            <div class="td"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></div>
            <div class="td"><a href="tel:{{ $user->phone }}">{!! str_replace(' ', '&nbsp;', e($user->phone)) !!}</a></div>
            <div class="td {{ $user->lastTrainingClasses() }}">{{ $user->getLastTrainingForHumans(now()) }}</div>
            @include($tablebuttons)
        </div>
        @endforeach
    </div>
</div>