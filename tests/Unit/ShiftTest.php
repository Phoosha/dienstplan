<?php

namespace Tests\Unit;

use App\Duty;
use App\Shift;
use Carbon\Carbon;
use DateTime;
use Tests\TestCase;

class ShiftTest extends TestCase {

    /**
     * @inheritdoc
     */
    protected function shifts() {
        return [ '6', '14', '20' ];
    }

    public function testCanBeCreatedFromNull() {
        $shift = new Shift();

        self::assertInstanceOf(Shift::class, $shift);
    }

    public function testCanBeCreatedFromCarbon() {
        $shift = new Shift(Carbon::now());

        self::assertInstanceOf(Shift::class, $shift);
    }

    public function testCanBeCreatedFromDateTime() {
        $now   = new DateTime(now());
        $shift = new Shift($now);

        self::assertInstanceOf(Shift::class, $shift);
    }

    /**
     * @dataProvider hourProvider
     */
    public function testConstructShiftEncompassesDateTime($dt) {
        $shift = new Shift($dt);

        self::assertLessThanOrEqual($dt, $shift->start);
        self::assertGreaterThan($dt, $shift->end);
    }

    public function hourProvider() {
        $hours = range(0, 23);

        return array_combine(
            array_map(function ($hour) {
                return "${hour} o'clock";
            }, $hours),
            array_map(function ($hour) {
                return [ Carbon::createFromTime($hour) ];
            }, $hours)
        );
    }

    /**
     * @dataProvider shiftIdProvider
     */
    public function testCreateNthShiftIsNthShift($id, $nextId) {
        $now   = now();
        $shift = Shift::create($now->year, $now->month, $now->day, $id);

        self::assertEquals($this->shifts()[$id], $shift->hour);
        self::assertEquals($this->shifts()[$nextId], $shift->end->hour);
    }

    public function shiftIdProvider() {
        return array_map(function ($id) {
            return [
                $id,
                ($id + 1) % count($this->shifts()),
                ($id + 2) % count($this->shifts())
            ];
        }, range(0, count($this->shifts()) - 1));
    }

    public function testCreateFirstShiftReturnsFirstShift() {
        $shift = Shift::firstOfDay();

        self::assertEquals($this->shifts()[0], $shift->hour);
        self::assertEquals($this->shifts()[1], $shift->end->hour);
        self::assertTrue($shift->isFirstShift());
    }

    public function testCreateLastShiftReturnsLastShift() {
        $shift = Shift::lastOfDay();

        self::assertEquals(array_last($this->shifts()), $shift->hour);
        self::assertEquals(array_first($this->shifts()), $shift->end->hour);
        self::assertTrue($shift->isLastShift());
    }

    public function testEmptyCreateAndConstructReturnSameShift() {
        $create_shift = Shift::create();
        $constr_shift = new Shift();

        self::assertEquals($create_shift, $constr_shift);
    }

    public function testCopyReturnsClone() {
        $shift = new Shift();

        self::assertEquals($shift, $shift->copy());
    }

    public function testToDutyReturnsDutyWithSameStartAndEnd() {
        $shift = new Shift();
        $duty  = $shift->toDuty();

        self::assertInstanceOf(Duty::class, $duty);
        self::assertEquals($shift->start, $duty->start);
        self::assertEquals($shift->end, $duty->end);
    }

    /**
     * @dataProvider shiftIdProvider
     */
    public function testNextReturnsNextShift($id, $nextId, $nextNextId) {
        $shift = Shift::create(null, null, null, $id)->next();

        self::assertEquals($this->shifts()[$nextId], $shift->hour);
        self::assertEquals($this->shifts()[$nextNextId], $shift->end->hour);
    }

    /**
     * @dataProvider shiftIdProvider
     */
    public function testPrevReturnsPrevShift($id, $nextId) {
        $shift = Shift::create(null, null, null, $nextId)->prev();

        self::assertEquals($this->shifts()[$id], $shift->hour);
        self::assertEquals($this->shifts()[$nextId], $shift->end->hour);
    }

    public function testNowShiftIsNotPast() {
        self::assertFalse(Shift::create()->isPast());
    }

    public function testNowShiftIsNotFuture() {
        self::assertFalse(Shift::create()->isFuture());
    }

    public function testNowShiftIsNow() {
        self::assertTrue(Shift::create()->isNow());
    }

    public function testNowShiftIsNowish() {
        self::assertTrue(Shift::create()->isNowish());
    }

    public function testTodaysFirstShiftIsFirstNowish() {
        Carbon::setTestNow(Carbon::createFromTime(23, 59, 59));

        self::assertTrue(Shift::firstOfDay()->isFirstNowish());
    }

}
