<form method="post" action="{{ url('admin/users') }}" class="tr pure-form">
    {{ csrf_field() }}
    <div class="td">
        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"/>
    </div>
    <div class="td">
        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"/>
    </div>
    <div class="td">
        <input type="text" name="login" id="login" value="{{ old('login') }}"/>
    </div>
    <div class="td">
        <input type="email" name="email" id="email" value="{{ old('email') }}"/>
    </div>
    <div class="td">
        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"/>
    </div>
    <div class="td">
        <input type="text" name="last_training" id="last_training" value="{{ old('last_training') ?? 'nie' }}" class="date start-date"/>
        <input type="hidden" id="min-date" value="{{ config('dienstplan.min_date')->format('d.m.Y') }}" disabled/>
        <input type="hidden" id="max-date" value="{{ config('dienstplan.max_date')->format('d.m.Y') }}" disabled/>
    </div>
    <div class="td pure-button-group">
        <button type="submit" title="Hinzufügen" class="pure-button primary-button icon-button">
            <i class="fa-fw fa fa-send" aria-hidden="true"></i>
        </button>
    </div>
</form>

@push('late')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/datepicker.js') }}"></script>
@endpush
