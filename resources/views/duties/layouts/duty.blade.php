{{-- PARAMS: id, duty[, index] --}}
<div class="pure-u-1 pure-u-xl-11-24"><fieldset><div class="pure-g">
    @if (isset($index))
        <div class="pure-u-1">
            <h2 class="content-subhead">Dienst {{ $index + 1 }}</h2>
        </div>
    @endif
    <div class="pure-u-5-8">
        <div class="input-box">
            <label for="{{ $id }}user">Fahrer: </label>
            <select name="{{ $id }}user">
                {{-- TODO --}}
            </select>
        </div>
        <label for="{{ $id }}internee" class="pure-checkbox">
            <input type="checkbox" name="{{ $id }}internee" value="1{{-- TODO --}}" /> mit Praktikant
        </label>
    </div>

    <div class="pure-u-3-8"><div class="input-box">
        <label for="{{ $id }}slot">Fahrzeug: </label>
        <select name="{{ $id }}slot">
            {{-- TODO --}}
        </select>
    </div></div>

    <div class="pure-u-1"><div class="input-box">
        <label for="{{ $id }}comment">Kommentar: </label>
        <input id="{{ $id }}comment" name="comment" placeholder="Kommentar" value="{{-- TODO --}}" />
    </div></div>

    @include('duties.layouts.datecell', [
        'type' => 'start',
        'label' => 'Dienstanfang',
        'dt' => $duty->start ])

    @include('duties.layouts.datecell', [
        'type' => 'end',
        'label' => 'Dienstende',
        'dt' => $duty->end ])
</div></fieldset></div>