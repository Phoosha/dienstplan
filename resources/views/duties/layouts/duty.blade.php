{{-- PARAMS: index, duties --}}
<div class="pure-u-1 pure-u-xl-11-24"><fieldset><div class="pure-g">
    @if (count($duties) > 1)
        <div class="pure-u-1">
            <h2 class="content-subhead">Dienst {{ $index + 1 }}</h2>
        </div>
    @endif

    {{-- FAHRER --}}
    <div class="pure-u-5-8">
        <div class="input-box">
            <label for="{{ $index }}-user">Fahrer: </label>
            <select id="{{ $index }}-user" name="duties[{{ $index }}][user]">
                @foreach ($duty->possibleTakers() as $user)
                    <option value="{{ $user->id }}">{{ $user->getFullName() }}</option>
                @endforeach
            </select>
        </div>
        <label for="{{ $index }}-internee" class="pure-checkbox">
            <input type="checkbox" id="{{ $index }}-internee" name="duties[{{ $index }}][internee]" value="1{{-- TODO --}}" /> mit Praktikant
        </label>
    </div>

    {{-- FAHRZEUG --}}
    <div class="pure-u-3-8"><div class="input-box">
        <label for="{{ $index }}-slot">Fahrzeug: </label>
        <select id="{{ $index }}-slot" name="duties[{{ $index }}][slot]">
            {{-- TODO --}}
        </select>
    </div></div>

    {{-- KOMMENTAR --}}
    <div class="pure-u-1"><div class="input-box">
        <label for="{{ $index }}-comment">Kommentar: </label>
        <input id="{{ $index }}-comment" name="duties[{{ $index }}][comment]" placeholder="Kommentar" value="{{-- TODO --}}" />
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
</div></fieldset></div>