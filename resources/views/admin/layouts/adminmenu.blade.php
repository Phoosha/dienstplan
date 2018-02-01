<div class="pure-button-group">
    @component('admin.layouts.adminmenuentry', [ 'uri' => 'admin/users' ])
        <i class="fa fa-users" aria-hidden="true"></i>&nbsp;Nutzer
    @endcomponent
    @component('admin.layouts.adminmenuentry', [ 'uri' => 'admin/reports' ])
        <i class="fa fa-bar-chart" aria-hidden="true"></i>&nbsp;Berichte
    @endcomponent
    @component('admin.layouts.adminmenuentry', [ 'uri' => 'admin/slots' ])
        <i class="fa fa-car" aria-hidden="true"></i>&nbsp;Fahrzeuge
    @endcomponent
</div>
