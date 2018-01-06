<?php

namespace App\Http\Requests;

use App\SlotConfig;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Psr\Log\InvalidArgumentException;

class CreateDutyFromShifts extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'year' => 'sometimes|integer|min:1970|max:2037',
            'month' => 'required_with:year|integer|min:1|max:12',
            'shifts' => 'required_with:year|array|max:7|integer_keys',
            'shifts.*' => 'required_with:year|array|max:9|integer_keys',
            'shifts.*.*' => 'required_with:year|integer|exists:slots,id',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator) {
        // Try to add a rule that validates slot ids
        try {
            $year   = isset($this->year)  ? (int) $this->year  : null;
            $month  = isset($this->month) ? (int) $this->month : null;
            $config = SlotConfig::active(Carbon::createSafe($year, $month, 0, 1));
            if (isset($config)) {
                $validator->addRules([
                    'shifts.*.*' => Rule::in($config->slots->pluck('id')->toArray())
                ]);
            }
        } catch (InvalidArgumentException $e) {
        }
    }

}
