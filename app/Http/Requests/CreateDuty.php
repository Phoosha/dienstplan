<?php

namespace App\Http\Requests;

use App\Duty;
use App\Shift;
use App\Slot;
use Carbon\Carbon;
use HttpException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            $year  = isset($this->year)  ? (int) $this->year  : null;
            $month = isset($this->month) ? (int) $this->month : null;
            $slots = Slot::active(Carbon::createSafe($year, $month, 0, 1));
            $validator->addRules([
                'shifts.*.*' => Rule::in($slots->pluck('id')->all())
            ]);
        } catch (InvalidArgumentException $e) {
        }
    }

    /**
     * Returns an array containing the duties whose creation has
     * been requested.
     *
     * @return Duty[]
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function getDuties() {
        $month_start = firstOfMonth($this->year, $this->month);

        $duties = new Collection();
        foreach ($this->shifts as $day => $dayOfShifts) {
            if ($day < 1 || $day > $month_start->daysInMonth)
                abort(400);
            foreach ($dayOfShifts as $shift => $slot_id) {
                if ($shift < 0 || $shift >= Shift::shiftsPerDay())
                    abort(400);

                $duty = Shift::create($month_start->year, $month_start->month, $day, $shift)->toDuty();
                $duty->slot_id = (int) $slot_id;
                $duties->push($duty);
            }
        }

        return Duty::mergeAll($duties->all());
    }

}
