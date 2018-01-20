<?php

namespace App\Http\Requests;

use App\CalendarMonth;
use App\Duty;
use App\Shift;
use App\Slot;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\HttpException;
use Illuminate\Support\Collection;

class CreateDuty extends FormRequest {

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
            'shifts' => 'required_with:year|array|max:31|integer_keys',
            'shifts.*' => 'required_with:year|array|max:14|integer_keys',
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
        $validator->after(function ($validator) {
            // bail if there have been errors so far
            if ($validator->messages()->isNotEmpty())
                return;

            $month = new CalendarMonth($this->year, $this->month);

            foreach ($this->shifts as $day => $dayOfShifts) {
                if ($day < 1 || $day > $month->daysInMonth)
                    $validator->errors()->add("shifts.{$day}",
                        'Gewählter Tag existiert nicht');

                foreach ($dayOfShifts as $shift => $slot_id) {
                    if ($shift < 0 || $shift >= Shift::shiftsPerDay())
                        $validator->errors()->add("shifts.{$day}.{$shift}",
                            'Gewählte Schicht existiert nicht');
                    if (! Slot::find($slot_id)->slot_config->isActive($month->start))
                        $validator->errors()->add("shifts.{$day}.{$shift}",
                            'Gewähltes Fahrzeug ist zu dieser Zeit nicht aktiv');
                }
            }
        });
    }

    /**
     * Returns an array containing the duties whose creation has
     * been requested.
     *
     * @return Duty[]
     * @throws HttpException
     */
    public function getDuties() {
        $month = new CalendarMonth($this->year, $this->month);

        $duties = new Collection();
        foreach ($this->shifts as $day => $dayOfShifts) {
            foreach ($dayOfShifts as $shift => $slot_id) {
                $duty = Shift::create($month->year, $month->month, $day, $shift)->toDuty();
                $duty->slot_id = (int) $slot_id;
                $duty->user_id = Auth::user()->id;
                $duties->push($duty);
            }
        }

        return Duty::mergeAll($duties->all());
    }

}
