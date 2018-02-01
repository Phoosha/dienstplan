<li class="pure-menu-item{{ Request::is(($match ?? $uri) . '*') ? ' pure-menu-selected' : '' }}">
    <a href="{{ url($uri) }}" class="pure-menu-link">
        {{ $slot }}
    </a>
</li>
