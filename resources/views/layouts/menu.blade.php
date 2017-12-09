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
                @component('layouts.menuentry')
                    @slot('uri')
                        /
                    @endslot
                    <i class="fa fa-home fa-fw" aria-hidden="true"></i> Start</a>
                @endcomponent
                @component('layouts.menuentry')
                    @slot('uri')
                        plan
                    @endslot
                    <i class="fa fa-calendar fa-fw" aria-hidden="true"></i> Dienstplan</a>
                @endcomponent
                @component('layouts.menuentry')
                    @slot('uri')
                        user/phonelist
                    @endslot
                    <i class="fa fa-phone fa-fw" aria-hidden="true"></i> Telefonliste</a>
                @endcomponent
                @component('layouts.menuentry')
                    @slot('uri')
                        user/settings
                    @endslot
                    <i class="fa fa-user fa-fw" aria-hidden="true"></i> Mein Konto</a>
                @endcomponent
                @component('layouts.menuentry')
                    @slot('uri')
                        admin
                    @endslot
                    <i class="fa fa-users fa-fw" aria-hidden="true"></i> Verwaltung</a>
                @endcomponent
            </ul>
        </div>
        <a href="/auth/logout" class="primary-button pure-button danger-button"><i class="fa fa-sign-out" aria-hidden="true"></i> Abmelden</a>
    @show
</div>

@push('late')
    <script src="{{ asset('js/menu.js') }}"></script>
@endpush
