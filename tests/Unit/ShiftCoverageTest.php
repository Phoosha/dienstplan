<?php

namespace Tests\Unit;

use App\Duty;
use App\Shift;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ShiftCoverageTest extends TestCase {

    protected $shift;

    /**
     * @inheritdoc
     */
    protected function setUp() {
        parent::setUp();

        $this->shift = Shift::firstOfDay(now());
    }

    /**
     * @inheritdoc
     */
    protected function shifts() {
        return [ '6', '18' ];
    }

    protected static function buildDuties(array $timespans) {
        return Collection::make($timespans)
            ->map(function ($arr) {
                return new Duty([
                    'start' => Carbon::now()->setTimeFromTimeString($arr[0]),
                    'end' => Carbon::now()->setTimeFromTimeString($arr[1]),
                ]);
            });
    }

    public function dutyProvider() {
        return [
            'no duties' => [
                0, [ ]
            ],
            'one duty inside' => [
                0, [ [ '6', '17:59:59' ] ]
            ],
            'one duty ends inside' => [
                0, [ [ '5', '12' ] ]
            ],
            'one duty starts inside' => [
                0, [ [ '12', '20' ] ]
            ],
            'one exactly covering duty' => [
                1, [ [ '6', '18' ] ]
            ],
            'two each exactly covering duties' => [
                2, [ [ '6', '18' ], [ '6', '18' ] ]
            ],
            'three each exactly covering duties' => [
                3, [ [ '6', '18' ], [ '6', '18' ], [ '6', '18' ] ]
            ],
            'two together exactly covering duties' => [
                1, [ [ '6', '12' ], [ '12', '18' ] ]
            ],
            'two together exactly covering duties with distractor inbetween' => [
                1, [ [ '6', '12' ], [ '8', '10' ], [ '12', '18' ] ]
            ],
            'two together exactly covering duties with different distractor inbetween' => [
                1, [ [ '6', '12' ], [ '6', '10' ], [ '12', '18' ] ]
            ],
            'together twice covering duties' => [
                2, [ [ '5', '8' ], [ '6', '12' ], [ '8', '19' ], [ '12', '18' ] ]
            ],
            'together twice covering duties with distractor inbetween' => [
                2, [ [ '5', '8' ], [ '6', '12' ], [ '8', '19' ], [ '9', '10' ], [ '12', '18' ] ]
            ],
            'together three times covering duties' => [
                3, [
                    [ '5', '9:30' ], [ '5', '8' ], [ '6', '12' ],
                    [ '8', '19' ], [ '9', '10' ], [ '10', '20' ],
                    [ '12', '18' ] ]
            ],
            'together three times covering duties with distractor inbetween' => [
                3, [
                    [ '4', '9:30' ], [ '5', '8' ], [ '6', '12' ],
                    [ '8', '19' ], [ '9', '10' ],
                    [ '9:15', '9:30'],
                    [ '10', '20' ], [ '12', '18' ]
                ]
            ],
        ];
    }

    protected function doTestNTimeCoverageResult($times, $expected, $timespans, $sort = false) {
        $duties = self::buildDuties($timespans);
        $coverage = $this->shift->analyzeCoverage($times, $duties, $sort);

        self::assertEquals(min($times, $expected), $coverage);
    }

    /**
     * @dataProvider dutyProvider
     */
    public function testZeroTimeCoverageResult($expected, $timespans) {
        return $this->doTestNTimeCoverageResult(0, $expected, $timespans);
    }

    /**
     * @dataProvider dutyProvider
     */
    public function testOneTimeCoverageResult($expected, $timespans) {
        return $this->doTestNTimeCoverageResult(1, $expected, $timespans);
    }

    /**
     * @dataProvider dutyProvider
     */
    public function testTwoTimeCoverageResult($expected, $timespans) {
        return $this->doTestNTimeCoverageResult(2, $expected, $timespans);
    }

    /**
     * @dataProvider dutyProvider
     */
    public function testThreeTimeCoverageResult($expected, $timespans) {
        return $this->doTestNTimeCoverageResult(3, $expected, $timespans);
    }

    /**
     * @dataProvider dutyProvider
     */
    public function testTwoTimeCoverageResultWithSort($expected, $timespans) {
        rsort($timespans);
        return $this->doTestNTimeCoverageResult(2, $expected, $timespans, true);
    }

}
