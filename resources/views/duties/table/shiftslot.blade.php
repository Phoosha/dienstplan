{{-- PARAMS: shift, slot --}}
<td class="shift-slot selectable" data-shift="{{ $shift->day }}-{{ $shift->shift }}">
    <input type="radio" name="shifts[{{ $shift->day }}][{{ $shift->shift }}]" value="{{ $slot->id }}" class="shift-slot-select" />
</td>
