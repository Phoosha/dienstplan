<?php

namespace App\Http\Controllers;

use App\Duty;
use App\Events\DutyCreated;
use App\Events\DutyDeleted;
use App\Events\DutyReassigned;
use App\Events\DutyUpdated;
use App\Http\Requests\CreateDuty;
use App\Http\Requests\StoreDuty;
use App\Http\Requests\UpdateDuty;
use App\User;
use App\ViewModels\CalendarMonth;
use App\ViewModels\Shift;
use Auth;
use Carbon\Exceptions\InvalidDateException;
use DB;
use Exception;
use Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Log;
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

        $back = url('plan');

        $this->authorize('create', $duty);

        return view('duties.create', compact('duty', 'back'));
    }

    public function createFromShifts(CreateDuty $request) {
        $duties = $request->getDuties();
        $back = url('plan', [ $request->year, $request->month ]);

        foreach ($duties as $duty)
            $this->authorize('create', $duty);

        return view('duties.create', compact('duties', 'back'));
    }

    public function edit(Duty $duty) {
        $back = url(planWithDuty($duty));
        $readonly = Auth::user()->cannot('edit', $duty);

        return view('duties.edit', compact('duty', 'back', 'readonly'));
    }

    public function store(StoreDuty $request) {
        $duties = $request->getDuties();

        try {
            DB::transaction(function () use ($duties) {
                foreach ($duties as $duty) {
                    $this->authorize('save', $duty);
                    $duty->saveOrFail();
                }
            });
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Error saving duties', [ $duties, $e ]);
            return back()->withInput($request->all())->withErrors('Dienst konnte nicht gespeichert werden');
        }

        $duties->each(function ($duty) {
            DutyCreated::dispatch($duty, Auth::user());
        });

        return redirect(planWithDuty($duties->first()));
    }

    public function update(UpdateDuty $request) {
        $duty    = $request->getDuty();
        $changes = $duty->getDirty();

        $this->authorize('save', $duty);
        try {
            $duty->saveOrFail();
        } catch (Throwable $e) {
            Log::error('Error updating duty', [ $duty, $e ]);
            return back()->withInput($request->all())->withErrors('Dienst konnte nicht aktualisiert werden');
        }

        if (isset($changes['user_id']))
            DutyReassigned::dispatch($duty, Auth::user(), User::find($changes['user_id']));
        else
            DutyUpdated::dispatch($duty, Auth::user());

        return redirect(planWithDuty($duty));
    }

    public function destroy(Duty $duty) {
        $this->authorize('delete', $duty);
        try {
            $duty->delete();
        } catch (Exception $e) {
            Log::error('Error deleting duty', [ $duty, $e ]);
            return back()->withErrors('Dienst konnte nicht gelöscht werden');
        }

        DutyDeleted::dispatch($duty, Auth::user());

        return redirect(planWithDuty($duty));
    }

}
