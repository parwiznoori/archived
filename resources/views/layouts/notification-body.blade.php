{{-- محتوای متنی یک اعلان (اینکه آیا لینک داشته باشد در header.blade.php تعیین می‌شود) --}}
@if($action === 'page_rejected')
    <span style="display: block; font-size: 13px; font-weight: 600; color: #e74c3c;">
        <i class="fa fa-times-circle"></i> {{ __('general.page_rejected') }}
        @if($isUnread) <span style="background: #e74c3c; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
    </span>
    <span style="display: block; font-size: 12px; color: #555; margin-top: 3px;">
        {{ __('general.book') }}: <strong>{{ $ndata['book_name'] ?? '-' }}</strong> | {{ $ndata['page_title'] ?? '-' }}
    </span>
    <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
        {{ __('general.rejected_by') }}: {{ $ndata['rejected_by'] ?? '-' }}
    </span>
    <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
        {{ __('general.reason') }}: {{ \Illuminate\Support\Str::limit($ndata['reject_comment'] ?? '-', 60, '...') }}
    </span>
    <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
        {{ __('general.rejected_at') }}: {{ $notifDate }}
    </span>

@elseif($action === 'page_fixed')
    <span style="display: block; font-size: 13px; font-weight: 600; color: #27ae60;">
        <i class="fa fa-check-circle"></i> {{ __('general.page_fixed') }}
        @if($isUnread) <span style="background: #27ae60; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
    </span>
    <span style="display: block; font-size: 12px; color: #555; margin-top: 3px;">
        {{ __('general.book') }}: <strong>{{ $ndata['book_name'] ?? '-' }}</strong> | {{ $ndata['page_title'] ?? '-' }}
    </span>
    <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
        {{ __('general.fixed_by') }}: {{ $ndata['fixed_by'] ?? '-' }}
    </span>
    <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
        {{ __('general.fixed_at') }}: {{ $notifDate }}
    </span>

@elseif($ntype == "App\Notifications\IssueCreatedNotication")
    <span style="display: block; font-size: 13px; font-weight: 600; color: #5b9bd1;">
        <i class="fa fa-bug"></i> {{ $ndata['user']['name'] ?? '-' }}
        @if($isUnread) <span style="background: #5b9bd1; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
    </span>
    <span style="display: block; font-size: 12px; color: #555; margin-top: 3px;">
        {{ \Illuminate\Support\Str::limit($ndata['issueCreated']['title'] ?? '', 60, '...') }}
    </span>

@elseif($ntype == "App\Notifications\GraduateBookCreatedNotification" && Route::has('graduate-book.show'))
    <span style="display: block; font-size: 13px; font-weight: 600; color: #8e44ad;">
        <i class="fa fa-book"></i> {{ __('general.graduates-book') }} {{ $ndata['graduateBookCreated']['graduated_year'] ?? '' }}
        @if($isUnread) <span style="background: #8e44ad; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
    </span>

@else
    <span style="display: block; font-size: 13px; font-weight: 600; color: #5b9bd1;">
        {{ \Illuminate\Support\Str::limit($ndata['message'] ?? ($ndata['issueCreated']['title'] ?? 'اعلان'), 80, '...') }}
        @if($isUnread) <span style="background: #5b9bd1; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
    </span>
@endif
