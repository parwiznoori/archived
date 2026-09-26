<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ArchivePageFixedNotification extends Notification
{
    use Queueable;

    public $archiveId;
    public $bookName;
    public $pageTitle;
    public $fixedBy;
    public $imageArchiveId;

    public function __construct($archiveId, $bookName, $pageTitle, $fixedBy, $imageArchiveId)
    {
        $this->archiveId = $archiveId;
        $this->bookName = $bookName;
        $this->pageTitle = $pageTitle;
        $this->fixedBy = $fixedBy;
        $this->imageArchiveId = $imageArchiveId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'action' => 'page_fixed',
            'archive_id' => $this->archiveId,
            'book_name' => $this->bookName,
            'page_title' => $this->pageTitle,
            'fixed_by' => $this->fixedBy,
            'image_archive_id' => $this->imageArchiveId,
            'url' => url('archiveqcheckdata/' . $this->archiveId . '/' . $this->imageArchiveId),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
