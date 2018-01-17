<?php

namespace App\Http\Requests;

use App\Duty;
use App\Slot;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\QueryException;
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
            'duties.*.user_id'    => 'required|integer|exists:users,id',
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
            $validator->sometimes("duties.{$key}.end-time",
                "after:duties.{$key}.start-time",
                function ($input) use ($key) {
                    return $input->duties[$key]['start-date'] === $input->duties[$key]['end-date'];
                });

            $slots = Slot::active(
                Carbon::parse("{$this->duties[$key]['start-date']} {$this->duties[$key]['start-time']}")
            );
            $validator->addRules([
                "duties.{$key}.slot_id" => Rule::in($slots->pluck('id')->all())
            ]);
        }
    }

    /**
     * @return Collection
     * @throws \Exception|\Throwable
     */
    public function persist() {
        $duties = new Collection();

        foreach ($this->duties as $dutyAttrs) {
            $dutyAttrs['start'] = Carbon::parse("{$dutyAttrs['start-date']} {$dutyAttrs['start-time']}");
            $dutyAttrs['end']   = Carbon::parse("{$dutyAttrs['end-date']} {$dutyAttrs['end-time']}");
            $duty = new Duty($dutyAttrs);
            $duty->user()->associate(Auth::user());
            //$duty->user_id = 0;
            $duties->push($duty);
        }

        DB::transaction(function () use ($duties) {
            foreach ($duties as $duty) {
                $duty->saveOrFail();
            }
        });

        return $duties;
    }

}
