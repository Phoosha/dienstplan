<?php

namespace App\Listeners;

use App\Duty;
use App\Events\DutyEvent;
use App\Jobs\SendDutyNotification;
use Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class DispatchDutyNotification implements ShouldQueue {

    public $tries = 3;

    /**
     * Handle duty events and queue a (delayed) notification if necessary.
     *
     * @param  DutyEvent $event
     *
     * @return void
     */
    public function handle(DutyEvent $event) {
        /*
         * When coalesce is set, check whether a notification is already queued
         * to ensure only one (the original) notification is queued. Whenever we
         * are coalescing we also cache last notification so SendDutyNotification
         * can decide, whether to use the first or the most recent event.
         */
        if ($event->isCoalescable() && self::hasQueuedNotification($event->duty)) {
            self::cacheLastEvent($event);
            Log::notice('Coalesced duty notification', [ get_class($event) ]);
            return;
        }

        /*
         * The job actually sending the notification is delayed to the time
         * specified with the event.
         */
        $result = SendDutyNotification::dispatch($event)->delay($event->getNotificationRelease());

        if (empty($result)) {
            Log::error('Error queueing duty notification', [ get_class($event) ]);
            return;
        }

        /*
         * Finally we need to remember that a notification is queued to
         * coalesce future events.
         */
        self::setQueuedFlag($event);
        Log::debug('Queued duty notification', [ get_class($event) ]);
    }

    /**
     * Returns whether the sending of a notification is already queued.
     *
     * @param \App\Duty $duty
     *
     * @return bool
     */
    protected static function hasQueuedNotification(Duty $duty) {
        return Cache::has(self::cacheKeySequence($duty));
    }

    /**
     * Updates the cache with information, that a notification is already queued.
     *
     * @param \App\Events\DutyEvent $event
     */
    protected static function setQueuedFlag(DutyEvent $event) {
        /*
         * A queued notification is supposed to clear the queued flag itself.
         * But in case something goes wrong, the queued flag is assigned an
         *     expiry time = release time + some time for releasing
         * which ensures that the lock-like flag disappears in any case.
         */
        $release     = $event->getNotificationRelease() ?? now();
        $flagExpires = $release->addMinutes(15);

        /* Set the flag */
        Cache::put(
            self::cacheKeySequence($event->duty),
            $event->duty->sequence,
            $flagExpires
        );

        /*
         * In case later up someone wants to cache a more recent event for us,
         * we need to tell them how long to store it for us, i.e. until we
         * expire. Instead of us a previously queued event may expire later so
         * we always have to use the maximum.
         */
        $maxFlagExpires = Cache::get(self::cacheKeyTTL($event->duty));
        if (empty($maxFlagExpires) || $flagExpires > $maxFlagExpires) {
            Cache::put(self::cacheKeyTTL($event->duty), $flagExpires, $flagExpires);
        }
    }

    /**
     * Caches a more recent event, which may be used by a queued notification
     * to replace its original event with.
     *
     * @param \App\Events\DutyEvent $event
     */
    protected static function cacheLastEvent(DutyEvent $event) {
        $ttl = Cache::get(self::cacheKeyTTL($event->duty));

        Cache::put(self::cacheKeyEvent($event->duty), $event, $ttl);
    }

    /**
     * Returns the most recent coalescable event for <code>$duty</code>.
     *
     * @param \App\Duty $duty
     *
     * @return DutyEvent|null
     */
    public static function getLastEvent(Duty $duty) {
        return Cache::get(self::cacheKeyEvent($duty));
    }

    /**
     * To be called when a notification from the queue is actually sent, because
     * from then future events cannot coalesce anymore.
     *
     * @param \App\Duty $duty
     */
    public static function sentNotificationFor(Duty $duty) {
        $cachedSeq = Cache::get(self::cacheKeySequence($duty));

        if ($duty->sequence >= $cachedSeq) {
            Cache::pull(self::cacheKeySequence($duty));
            Cache::pull(self::cacheKeyTTL($duty));
            Cache::pull(self::cacheKeyEvent($duty));
        }
    }

    private static function cacheKeySequence(Duty $duty) {
        return "duties.{$duty->id}.sequence";
    }

    private static function cacheKeyEvent(Duty $duty) {
        return "duties.{$duty->id}.last_event";
    }

    private static function cacheKeyTTL(Duty $duty) {
        return "duties.{$duty->id}.max_ttl";
    }

}
