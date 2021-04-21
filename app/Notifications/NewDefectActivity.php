<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewDefectActivity extends Notification
{
    use Queueable;

    private $project;
    private $case;
    private $defect;
    private $defect_activity;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($project, $case, $defect, $defect_activity)
    {
        $this->project = $project;
        $this->case = $case;
        $this->defect = $defect;
        $this->defect_activity = $defect_activity;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', OneSignalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'case_id' => $this->case->id,
            'case_ref_no' => $this->case->ref_no,
            'defect_id' => $this->defect->id,
            'defect_ref_no' => $this->defect->ref_no,
            'user_name' => $this->defect_activity->user->name,
            'type' => $this->defect_activity->type,
            'content' => $this->defect_activity->type == "update" ?? $this->defect_activity->content
        ];
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject("Defect Status Changed")
            ->body("New activity added to defect (C".$this->case->ref_no."-D".$this->defect->ref_no.")")
            ->setData('type', get_class($this))
            ->setData('project_id', $this->project->id)
            ->setData('project_name', $this->project->name)
            ->setData('case_id', $this->case->id)
            ->setData('defect_id', $this->defect->id);        
    }

}
