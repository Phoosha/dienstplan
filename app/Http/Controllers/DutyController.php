<?php

namespace App\Http\Controllers;

use App\Duty;
use App\CalendarMonth;
use App\Http\Requests\CreateDuty;
use App\Http\Requests\StoreDuty;
use App\Shift;
use Carbon\Exceptions\InvalidDateException;
use DB;
use Gate;
use Throwable;

class DutyController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function index($year = null, $month = null) {
        try {
            $cur_month  = new CalendarMonth($year, $month);
            $next_month = $cur_month->next();
            $prev_month = $cur_month->prev();
        } catch (InvalidDateException $e) {
            abort(400);
        }

        if (! $cur_month->isUsable())
            abort(404);

        Gate::authorize('month.view', $cur_month);

        return view('duties.index', compact('cur_month', 'next_month', 'prev_month'));
    }

    public function create(CreateDuty $request) {
        // Check if we want to create duties from a selection of shifts
        if (isset($request['year'])) {
            return $this->createFromShifts($request);
        }

        // Otherwise present the current shift
        $duty = Shift::create()->toDuty();
        $duty->slot()->associate(
            $duty->availableSlots()->first()
        );

        $this->authorize('create', $duty);

        return view('duties.create', compact('duty', 'from_scratch'));
    }

    public function createFromShifts(CreateDuty $request) {
        $duties = $request->getDuties();

        foreach ($duties as $duty)
            $this->authorize('create', $duty);

        return view('duties.create', compact('duties', 'from_scratch'));
    }

    public function edit(Duty $duty) {
        $this->authorize('edit', $duty);

        return view('duties.edit', compact('duty'));
    }

    public function store(StoreDuty $request) {
        $duties = $request->getDuties();

        try {
            DB::transaction(function () use ($duties) {
                foreach ($duties as $duty) {
                    $this->authorize('store', $duty);
                    $duty->saveOrFail();
                }
            });
        } catch (Throwable $e) {
            return back()->withInput($request->all())->withErrors('Dienst konnte nicht gespeichert werden.');
        }

        return redirect(planWithDuty($duties->first()));
    }

    public function update() {
        // FIXME
        dd('update');
        // $this->authorize('update', $duty);
    }

    public function destroy($id) {
        $duty = Duty::find($id);

        $this->authorize('delete', $duty);
        $duty->delete();

        return redirect(planWithDuty($duty));
    }

}
