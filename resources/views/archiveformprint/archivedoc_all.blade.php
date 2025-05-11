<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Archive Document</title>
    <style>
        body {
            font-family: B Nazanin;
            /*font-family: B Nazanin, 'Times New Roman', serif;*/
            text-align: justify; /* Justify text alignment */
            margin: 0; /* Removes default body margin */
            padding-bottom: 70px; /* Prevent footer overlap */
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
        }

        .title-section {
            text-align: center;
            line-height: 1.15;
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
            font-size: 19px;
            line-height: 1.15;
            font-weight: bold;
        }

        p{
            margin: 0; /* Removes default body margin */
            padding-bottom: 5px; /* Prevent footer overlap */
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            padding: 10px 0;
            font-weight: bold;
        }

        .footer span {
            display: inline-block;
            margin: 0 15px; /* Add spacing between items */
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
        {{ $archivedatid }}
        <br>
        نېټه:
        @php
            $date1 = Date('Y-m-d');
            $jalali_date = explode('-', $date1);
            $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0], $jalali_date[1], $jalali_date[2]);
            $date = implode('/', $jDate);
            $time = Date('h:i:sa');
            echo $date . ' ه ش';
        @endphp
        <br>
        مطابق: &emsp;&emsp; / &emsp;&emsp;/ 1446 ه ق &emsp;&emsp;
    </span>
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    <span class="checkbox">
        <label><input type="checkbox"> عادی</label>&emsp;
        <label><input type="checkbox"> عاجل</label>&emsp;
        <label><input type="checkbox"> محرم</label>&emsp;
        <label><input type="checkbox"> اشد محرم</label>
        <hr>
    </span>

</div>

<!-- Main Content -->
<p>
    {!! $content !!}
</p>

<!-- Footer Section -->
<div class="footer">
    <hr>
    <span>آدرس: کارته چهار، ناحیه سوم، کابل- افغانستان</span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    <span>ویب‌ سایت: <a href="http://www.mohe.gov.af" target="_blank">www.mohe.gov.af</a></span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    <span>تلیفون: ۰۲۰۲۵۰۳۵۸۹</span>
</div>

</body>
</html>
