<?php

namespace App\Http\Controllers\Issue;
use App\Events\IssueCommentEvent;
use App\Events\IssueNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\IssueComment;
use App\Notifications\IssueCreatedNotication;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class CommentsController extends Controller
{

    public function store(Request $request)
    {
      
        $message= $request->message;
        $issue_id= $request->issue;
        $current_user = auth()->user()->id;

        $comment = IssueComment::create ([
            'comment' => $message,
            'issue_id' => $issue_id,
            'user_id' => $current_user,
        ]);
        $data=array();

        if($comment){

            $current_user = User::find($current_user);
            event(new IssueCommentEvent($comment, $current_user));

            //notificaton will send to who create isssue
            $issue = Issue::findOrFail($issue_id);
            $user = $issue->user_id;
            $user = User::find($user);

            $user->notify(new IssueCreatedNotication($issue));

            $message = "به سوال شما جواب ....";
            event(new IssueNotificationEvent($issue, $current_user , $message));
            $data['user_name']=$current_user->name;
            $data['create_date']=$comment->date();
            $data['comment']=$comment->comment;
            $data['id']=$comment->id;
            $admin_user=0;
            if ($comment->isOwner() or  Gate::allows('delete-issues-comment'))
            {
                $admin_user=1;
            }
            $data['adminUser']=$admin_user;

            return response()->json($data,200);

        }
   
    }

    public function destroy(Request $request){

        $id = $request->get('id');

        IssueComment::where('id',$id)->delete();

    }
}
