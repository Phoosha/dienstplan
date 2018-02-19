<?php


use App\CalendarMonth;
use App\Duty;
use Carbon\Carbon;
use Illuminate\Support\Collection;

function dayname_short($dt) {
    return __('date.' . $dt->format('D'));
}

function dayname($dt) {
    return __('date.' . $dt->format('l'));
}

function monthname($dt) {
    return __('date.' . $dt->format('F'));
}

function time_dropdown($dt) {
    $dt  = Carbon::instance($dt)->startOfDay();
    $int = config('dienstplan.dropdown_time_steps');

    $vals = [];
    for ($i = $dt->copy(); $i->isSameDay($dt); $i->add($int))
        $vals[] = $i->copy();

    return $vals;
}

/**
 * Returns a time string that uses minutes and seconds only if necessary.
 *
 * @param Carbon $dt
 * @return string
 */
function minTime(Carbon $dt) {
    $format = 'G:i:s';
    if ($dt->second === 0) {
        if ($dt->minute === 0)
            $format = 'G';
        else
            $format = 'G:i';
    }
    return $dt->format($format);
}

/**
 * Returns a Zulu (UTC) string representation of <code>$dt</code> as specified by RFC5545.
 *
 * @param Carbon $dt
 * @return string
 */
function icsZuluDateTime(Carbon $dt) {
    return $dt->setTimezone('UTC')->format('Ymd\THis\Z');
}

/**
 * Outputs value and selection state for an HTML option tag.
 *
 * @param mixed $sel value of the selected option
 * @param mixed $cur value name of the current option
 * @return string
 */
function selected($sel, $cur) {
    $selected = $cur == $sel ? ' selected' : '';
    return "value=\"{$cur}\"{$selected}";
}

/**
 * Outputs value and selection state for an HTML checkbox.
 *
 * @param mixed $sel value of the checked option
 * @param mixed $cur value name of the current option
 * @return string
 */
function checked($sel, $cur) {
    $selected = $cur == $sel ? ' checked' : '';
    return "value=\"{$cur}\"{$selected}";
}

/**
 * Outputs pure-table-odd class if <code>$index</code> is odd.
 *
 * @param int $index
 * @return string
 */
function tableOdd(int $index) {
    return $index % 2 === 0 ? '' : 'pure-table-odd';
}

/**
 * Checks for a range supported by MySQL TIMESTAMP type.
 *
 * @param Carbon $dt
 * @return bool
 */
function isValidDate(Carbon $dt) {
    return $dt >= config('dienstplan.min_date')
        && $dt < config('dienstplan.max_date');
}

/**
 * Checks for a range supported by MySQL TIMESTAMP type and throws
 * an <code>OutOfBoundsException</code> otherwise.
 *
 * @param Carbon $dt
 * @return bool
 */
function isValidDateOrFail(Carbon $dt) {
    $minDate = config('dienstplan.min_date');
    $maxDate = config('dienstplan.max_date');
    if (! isValidDate($dt))
        throw new OutOfBoundsException("Date was not between ${minDate} and ${maxDate}");
    else
        return true;
}


/**
 * Returns a URI that shows the plan with <code>$duty</code>.
 *
 * @param Duty $duty
 * @return string
 */
function planWithDuty(Duty $duty) {
    $start  = $duty->start;

    return "plan/{$start->year}/{$start->month}#day-{$start->day}";
}

/**
 * Returns a URI that shows the plan with <code>$day</code>
 * and is relative to <code>$cur_month</code>
 *
 * @param Carbon $day
 * @param CalendarMonth $cur_month
 * @return string
 */
function planWithDay(Carbon $day, CalendarMonth $cur_month) {
   if ($day->isSameMonth($cur_month->start))
       $prefix = '';
   else
       $prefix = url('/plan', [ $day->year, $day->month ]);

   return "{$prefix}#day-{$day->day}";
}

const ICS_LINE_SEP = "\r\n";
const ICS_PER_LINE = 75;
CONST ICS_REPLACEMENTS = [
    "\\" => "\\\\",
    ','  => '\,',
    ';'  => '\;',
    "\n" => '\n',
];

/**
 * Returns an iCalendar "VCALENDAR" component containing a representation of
 * each of <code>$duties</code> as "VEVENT" component.
 *
 * @param Collection|Duty $duties
 * @param string|null     $method   unless <code>null</code>, the value of the "METHOD"
 *                                  property of the "VCALENDAR" component
 * @param string|null     $cal_name the value of the "X—WR-CALNAME" property of the
 *                                  "VCALENDAR" component defaulting to the "app.name"
 *                                  configuration if unset
 *
 * @return string
 * @throws \Throwable
 */
function iCalendar($duties, $method = null, $cal_name = null) {
    $cal_name = $cal_name ?? config('app.name');
    $duties = $duties instanceof Collection ? $duties : Collection::make([ $duties ]);

    $foldLines = function ($view, $contents) {
        return implode(ICS_LINE_SEP,
            array_map(function ($line) {
                $foldedLine = mb_strcut($line, 0, ICS_PER_LINE);
                for ($pos = ICS_PER_LINE; $pos < strlen($line); $pos += ICS_PER_LINE - 1) {
                    $nextLine = mb_strcut($line, $pos, ICS_PER_LINE - 1);
                    $foldedLine .= ICS_LINE_SEP . ' ' . $nextLine;
                }

                return $foldedLine;
            }, explode("\n", $contents))
        );
    };

    return view('api.duties', compact('duties', 'cal_name', 'method'))->render($foldLines);
}

/**
 * Escapes <code>$text</code> for usage as a value in an iCalendar.
 *
 * @param $text
 *
 * @return mixed
 */
function icsEscapeText($text) {
    return str_replace(
        array_keys(ICS_REPLACEMENTS),
        array_values(ICS_REPLACEMENTS),
        $text
    );
}
