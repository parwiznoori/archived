
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Archive Document</title>
    <style>
        body {
            /*font-family: nazanin;*/
            /*font-family: 'times new roman', sans-serif !important;*/
            /*line-height: 1.15;*/
            direction: rtl; /* Right-to-left text direction */
            text-align: justify; /* Justify text alignment */
            margin: 0; /* Removes default body margin */
        }

        .header {
            margin-bottom: 20px;
        }

        .logo-cell {
            width: 15%; /* Adjust width for logos */
            text-align: center;
            vertical-align: middle;
        }

        .logo {
            height: 150px;
            width: 150px; /* Maintain aspect ratio */
            display: block;
            margin: auto;
            position: relative;
        }

        .title-section {
            text-align: center;
            vertical-align: middle;
            padding: 10px;
        }

        .title {
            font-size: 30px;
            margin: 5px 0;
            font-weight: bold;
        }

        .title-2 {
            font-size: 23px;
            margin: 3px 0;
            font-weight: bold;
        }

        .info-bar {
            text-align: right;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
        }

        .checkbox {
            font-size: 13px;
            font-weight: bold;
        }

        .title-3 span {
            font-size: 19px;  /* Set font size to 19px */
            line-height: 1.15;  /* Set line spacing to 1.15 */
            font-weight: bold;
        }

        p {
            font-size: 19px;  /* Set font size to 19px */
            line-height: 1.15;  /* Set line spacing to 1.15 */
        }

        .signature {
            margin-top: 40px;
            text-align: center;
            font-size: 19px;  /* Set font size to 19px */
            line-height: 1.15;  /* Set line spacing to 1.15 */
            font-weight: bold;
        }

        .doc-copy {
            margin-top: 70px;
            text-align: right;
            font-size: 14px;  /* Set font size to 14px */
            line-height: 1;  /* Set line spacing to 1 */
        }

        .footer {
            margin-top: 295px; /* Adjusted space above footer */
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
    </style>
</head>
<body>

<!-- Header Section with Logos and Centered Title -->
<div class="header">
    <table class="table table-border w-100">
        <tr>
            <!-- Right Logo -->
            <td rowspan="2" class="logo-cell">
                <img src="{{ url('img/emarat-logo.jpg') }}" alt="لوگو راست" class="logo">
            </td>
            <!-- Spacer Cells -->
            <td></td>
            <td></td>
            <td></td>
            <!-- Left Logo -->
            <td rowspan="2" class="logo-cell">
                <img src="{{ url('img/wezarat-logo.jpg') }}" alt="لوگو چپ" class="logo">
            </td>
        </tr>
        <tr>
            <!-- Title Section -->
            <td colspan="3" class="title-section">
                <h3 class="title"><u>بسم الله الرحمن الرحیم</u></h3>
                <h3 class="title">د افغانستان اسلامی امارت</h3>
                <h3 class="title">د لوړ و زده کړو وزارت</h3>
                <h3 class="title-2">دمحصلانو چارو معینیت</h3>
                <h3 class="title-2">دخصوصي محصلانو چارو ریاست</h3>
                <h3 class="title-2">دډیټابیس آمریت</h3>
            </td>
        </tr>
    </table>
</div>

<!-- Info Bar -->
<div class="info-bar">
    <span>
        ګنه :
        <br>
    نېټه:
{{--       @php--}}
{{--           $date1 = Date('Y-m-d');--}}
{{--           $jalali_date=explode('-',$date1);--}}
{{--           $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);--}}
{{--           $date=implode('/',$jDate);--}}

{{--           $time = Date('h:i:sa');--}}
{{--           echo $date. ' ه ش';--}}
{{--       @endphp--}}
        <br>
        مطابق: &emsp;&emsp; / &emsp;&emsp;/ 1446 ه ق&emsp;&emsp;

    </span>
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;


    <span class="checkbox">
        <label><input type="checkbox"> عادی</label>&emsp;
        <label><input type="checkbox"> عاجل</label>&emsp;
        <label><input type="checkbox"> محرم</label>&emsp;
        <label><input type="checkbox"> اشد محرم</label>
    </span>
    <hr>
</div>

<!-- Main Content -->
<div class="title-3">
    <span>د پوهنی محترم وزارت</span><br>
    <span>د بشری سرچینولوی ریاست</span><br>
    <span>السلام علیکم ورحمة الله وبرکاته</span><br>
    <span><u>موضوع: د فراغت تائیدی</u></span>
</div>
<p>
    ستاسود ( &emsp;&emsp;&emsp;&emsp;&emsp; )
    گڼه او ( &emsp;&emsp;&emsp;&emsp;&emsp;) نیټی مکتوب ځواب کی احتراماَ لیکو:
</p>

<p>
    @if ($archivedata)
        تحصیلی اسنادو ته په کتني سره محترم
        {{ $archivedata->name }},
        د
        {{ $archivedata->father_name }}
        زوی/لور په
        {{ $archivedata->year_of_inclusion }}
        کال کی د
        @if ($archive && $archive->university)
            {{ $archive->university->name }}
        @else
            (معلومه نه ده)
        @endif
        پوهنتون/مؤسسی د
        @if ($archive && $archive->faculty)
            {{ $archive->faculty->name }}
        @else
            (معلومه نه ده)
        @endif
        پوهنځی ته شامل او خپلی زده کړي یی په
        {{ $archivedata->graduated_year }}
        کال کی د
        @if ($archive && $archive->department)
            {{ $archive->department->name }}
        @else
            (معلومه نه ده)
        @endif
        رشتی څخه په ورځنی وخت کی پای ته رسولی.
    @else
        (معلومه نه ده)
    @endif
</p>

<!-- Signature -->
<div class="signature">
    په درنښت
    <br>
    انجنیر محمد سـلیم "افغان"<br>
    د خصوصی محصلانو رئیس
</div>

<div class="doc-copy">
    کاپی:
    <br>
</div>

<!-- Footer Section -->
<div class="footer">
    <hr>
    <span>آدرس: کارته چهار، ناحیه سوم، کابل- افغانستان</span>
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    <span>ویب‌سایت: <a href="http://www.mohe.gov.af" target="_blank">www.mohe.gov.af</a></span>
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    <span>تلیفون: ۰۲۰۲۵۰۳۵۸۹</span>
</div>

</body>
</html>
