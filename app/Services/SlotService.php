<?php

namespace App\Services;

use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotService
{
    /**
     * Returns a Collection of slot tuples for the given doctor + date.
     * Each tuple: ['time' => 'HH:MM:SS', 'display' => '9:00 AM',
     *              'is_past' => bool, 'is_booked' => bool, 'is_available' => bool]
     * Empty collection if the doctor has no schedule on that day.
     */
    public function slotsFor(Doctor $doctor, Carbon $date): Collection
    {
        $dayName = strtolower($date->englishDayOfWeek);
        $schedule = $doctor->schedules()->where('day_of_week', $dayName)->first();

        if (! $schedule) {
            return collect();
        }

        $bookedTimes = $doctor->appointments()
            ->where('appointment_date', $date->toDateString())
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->map(fn ($t) => Carbon::parse($t)->format('H:i:s'))
            ->all();

        $slots = collect();
        $cursor = Carbon::parse($date->toDateString().' '.$schedule->start_time);
        $end = Carbon::parse($date->toDateString().' '.$schedule->end_time);

        while ($cursor->lessThan($end)) {
            $time = $cursor->format('H:i:s');
            $isPast = $cursor->isPast();
            $isBooked = in_array($time, $bookedTimes, true);

            $slots->push([
                'time'         => $time,
                'display'      => $cursor->format('g:i A'),
                'is_past'      => $isPast,
                'is_booked'    => $isBooked,
                'is_available' => ! $isPast && ! $isBooked,
            ]);

            $cursor->addMinutes($schedule->slot_duration);
        }

        return $slots;
    }

    /**
     * Returns a Collection of Carbon dates over the next $daysAhead days
     * on which the doctor has any schedule (regardless of bookings).
     */
    public function bookableDatesFor(Doctor $doctor, int $daysAhead = 30): Collection
    {
        $availableDays = $doctor->schedules->pluck('day_of_week')
            ->map(fn ($d) => strtolower($d))
            ->all();

        $dates = collect();
        for ($i = 0; $i < $daysAhead; $i++) {
            $date = Carbon::today()->addDays($i);
            if (in_array(strtolower($date->englishDayOfWeek), $availableDays, true)) {
                $dates->push($date);
            }
        }

        return $dates;
    }
}
