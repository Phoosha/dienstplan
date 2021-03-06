<?php

namespace App\Http\Requests;

use App\Duty;
use App\Policies\DutyPolicy;
use App\Slot;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class StoreDuty extends FormRequest {

    protected $parsedDuties;
    protected $max_duties;
    protected $min_dt;
    protected $max_dt;

    /**
     * Late initialization before returning the validation rules.
     */
    protected function preRulesInit() {
        $this->max_duties = 12;
        $this->min_dt     = DutyPolicy::store_start(Auth::user());
        $this->max_dt     = DutyPolicy::store_end(Auth::user());
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->can('store', Duty::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->preRulesInit();

        $date_format = config('dienstplan.date_format');
        $time_format = config('dienstplan.time_format');
        $min_date    = $this->min_dt->copy()->startOfDay();
        $max_date    = $this->max_dt->copy()->startOfDay();

        return [
            'duties'              => "required|array|max:{$this->max_duties}|integer_keys",
            'duties.*.user_id'    => 'sometimes|integer|exists:users,id',
            'duties.*.slot_id'    => 'required|integer|exists:slots,id',
            'duties.*.comment'    => 'nullable|string|max:255',
            'duties.*.start-date' => [
                'required',
                "date_format:{$date_format}",
                "after_or_equal:{$min_date}",
                "before:{$max_date}",
            ],
            'duties.*.end-date'   => [
                'required',
                "date_format:{$date_format}",
                "after_or_equal:{$min_date}",
                "before:{$max_date}",
                'after_or_equal:duties.*.start-date',
            ],
            'duties.*.start-time' => "required|date_format:{$time_format}",
            'duties.*.end-time'   => "required|date_format:{$time_format}",
            'duties.*.type'       => [ 'sometimes', 'integer', Rule::in(Duty::TYPES) ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator) {
        foreach ($this->validated()['duties'] as $key => $duty) {
            /*
             * validate end-time > start-time if end-date = start-date
             */
            $validator->sometimes("duties.{$key}.end-time", "after:duties.{$key}.start-time", function () use ($duty) {
                return $duty['start-date'] === $duty['end-date'];
            });

            /*
             * Post validation after everything else has been successful
             */
            $validator->after(function ($validator) {
                // bail if there have been errors so far
                if ($validator->messages()->isNotEmpty())
                    return;

                $this->parsedDuties = new Collection();

                foreach ($this->input('duties') as $key => $dutyAttrs) {
                    $dutyAttrs['start'] = Carbon::parse("{$dutyAttrs['start-date']} {$dutyAttrs['start-time']}");
                    $dutyAttrs['end']   = Carbon::parse("{$dutyAttrs['end-date']} {$dutyAttrs['end-time']}");

                    $duty = $this->buildDuty($dutyAttrs);

                    // set the user_id, authorization is handled later with the 'save' ability
                    $duty->user_id = $dutyAttrs['user_id'] ?? Auth::user()->id;

                    $time_format = config('dienstplan.time_format');
                    if ($duty->start < $this->min_dt)
                        $validator->errors()->add("duties.{$key}.start-time",
                            "Dienstanfang muss nach oder auf {$this->min_dt->format($time_format)} Uhr liegen");
                    if ($duty->end >= $this->max_dt)
                        $validator->errors()->add("duties.{$key}.end-time",
                            "Dienstende muss vor {$this->max_dt->format($time_format)} Uhr liegen");

                    // slot needs to be active
                    if (! Slot::find($duty->slot_id)->slot_config->isActive($duty->start))
                        $validator->errors()->add("duties.{$key}.slot_id",
                            'Gewähltes Fahrzeug gibt es zu dieser Zeit nicht');

                    // check for existing conflicting duties
                    $conflicts = $duty->getConflicts()
                        ->merge($duty->getConflicts($this->parsedDuties));
                    if ($conflicts->isNotEmpty()) {
                        if ($conflicts->first()->type == Duty::SERVICE)
                            $validator->errors()->add("duties.{$key}.slot_id",
                                "Fahrzeug \"{$duty->slot->name}\" ist zu dieser Zeit außer Dienst");
                        elseif ($duty->type == Duty::SERVICE)
                            $validator->errors()->add("duties.{$key}.type",
                                'Existierende Dienste verhindern, dass das Fahrzeug außer Betrieb genommen wird');
                        else
                            $validator->errors()->add("duties.{$key}.user_id",
                                'Der Fahrer ist zu dieser Zeit schon eingetragen');
                    }

                    // in any case, push the duty and let the validator handle errors (if any)
                    $this->parsedDuties->push($duty);
                }
            });
        }
    }

    /**
     * Builds a <code>Duty</code> from <code>$dutyAttrs</code>.
     *
     * @param array $dutyAttrs
     * @return Duty
     */
    protected function buildDuty(array &$dutyAttrs) {
        return new Duty($dutyAttrs);
    }

    /**
     * Returns the duties whose storage has been requested.
     *
     * @return Collection
     */
    public function getDuties() {
        return $this->parsedDuties;
    }

}
