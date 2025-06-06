<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use App\Models\User;
use App\Notifications\ScheduleReminderNotification;
use Carbon\Carbon;

class GenerateScheduleReminders extends Command
{
    protected $signature = 'reminders:generate';
    protected $description = 'Generates notifications for upcoming schedules based on reminder days (H-7, H-5, H-3, H-1) for all users.';

    public function handle()
    {
        $this->info('Starting to generate schedule reminders for all users...');

        $reminderDays = [7, 5, 3, 1];

        $schedules = Schedule::where('start_date', '>=', Carbon::today())->get();

        $users = User::all();

        foreach ($schedules as $schedule) {
            $startDate = Carbon::parse($schedule->start_date);

            foreach ($reminderDays as $daysBefore) {
                $reminderDate = $startDate->copy()->subDays($daysBefore);

                if ($reminderDate->isSameDay(Carbon::today())) {
                    foreach ($users as $user) {
                        $alreadyNotified = $user->notifications()
                            ->where('type', ScheduleReminderNotification::class)
                            ->whereJsonContains('data->schedule_id', $schedule->id_schedule)
                            ->whereDate('created_at', Carbon::today())
                            ->exists();

                        if (!$alreadyNotified) {
                            $user->notify(new ScheduleReminderNotification($schedule, $daysBefore));
                            $this->info("Generated reminder for user {$user->id} for schedule '{$schedule->title}' ({$daysBefore} days before).");
                        } else {
                            $this->line("Reminder for user {$user->id} for schedule '{$schedule->title}' ({$daysBefore} days before) already exists today. Skipping.");
                        }
                    }
                }
            }
        }

        $this->info('Schedule reminder generation completed.');
    }
}