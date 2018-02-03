@cannot('resetPasswordless', $user)
    <div class="pure-control-group">
        <label for="password">Altes Passwort:</label>
        <input type="password" id="password" name="password" placeholder="Altes Passwort" size="25" required/>
        @if ($errors->has('password'))
            <span class="pure-form-message-inline error">{{ $errors->first('password') }}</span>
        @endif
    </div>
@endcan

<div class="pure-control-group">
    <label for="new-password">Neues Passwort:</label>
    <div class="pure-group">
        <input type="password" id="new-password" name="new-password" placeholder="Neues Passwort" size="25" required/>
        <input type="password" id="new-password_confirmation" name="new-password_confirmation" size="25" placeholder="Neues Passwort (Wdh.)" required/>
    </div>
    @if ($errors->has('new-password'))
        <span class="pure-form-message-inline error">{{ $errors->first('new-password') }}</span>
    @elseif ($errors->has('new-password_confirmation'))
        <span class="pure-form-message-inline error">{{ $errors->first('new-password_confirmation') }}</span>
    @endif
</div>

<div class="pure-controls">
    <button type="submit" class="pure-button primary-button danger-button">
        <i class="fa fa-save" aria-hidden="true"></i>&nbsp;Passwort setzen
    </button>
</div>
