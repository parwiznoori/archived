<?php

namespace App\Events;

use App\Models\GraduateBooksPdf;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Carbon\Carbon;

class GraduateBookNotificationEvent  implements ShouldBroadcast
{
    use  SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $graduateBook;
    public $user;
    public $date;
    public $url;
    public $title;
    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(GraduateBooksPdf $graduateBook, User $user, $message)
    {
        $this->graduateBook = $graduateBook;
        $this->user =$user;

        Carbon::setLocale('fa');
        $this->date = Carbon::parse(Carbon::now())->diffForHumans();

        $this->url = route('graduate-book.show', $graduateBook->id);
        $title = __('general.graduates-book').' '. __('general.year').' '.$graduateBook->graduated_year.' '.__('general.has_been_created');
        $this->title = str_limit($title, 50,'...'); 
        $this->message = $message;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new Channel('graduateBookCreated');
    }
}
