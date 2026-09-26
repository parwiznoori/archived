<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ArchivePageRejectedNotification extends Notification
{
    use Queueable;

    public $archiveId;
    public $bookName;
    public $pageTitle;
    public $rejectComment;
    public $rejectedBy;
    public $imageArchiveId;
    public $columnNumber;

    public function __construct($archiveId, $bookName, $pageTitle, $rejectComment, $rejectedBy, $imageArchiveId, $columnNumber = null)
    {
        $this->archiveId = $archiveId;
        $this->bookName = $bookName;
        $this->pageTitle = $pageTitle;
        $this->rejectComment = $rejectComment;
        $this->rejectedBy = $rejectedBy;
        $this->imageArchiveId = $imageArchiveId;
        $this->columnNumber = $columnNumber;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        if ($this->columnNumber) {
            $url = url('archiveBookDataEntryPageData/' . $this->imageArchiveId . '/' . $this->archiveId . '/' . $this->columnNumber);
        } else {
            $url = url('archiveBookDataEntry/' . $this->archiveId);
        }

        return [
            'action' => 'page_rejected',
            'archive_id' => $this->archiveId,
            'book_name' => $this->bookName,
            'page_title' => $this->pageTitle,
            'reject_comment' => $this->rejectComment,
            'rejected_by' => $this->rejectedBy,
            'image_archive_id' => $this->imageArchiveId,
            'column_number' => $this->columnNumber,
            'url' => $url,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
