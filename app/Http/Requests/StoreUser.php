<?php

namespace App\Http\Requests;

use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->can('create', User::class);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'first_name' => 'required|alpha_dash|max:35',
            'last_name' => 'required|alpha_dash|max:35',
            'login' => "sometimes|alpha_num|min:3|max:35|unique:users,login,{$this->route('id')}",
            'email' => 'required|email|max:100',
            'phone' => [ 'present', 'max:35', 'regex:' . config('dienstplan.phone_regex') ],
            'last_training' => 'required',
            'is_admin' => 'sometimes|boolean'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator) {
        $date_format = config('dienstplan.date_format');
        $validator->sometimes('last_training', "date_format:{$date_format}",
            function ($data) {
                return $data->last_training !== 'nie';
            });
    }

    /**
     * Builds a <code>User</code> from <code>$attrs</code>.
     *
     * @param array $attrs
     * @return User
     */
    protected function buildUser(&$attrs) {
        return new User($attrs);
    }

    /**
     * Returns the users whose storage has been requested.
     *
     * @return User
     */
    public function getUser() {
        $attrs = $this->validated();

        $user = $this->buildUser($attrs);

        $user->is_admin = $attrs['is_admin'] ?? false;
        $user->last_training =
            $attrs['last_training'] === 'nie'
                ? null
                : Carbon::parse($attrs['last_training']);

        return $user;
    }

}
