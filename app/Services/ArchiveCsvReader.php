<?php

namespace App\Services;

use App\Exports\ArchiveExcelTemplateExport;
use Illuminate\Validation\ValidationException;

class ArchiveCsvReader
{
    public function rows(string $path): \Generator
    {
        $content = file_get_contents($path);
        if (str_starts_with($content, "\xFF\xFE") || str_starts_with($content, "\xFE\xFF")) {
            $content = mb_convert_encoding($content, 'UTF-8', str_starts_with($content, "\xFF\xFE") ? 'UTF-16LE' : 'UTF-16BE');
        }
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        if (!mb_check_encoding($content, 'UTF-8')) {
            throw ValidationException::withMessages(['csv_file' => 'فایل باید با رمزگذاری CSV UTF-8 ذخیره شود.']);
        }
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);
        try {
            $first = fgets($stream);
            if ($first === false) {
                throw ValidationException::withMessages(['csv_file' => 'فایل CSV خالی است.']);
            }
            $delimiter = ',';
            foreach ([';', "\t", ','] as $candidate) {
                if (count(str_getcsv($first, $candidate, '"', '')) > count(str_getcsv($first, $delimiter, '"', ''))) {
                    $delimiter = $candidate;
                }
            }
            rewind($stream);
            $expected = (new ArchiveExcelTemplateExport)->headings();
            $canonical = array_combine(array_map('strtolower', $expected), $expected);
            $headings = array_map(fn ($v) => $canonical[strtolower(trim($v ?? ''))] ?? trim($v ?? ''), fgetcsv($stream, 0, $delimiter, '"', ''));
            if (count($headings) !== count(array_unique($headings)) || array_diff($headings, $expected)) {
                throw ValidationException::withMessages(['csv_file' => 'عنوان ستون‌ها معتبر نیست. از قالب CSV استفاده کنید؛ نام ستون‌ها باید انگلیسی باشد.']);
            }
            $required = ['archiveimage_id', 'column_number', 'university_id', 'faculty_id', 'department_id', 'name', 'father_name', 'year_of_inclusion', 'graduated_year'];
            if ($missing = array_diff($required, $headings)) {
                throw ValidationException::withMessages(['csv_file' => 'ستون‌های ضروری موجود نیست: ' . implode('، ', $missing)]);
            }
            $line = 2;
            while (true) {
                $offset = ftell($stream);
                $values = fgetcsv($stream, 0, $delimiter, '"', '');
                if ($values === false) {
                    break;
                }
                $end = ftell($stream);
                $rowLine = $line;
                $line += max(1, substr_count(substr($content, $offset, $end - $offset), "\n"));
                $values = array_map(fn ($v) => trim($v ?? ''), $values);
                if (!array_filter($values, fn ($v) => $v !== '')) {
                    continue;
                }
                $extra = array_slice($values, count($headings));
                $row = array_fill_keys($expected, null);
                foreach ($headings as $index => $heading) {
                    $row[$heading] = ($values[$index] ?? '') === '' ? null : $values[$index];
                }
                yield [$rowLine, $row, (bool) array_filter($extra, fn ($v) => $v !== '')];
            }
        } finally {
            fclose($stream);
        }
    }
}
