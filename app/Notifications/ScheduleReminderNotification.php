<?php

namespace App\Notifications;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleReminderNotification extends Notification
{
    use Queueable;

    protected $schedule;
    protected $daysBefore;

    /**
     * Create a new notification instance.
     */
    public function __construct(Schedule $schedule, int $daysBefore)
    {
        $this->schedule = $schedule;
        $this->daysBefore = $daysBefore;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id_notification' => $this->id,
            'schedule_id' => $this->schedule->id_schedule,
            'title' => "Reminder: Schedule '{$this->schedule->title}' is in {$this->daysBefore} days!",
            'content' => "Your schedule '{$this->schedule->title}' is set to start on {$this->schedule->start_date->format('d M Y')}. Don't forget!",
            'schedule' => [
                'id' => $this->schedule->id_schedule,
                'title' => $this->schedule->title,
                'start_date' => $this->schedule->start_date->format('Y-m-d'),
                'end_date' => optional($this->schedule->end_date)->format('Y-m-d'),
            ]
        ];
    }
}
