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
            margin: 0; /* Set body margin to 0 */
        }
        .header {
            display: flex;
            justify-content: space-between; /* Align items to the left and right */
            align-items: center; /* Center items vertically */
            margin-bottom: 3px;
        }
        .header img {
            width: 100px;
            height: 100px;
            margin: 0; /* Ensure images have no margin */
        }
        h3 {
            margin: 0; /* Remove default margin */
        }
        p {
            font-size: 19px;  /* Set font size to 19px */
            line-height: 1.15;  /* Set line spacing to 1.15 */
        }
        .title {
            margin: 0; /* Remove default margin */
            font-size: 16px;
        }
        .title-2 {
            margin: 0; /* Remove default margin */
            font-size: 16px;
        }

        .title-3 span {
            font-size: 19px;  /* Set font size to 19px */
            line-height: 1.15;  /* Set line spacing to 1.15 */
            font-weight: bold;
        }

        .info-bar {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
        }
        .signature {
            margin-top: 40px;
            text-align: center;
            font-size: 19px;  /* Set font size to 19px */
            line-height: 1.15;  /* Set line spacing to 1.15 */
            font-weight: bold;
        }
        .footer {
            margin-top: 400px; /* Adjusted space above footer */
            text-align: center; /* Center footer text */
            font-size: 12px; /* Slightly smaller font for footer */
            display: flex; /* Use flexbox for alignment */
            justify-content: center; /* Center items */
            font-weight: bold;
        }
        .footer span {
            margin: 0 10px; /* Add some space between footer items */
        }
        td {
            text-align: center;
        }
        table{
            width: 100%!important;
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
                <h6 class="title">د افغانستان اسلامی امارت</h6>
                <h6 class="title">د لوړ و زده کړو وزارت</h6>
            </td>
            <td></td>
            <td>
                <h6 class="title">ISLAMIC EMIRATE OF AFGHANISTAN</h6>
                <h6 class="title">MINISTRY OF HIGHER EDUCATION</h6>
            </td>
            <td rowspan="2" style="position:relative!important;; vertical-align: top!important;">
                <img style="position: absolute!important;top:0px!important;" src="{{ url('img/wezarat-logo.jpg') }}" height="100" width="100" alt="لوگو چپ">
            </td>
        </tr>
        <tr  class="w-100">
            <td colspan="3" >
                <h5 class="title-2">د خصوصي محصلانو چارو ریاست</h5>
                <h5 class="title-2">Directorate of Private Universities Students’ Affairs</h5>
                <h5 class="title-2">د فارغانو آمریت</h5>
            </td>

        </tr> <!-- Closing the second row -->
    </table>
</div>

<div class="info-bar">
    <span>ګڼه:..............</span>
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    <span>نېټه: &emsp; / &emsp;&emsp;/ 1446 ه ق</span>&emsp;&emsp;
    <hr>
</div>


<div class="title-3">
    <span>د بهرنیو چارو محترم وزارت ته!</span><br>
    <span>د قونسلیو چارو د محترم ریاست د پام وړ!</span><br>
    <span>السلام علیکم ورحمة الله وبرکاته</span><br>
    <span><u>موضوع: د فراغت تائیدی</u></span>
</div>

<p>
    @if ($archivedata)
        تحصیلي اسنادو ته په کتنې سره محترمه
       ( {{ $archivedata->name }})
        د
        ({{ $archivedata->father_name }})
        زوی/لور په
        ({{ $archivedata->year_of_inclusion }})
        کال کی د
        @if ($archive && $archive->university)
            ({{ $archive->university->name }})
        @else
            (معلومه نه ده)
        @endif
        پوهنتون/مؤسسی د
        @if ($archive && $archive->faculty)
            ({{ $archive->faculty->name }})
        @else
            (معلومه نه ده)
        @endif
        پوهنځی ته شامل او خپلی زده کړي یی په
        ({{ $archivedata->graduated_year }} )
        کال کی د
        @if ($archive && $archive->department)
            ({{ $archive->department->name }})
        @else
            (معلومه نه ده)
        @endif
        رشتی څخه په ورځنی وخت کی پای ته رسولی. نوموړې خپل پایلیک/ستاژ لیکلی
        او په
        ({{ $archivedata->monograph_date }} )
        نیټه کی یې تکمیل او دفاع کړی.
        چی وروسته له دفاع څخه د لیسانس په کچه فارغ شوی.
    @else
        (معلومه نه ده)
    @endif
</p>

<div class="signature">
    <p>په درنښت</p>
    انجنیر محمد سـلیم "افغان"<br>
    د خصوصی محصلانو رئیس
</div>

<!-- Footer Section -->
<div class="footer">
    <hr>
    <span>آدرس: کارته چهار، ناحیه سوم، کابل- افغانستان</span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    <span>ویب‌ سایت: <a href="http://www.mohe.gov.af" target="_blank">www.mohe.gov.af</a></span>
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    <span>تلیفون: ۰۲۰۲۵۰۳۵۸۹</span>
</div>

</body>
</html>
