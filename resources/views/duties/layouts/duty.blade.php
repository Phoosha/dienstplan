{{-- PARAMS: index, duty, duties --}}
<div class="pure-u-1 pure-u-xl-11-24"><fieldset><div class="pure-g">
    <div class="pure-u-1">
        @if (count($duties) > 1)
                <h2 class="content-subhead">Dienst {{ $index + 1 }}</h2>
        @endif
    </div>

    {{-- FAHRER --}}
    <div class="pure-u-5-8">
        <div class="input-box">
            <label for="{{ $index }}-user">Fahrer: </label>
            <select id="{{ $index }}-user" name="duties[{{ $index }}][user_id]" @yield('duties.disabled') @yield('duties.driver.disabled')>
                @foreach ($duty->possibleTakers() as $user)
                    <option {!! selected(old("duties.{$index}.user_id") ?? $duty->user_id, $user->id) !!}>{{ $user->getFullName() }}</option>
                @endforeach
            </select>
        </div>
        @can('service', App\Duty::class)
            <label for="{{ $index }}-internee" class="pure-radio">
                <input type="radio" id="{{ $index }}-internee" name="duties[{{ $index }}][type]" {!! checked(old("duties.{$index}.type") ?? $duty->type, Duty::WITH_INTERNEE) !!}/> mit Praktikant
            </label>
            <label for="{{ $index }}-normal" class="pure-radio">
                <input type="radio" id="{{ $index }}-normal" name="duties[{{ $index }}][type]" {!! checked(old("duties.{$index}.type") ?? $duty->type, Duty::NORMAL) !!}/> ohne Praktikant
            </label>
        @else
            <label for="{{ $index }}-internee" class="pure-checkbox">
                <input type="checkbox" id="{{ $index }}-internee" name="duties[{{ $index }}][type]" {!! checked(old("duties.{$index}.type") ?? $duty->type, Duty::WITH_INTERNEE) !!}/> mit Praktikant
            </label>
        @endcan
    </div>

    {{-- FAHRZEUG --}}
    <div class="pure-u-3-8">
        <div class="input-box">
            <label for="{{ $index }}-slot">Fahrzeug: </label>
            <select id="{{ $index }}-slot" name="duties[{{ $index }}][slot_id]" @yield('duties.disabled')>
                @foreach ($duty->availableSlots() as $slot)
                    <option {!! selected(old("duties.{$index}.slot_id") ?? $duty->slot_id, $slot->id) !!}>{{ $slot->name }}</option>
                @endforeach
            </select>
        </div>
        @can('service', App\Duty::class)
            <label for="{{ $index }}-service" class="pure-radio">
                <input type="radio" id="{{ $index }}-service" name="duties[{{ $index }}][type]" {!! checked(old("duties.{$index}.type") ?? $duty->type, Duty::SERVICE) !!}/> außer Dienst
            </label>
        @endcan
    </div>

    {{-- KOMMENTAR --}}
    <div class="pure-u-1"><div class="input-box">
        <label for="{{ $index }}-comment">Kommentar: </label>
        <input type="text" id="{{ $index }}-comment" name="duties[{{ $index }}][comment]" placeholder="Kommentar" value="{{ old("duties.{$index}.comment") ?? $duty->comment }}" @yield('duties.disabled')/>
    </div></div>

    {{-- ANFANG --}}
    @include('duties.layouts.datecell', [
        'type' => 'start',
        'label' => 'Dienstanfang',
        'dt' => $duty->start ])

    {{-- ENDE --}}
    @include('duties.layouts.datecell', [
        'type' => 'end',
        'label' => 'Dienstende',
        'dt' => $duty->end ])

    <div class="pure-u-1">
        <div id="infoMessage">
            @foreach (array_flatten([ $errors->get("duties.{$index}"), $errors->get("duties.{$index}.*") ]) as $message)
                <p class="error">{{ $message }}</p>
            @endforeach
        </div>
    </div>
</div></fieldset></div>