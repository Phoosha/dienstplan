<?php

namespace App\Http\Requests;

use App\Duty;
use App\Policies\DutyPolicy;
use Auth;

class UpdateDuty extends StoreDuty {

    protected function preRulesInit() {
        $duty = Duty::find($this->route('id'));

        $this->max_duties = 1;
        $this->min_dt     = DutyPolicy::update_start(Auth::user(), $duty);
        $this->max_dt     = DutyPolicy::store_end(Auth::user());
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $duty = Duty::find($this->route('id'));

        return Auth::user()->can('update', $duty);
    }

    /**
     * Updates an existing <code>Duty</code> with <code>$dutyAttrs</code>.
     *
     * @param array $dutyAttrs
     * @return Duty
     */
    protected function buildDuty(array &$dutyAttrs) {
        return Duty::find($this->route('id'))->fill($dutyAttrs);
    }

    /**
     * Returns the updated <code>Duty</code>.
     *
     * @return Duty
     */
    public function getDuty() {
        return $this->getDuties()->first();
    }

}
