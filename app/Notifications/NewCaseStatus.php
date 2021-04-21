<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Constants\CaseStatus;

class NewCaseStatus extends Notification
{
    use Queueable;

    private $project;
    private $case;
    private $previous_status;
    private $user_name;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($project, $case, $previous_status, $user_name)
    {
        $this->project = $project;
        $this->case = $case;
        $this->previous_status = $previous_status;
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
            'previous_status' => !empty($this->previous_status)? CaseStatus::$dict[$this->previous_status] : null,
            'case_status' => !empty($this->case->status)? CaseStatus::$dict[$this->case->status] : null,
            'user_name' => $this->user_name,
        ];
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject("Case Status Changed")
            ->body("Status for case (C".$this->case->ref_no.") is now '".CaseStatus::$dict[$this->case->status]."'")
            ->setData('type', get_class($this))
            ->setData('project_id', $this->project->id)
            ->setData('project_name', $this->project->name)
            ->setData('case_id', $this->case->id);        
    }
}
