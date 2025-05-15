<?php

namespace App\Console\Commands;

use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var stringcls
     * 
     */
    protected $description = 'Notify the User About the event starting soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = \App\Models\Event::with('attendees.user')->whereBetween('start_time',[now(),now()->addDay()])->get();
        $eventCount = $events->count();
        $eventLabel = Str::plural('event',$eventCount);

        $this->info("Found {$eventCount} {$eventLabel}.");
        foreach($events as $event){
            foreach($event->attendees as $attendee){
                $attendee->user->notify(new EventReminderNotification($event));
                $this->info("Notifying User: ".$attendee->user->id);
            }
        }


        $this->info('Remainder Notification sent successfully ');
    }
}
