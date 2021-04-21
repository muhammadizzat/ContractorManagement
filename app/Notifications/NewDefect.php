<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Constants\DefectStatus;

class NewDefect extends Notification
{
    use Queueable;

    private $project;
    private $case;
    private $defect;
    private $user_name;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($project, $case, $defect, $user_name)
    {
        $this->project = $project;
        $this->case = $case;
        $this->defect = $defect;
        $this->user_name = $user_name;
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
            'user_name' => $this->user_name,

        ];
    }

    public function toOneSignal($notifiable)
    { 
        return OneSignalMessage::create()
            ->subject("New Defect Created")
            ->body("New defect (C".$this->case->ref_no."-D".$this->defect->ref_no.") was created by '".$this->user_name."'")
            ->setData('type', get_class($this))
            ->setData('project_id', $this->project->id)
            ->setData('project_name', $this->project->name)
            ->setData('case_id', $this->case->id)
            ->setData('defect_id', $this->defect->id);        
    }
}
