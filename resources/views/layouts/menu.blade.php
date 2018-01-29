<div id="menu">
    @section('menu')
        {{-- Menu toggle --}}
        <a href="#menu" id="menuLink" class="menu-link">
            <!-- Hamburger icon -->
            <span></span>
        </a>

        {{-- Menu heading + content --}}
        <div class="pure-menu">
            <a class="pure-menu-heading" href="https://github.com/Phoosha/dienstplan">
                <div id="logo"></div>
            </a>

            <ul class="pure-menu-list">
                @auth
                    @component('layouts.menuentry', [ 'uri' => '/' ])
                        <i class="fa fa-home fa-fw" aria-hidden="true"></i> Start</a>
                    @endcomponent
                    @component('layouts.menuentry', [ 'uri' => 'plan' ])
                        <i class="fa fa-calendar fa-fw" aria-hidden="true"></i> Dienstplan</a>
                    @endcomponent
                    @component('layouts.menuentry', [ 'uri' => 'user/phonelist' ])
                        <i class="fa fa-phone fa-fw" aria-hidden="true"></i> Telefonliste</a>
                    @endcomponent
                    @component('layouts.menuentry', [ 'uri' => "users/" . Auth::user()->id ])
                        <i class="fa fa-user fa-fw" aria-hidden="true"></i> Mein Konto</a>
                    @endcomponent
                    @component('layouts.menuentry', [ 'uri' => 'admin' ])
                        <i class="fa fa-users fa-fw" aria-hidden="true"></i> Verwaltung</a>
                    @endcomponent
                @endauth
            </ul>
            @guest
                @if (! Request::is('login'))
                    <a href="{{ url('login') }}" class="primary-button pure-button primary-button">
                        <i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp;Anmelden
                    </a>
                @endif
            @endguest
            @auth
                <a href="{{ url('logout') }}" class="primary-button pure-button danger-button">
                    <i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;Abmelden
                </a>
            @endauth
        </div>
    @show
</div>

@push('late')
    <script src="{{ mix('js/menu.js') }}"></script>
@endpush
