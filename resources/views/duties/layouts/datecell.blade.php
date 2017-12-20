{{-- PARAMS: id, type, label, dt --}}
<div class="pure-u-1 pure-u-sm-1-2">
    <label for="{{ $id.$type }}-date">{{ $label }}: </label>
    <div class="pure-g">
        <div class="pure-u-13-24"><div class="input-box">
                <input type="text" name="{{ $id.$type }}-date" value="{{ $dt->format(config('dienstplan.date_format')) }}" class="date {{ $type }}-date"/>
            </div></div>
        <div class="pure-u-11-24"><div class="input-box">
            <select name="{{ $id.$type }}-time" id="{{ $id.$type }}-time" class="time {{ $type }}-time">
                @foreach (time_dropdown($dt) as $time)
                    <option value="{{ $time->toTimeString() }}" {!! $time->eq($dt) ? 'selected="selected"' : '' !!}>
                        {{ $time->format(config('dienstplan.time_format')) }}
                    </option>
                @endforeach
            </select>
        </div></div>
    </div>
</div>