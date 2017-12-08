<li class="pure-menu-item{{ Request::is($uri) ? ' pure-menu-selected' : '' }}">
    <a href="{{ $uri }}" class="pure-menu-link">
        {{ $slot }}
    </a>
</li>
