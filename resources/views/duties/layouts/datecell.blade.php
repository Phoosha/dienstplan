{{-- PARAMS: id, type, label, dt --}}
<div class="pure-u-1 pure-u-sm-1-2">
    <label for="{{ $index }}-{{ $type }}-date">{{ $label }}: </label>
    <div class="pure-g">
        <div class="pure-u-13-24"><div class="input-box">
            <input type="text" id="{{ $index }}-{{ $type }}-date" name="duties[{{ $index }}][{{ $type }}-date]" value="{{ $dt->format(config('dienstplan.date_format')) }}" class="date {{ $type }}-date"/>
        </div></div>
        <div class="pure-u-11-24"><div class="input-box">
            <select id="{{ $index }}-{{ $type }}-time" name="duties[{{ $index }}][{{ $type }}-time]" class="time {{ $type }}-time">
                @foreach (time_dropdown($dt) as $time)
                    <option {!! option($dt->format(config('dienstplan.time_format')), $time->format(config('dienstplan.time_format'))) !!}>
                        {{ $time->format(config('dienstplan.time_format')) }}
                    </option>
                @endforeach
            </select>
        </div></div>
    </div>
</div>