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

            @auth('web')
            <div id="cur-user">{{ Auth::user()->getFullName() }}</div>
            @endauth

            <ul class="pure-menu-list">
                @auth('web')
                    @component('layouts.menuentry', [ 'uri' => '/' ])
                        <i class="fa fa-home fa-fw" aria-hidden="true"></i> Start
                    @endcomponent
                    @component('layouts.menuentry', [ 'uri' => 'plan' ])
                        <i class="fa fa-calendar fa-fw" aria-hidden="true"></i> Dienstplan
                    @endcomponent
                    @component('layouts.menuentry', [ 'uri' => 'phones' ])
                        <i class="fa fa-phone fa-fw" aria-hidden="true"></i> Telefonliste
                    @endcomponent
                    @component('layouts.menuentry', [ 'uri' => "user" ])
                        <i class="fa fa-user fa-fw" aria-hidden="true"></i> Mein Konto
                    @endcomponent
                    @if (Gate::allows('administrate'))
                    @component('layouts.menuentry', [ 'match' => 'admin', 'uri' => 'admin/users' ])
                        <i class="fa fa-users fa-fw" aria-hidden="true"></i> Verwaltung
                    @endcomponent
                    @endif
                @endauth
            </ul>
            @guest
                @if (! Request::is('login'))
                    <a href="{{ url('login') }}" class="primary-button pure-button primary-button">
                        <i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp;Anmelden
                    </a>
                @endif
            @endguest
            @auth('web')
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
