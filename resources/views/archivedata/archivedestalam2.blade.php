<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Archive Document</title>
    <style>
        body {
            font-family: nazanin;
            line-height: 1.6;
            direction: rtl;
            text-align: justify;
            margin: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3px;
        }
        .header img {
            width: 100px;
            height: 100px;
            margin: 0;
        }
        h3 {
            margin: 0;
        }
        p {
            margin: 10px 0;
        }
        .title {
            margin: 0;
        }
        .info-bar {
            text-align: right;
            margin-bottom: 20px;
            font-size: 1.1em;
            padding-right: 20px;
        }
        .signature {
            margin-top: 40px;
            text-align: center;
        }
        table{
            width: 100%!important;
        }

        td {
            text-align: center;
        }

    </style>
</head>
<body>

<!-- Header Section with Logos and Centered Title -->
<div class="header">

    <table class="table table-border border-2 w-100">
        <tr style="background-color: red!important;" class="w-100">
            <td rowspan="2" style="position:relative!important;; vertical-align: top!important;">
                <img style="position: absolute!important;top:0px!important;" src="{{ url('img/emarat-logo.jpg') }}" height="100" width="100" alt="لوگو راست">
            </td>
            <td>

            </td>
            <td></td>
            <td>

            </td>
            <td rowspan="2" style="position:relative!important;; vertical-align: top!important;">
                <img style="position: absolute!important;top:0px!important;" src="{{ url('img/wezarat-logo.jpg') }}" height="100" width="100" alt="لوگو چپ">
            </td>

        <tr>
            <td></td>
            <td>
                <h3 class="title">وزارت تحصیلات عالی</h3>
                <h3 class="title">معینیت امور محصلان</h3>
                <h3 class="title">ریاست امور محصلان خصوصی</h3>
                <h3 class="title">آمریت فارغان</h3>
            </td>
            <td></td>
        </tr>
    </table>
</div>


<div class="info-bar">
    <span>ملاحظه شد: &emsp;   /  &emsp;    / &emsp;</span>
    <hr>
</div>

<p>
    @if ($archivedata)
        به ملاحظه سوابق تحصیلی محترم/محترم
        ({{ $archivedata->name }}) فرزند ({{ $archivedata->father_name }}) در سال ({{ $archivedata->year_of_inclusion }})
        شامل پوهنڅی
        @if ($archive && $archive->faculty)
            ({{ $archive->faculty->name }})
        @else
            (معلومه نه ده)
        @endif
        رشته
        @if ($archive && $archive->department)
            ({{ $archive->department->name }})
        @else
            (معلومه نه ده)
        @endif
        تایم درسی
       ( {{ $archivedata->time }})
        پوهنتون/ موسسه تحصیلات عالی خصوصی
        @if ($archive && $archive->university)
            ({{ $archive->university->name }})
        @else
            (معلومه نه ده)
        @endif
        گردیده و دروس نظری خویش را در سال
       ( {{ $archivedata->graduated_year }})
        به پایان رسانیده است مونوگراف خویش را به تاریخ
       ( {{ $archivedata->monograph_date }})
        دفاع نموده و به سویه
        @if ($archivedata && $archivedata->grade)
            ({{ $archivedata->grade->name }})
        @else
            (معلومه نه ده)
        @endif
        فارغ التحصیل می باشد.
    @endif
</p>

<div class="signature">
    <p>بااحترام</p>
    <p>ریس امور محصلان خصوصی</p>
</div>

</body>
</html>
