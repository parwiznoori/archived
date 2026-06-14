<?php

// تبدیل تاریخ میلادی به شمسی
function toShamsi($date, $format = 'Y/m/d')
{
    if (!$date) return '-';
    
    try {
        $timestamp = strtotime($date);
        return gregorianToJalali(date('Y', $timestamp), date('m', $timestamp), date('d', $timestamp), $format);
    } catch (\Exception $e) {
        return '-';
    }
}

// تبدیل تاریخ شمسی به میلادی
function toGregorian($shamsiDate)
{
    if (!preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/', $shamsiDate, $matches)) {
        return null;
    }
    
    $year = (int)$matches[1];
    $month = (int)$matches[2];
    $day = (int)$matches[3];
    
    return jalaliToGregorian($year, $month, $day);
}

// تبدیل میلادی به شمسی
function gregorianToJalali($gy, $gm, $gd, $format = 'Y/m/d')
{
    $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    
    if ($gy > 1600) {
        $jy = 979;
        $gy -= 1600;
    } else {
        $jy = 0;
        $gy -= 621;
    }
    
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) - 80 + $gd + $g_d_m[$gm - 1];
    $jy += 33 * ((int)($days / 12053));
    $days %= 12053;
    $jy += 4 * ((int)($days / 1461));
    $days %= 1461;
    
    if ($days > 365) {
        $jy += (int)(($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    
    $jz = ($days < 186) ? 1 : 7;
    $jm = ($days < 186) ? (int)(($days + 31) / 31) : (int)(($days - 186) / 30);
    $jd = ($days < 186) ? ($days % 31) + 1 : ($days - 186) % 30 + 1;
    $jm += $jz;
    
    $result = str_replace('Y', sprintf("%04d", $jy), $format);
    $result = str_replace('m', sprintf("%02d", $jm), $result);
    $result = str_replace('d', sprintf("%02d", $jd), $result);
    
    return $result;
}

// تبدیل شمسی به میلادی
function jalaliToGregorian($jy, $jm, $jd)
{
    $jy += 1595;
    $days = -355668 + (365 * $jy) + ((int)($jy / 33)) * 8 + ((int)((($jy % 33) + 3) / 4));
    
    if ($jm < 7) {
        $days += ($jm - 1) * 31;
    } else {
        $days += (($jm - 7) * 30) + 186;
    }
    
    $days += $jd;
    
    $gy = 400 * ((int)($days / 146097));
    $days %= 146097;
    
    if ($days > 36524) {
        $gy += 100 * ((int)(--$days / 36524));
        $days %= 36524;
        if ($days >= 365) {
            $days++;
        }
    }
    
    $gy += 4 * ((int)($days / 1461));
    $days %= 1461;
    
    if ($days > 365) {
        $gy += (int)(($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    
    $gd = $days + 1;
    $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    
    for ($gm = 0; $gm < 12; $gm++) {
        if ($g_d_m[$gm] >= $gd) {
            break;
        }
    }
    
    $gd -= $g_d_m[$gm - 1];
    
    return date('Y-m-d', strtotime($gy . '-' . $gm . '-' . $gd));
}

// گرفتن شروع ماه جاری شمسی
function getCurrentMonthStart()
{
    $now = date('Y-m-d');
    $shamsi = toShamsi($now, 'Y/m/d');
    $parts = explode('/', $shamsi);
    return $parts[0] . '/' . $parts[1] . '/01';
}

// گرفتن نام ماه شمسی
function getMonthName($shamsiDate)
{
    $parts = explode('/', $shamsiDate);
    $month = (int)$parts[1];
    
    $months = [
        1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
        4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
        7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
        10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
    ];
    
    return $months[$month] ?? '';
}