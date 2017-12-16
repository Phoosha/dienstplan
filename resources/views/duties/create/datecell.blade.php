<td>
    <label class="center" for="{{ $name }}">
        {{ dayname($dt) }}, {{ $dt->format('d.m.Y') }}
    </label>
    <br/>
    <input type="hidden" name="{{ $name }}-date" value="{{ $dt->toDateString() }}">
    <select name="{{ $name }}-time" id="{{ $name }}-time">
        @foreach (time_dropdown($dt) as $time)
            <option value="{{ $time->toTimeString() }}" {!! $time->eq($dt) ? 'selected="selected"' : '' !!}>
                {{ $time->format(config('dienstplan.time_format')) }}
            </option>
        @endforeach
    </select>
</td>