// اعلان‌های لحظه‌ای (Pusher) – اگر در دسترس نباشد نباید بقیه توابع را از کار بیندازد
try {
    if (typeof pusher !== 'undefined' && pusher) {
        var channel = pusher.subscribe('isssuecreated');

        //Bind a function to  Event (the full Laravel class)
        channel.bind('App\\Events\\IssueNotificationEvent', addIssue);
    }
} catch (e) {
    // pusher در دسترس نیست – اعلان‌ها از دیتابیس خوانده می‌شوند
}

function addIssue(data) {
    var name = data.user.name;
    var date = data.date;
    var title = data.title;
    var url = data.url;
    var attachIssue = $("<li class='notif-item' style='background-color: #F0FFF0; margin-top:15px; padding:10px; border-bottom:1px solid #f0f0f0;'><a target='_blank' href='" + url + "' style='text-decoration:none; color:#333; display:block;'><span style='display: block; margin-right: 20px;'><span style='font-size: 13px; font-weight: 600;color: #5b9bd1'>" + name + " <span style='font-size: 11px; font-weight: 300; color:gray'> " + data.message + " </span> </span><span style='font-size: 12px; margin-left:20px; font-weight: 400; opacity: .5; float: left;'> " + date + " </span></span> <span style='display: block !important; font-size: 12px; line-height: 1.3; margin-right: 26px;'>" + title + "</span></a></li>");

    setInList(attachIssue, data, 'notification');
}

/**
 * علامت‌گذاری اعلان به عنوان خوانده شده و حذف آن از لیست
 */
function makeNotificationAsRead(id, element) {
    if (!id) {
        return false;
    }

    var $item = element ? $(element).closest('.notif-item') : $();
    if (!$item.length) {
        $item = $('#notif-' + id);
    }

    $.get('/makeNotificationAsRead/' + id)
        .done(function () {
            removeNotificationItem($item);
        })
        .fail(function () {
            // در صورت خطا اعلان در لیست باقی می‌ماند
        });

    return true;
}

function removeNotificationItem($item) {
    if (!$item || !$item.length) {
        return;
    }

    $item.fadeOut(150, function () {
        $(this).remove();
        updateNotificationBadges(-1);
        refreshNotificationListState();
    });
}

function updateNotificationBadges(delta) {
    var $badges = $('.js-notification-badge');
    if (!$badges.length) {
        return;
    }

    var current = parseInt(String($badges.first().text()).replace(/[^\d]/g, ''), 10);
    if (isNaN(current)) {
        current = 0;
    }

    var next = Math.max(0, current + delta);

    $badges.each(function () {
        $(this).text(next);
        $(this).css('display', next > 0 ? '' : 'none');
    });
}

function refreshNotificationListState() {
    var $list = $('#notification-list');
    if (!$list.length) {
        return;
    }

    if ($list.find('.notif-item').length === 0) {
        if (!$list.find('#no_notification').length) {
            var text = $list.attr('data-empty-text') || '';
            $list.append(
                "<li id='no_notification' style='text-align: center; padding: 25px 15px; color: #999;'>" +
                "<i class='icon-bell' style='font-size: 35px; display: block; margin-bottom: 8px; opacity: 0.2;'></i>" +
                text + "</li>"
            );
        }
    } else {
        $list.find('#no_notification').remove();
    }
}

function setInList(listItem, data, appendedPlace) {
    var $list = $('#notification-list');
    if (!$list.length) {
        $list = $('#' + appendedPlace);
    }
    if (!$list.length) {
        return;
    }

    $('#no_notification').remove();
    $list.prepend(listItem);
    updateNotificationBadges(1);
}
