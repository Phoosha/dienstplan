{{-- PARAMS: errors statusKey --}}
@php
    $statusKey = $statusKey ?? 'status';
    $errors = $errors ?? [];
    $errors = is_array($errors) ? $errors : $errors->all();
@endphp
<div class="status">
    @foreach ($errors as $message)
        <p class="error">{{ $message }}</p>
    @endforeach
    @if (session()->has($statusKey))
        <p class="success">{{ session($statusKey) }}</p>
    @endif
</div>
