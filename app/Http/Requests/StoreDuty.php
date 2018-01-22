<?php

namespace App\Http\Requests;

use App\Duty;
use App\Slot;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class StoreDuty extends FormRequest {

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
        $date_format = config('dienstplan.date_format');
        $time_format = config('dienstplan.time_format');
        return [
            'duties'              => 'required|array|max:12|integer_keys',
            'duties.*.user_id'    => 'sometimes|integer|exists:users,id',
            'duties.*.slot_id'    => 'required|integer|exists:slots,id',
            'duties.*.comment'    => 'nullable|string|max:255',
            'duties.*.start-date' => "required|date_format:{$date_format}|after_or_equal:1.1.1970|before:1.1.2038",
            'duties.*.end-date'   => "required|date_format:{$date_format}|after_or_equal:1.1.1970|before:1.1.2038|after_or_equal:duties.*.start-date",
            'duties.*.start-time' => "required|date_format:{$time_format}",
            'duties.*.end-time'   => "required|date_format:{$time_format}",
            'duties.*.type'       => 'sometimes|integer|' . Rule::in(Duty::TYPES),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator) {
        foreach ($this->duties as $key => $duty) {
            // validate end-time > start-time if end-date = start-date
            $validator->sometimes("duties.{$key}.end-time", "after:duties.{$key}.start-time", function () use ($duty) {
                    return $duty['start-date'] === $duty['end-date'];
                });

            $validator->after(function ($validator) use ($key, $duty) {
                // bail if there have been errors so far
                if ($validator->messages()->isNotEmpty())
                    return;

                $start = Carbon::parse("{$duty['start-date']} {$duty['start-time']}");

                if (! Slot::find($duty['slot_id'])->slot_config->isActive($start))
                    $validator->errors()->add("duties.{$key}.slot_id",
                        'Gewähltes Fahrzeug ist zu dieser Zeit nicht aktiv');
            });
        }
    }

    /**
     * Returns the duties to be stored.
     *
     * @return Collection
     */
    public function getDuties() {
        $duties = new Collection();

        foreach ($this->duties as $dutyAttrs) {
            $dutyAttrs['start'] = Carbon::parse("{$dutyAttrs['start-date']} {$dutyAttrs['start-time']}");
            $dutyAttrs['end']   = Carbon::parse("{$dutyAttrs['end-date']} {$dutyAttrs['end-time']}");

            $duty = new Duty($dutyAttrs);

            if (Auth::user()->cannot('impersonate', Duty::class))
                $duty->user_id = Auth::user()->id;
            else
                $duty->user_id = $dutyAttrs['user_id'] ?? Auth::user()->id;

            $duties->push($duty);
        }

        return $duties;
    }

}
