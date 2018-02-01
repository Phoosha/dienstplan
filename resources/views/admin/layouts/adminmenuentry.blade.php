<a href="{{ url($uri) }}" class="pure-button secondary-button{{ Request::is($uri . '*') ? ' pure-button-selected' : '' }}">
    {{ $slot }}
</a>
