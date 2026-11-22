<?php

namespace App\Services;

use App\Models\{Archive, Archivedata, ArchiveRole};
use Illuminate\Support\Facades\{DB, Log, Schema, Validator};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ArchiveCsvImporter
{
    public function import(string $path, int $archiveId, callable $authorize): array
    {
        $report = ['inserted' => 0, 'duplicate' => 0, 'error' => 0, 'empty' => 0, 'pages' => [], 'details' => [], 'total' => 0];
        $batch = (string) Str::uuid();
        $hasLog = Schema::hasTable('uploaded_csv_logs');
        // Validate the complete header before changing records.
        foreach ((new ArchiveCsvReader)->rows($path) as [$line, $row, $extra]) {
            $detail = ['row' => $line, 'page' => $row['archiveimage_id'], 'name' => $row['name'], 'status' => 'error', 'message' => ''];
            try {
                if ($extra) {
                    throw new \DomainException('ستون اضافی موجود است؛ جداکننده‌ها و علامت نقل‌قول متن را بررسی کنید.');
                }
                $this->validateRow($row, $archiveId);
                $result = DB::transaction(function () use ($row, $archiveId, $authorize, $hasLog, $batch) {
                    $archive = Archive::lockForUpdate()->findOrFail($archiveId);
                    $authorize($archive);
                    $image = $archive->images()->where('book_pagenumber', $row['archiveimage_id'])->orderBy('id')->lockForUpdate()->first();
                    if (!$image) {
                        throw new \DomainException('ستون archiveimage_id: این شماره صفحه در کتاب موجود نیست.');
                    }
                    if (in_array((int) $archive->qc_status_id, [2, 3, 4], true) || in_array((int) $image->qc_status_id, [2, 3, 4], true)) {
                        throw new \DomainException('صفحه یا کتاب به کنترل کیفیت معرفی شده و قابل تغییر نیست.');
                    }
                    $records = $image->archivedatas()->where('archive_id', $archiveId);
                    if ((int) $row['column_number'] === 0) {
                        if ($records->exists()) {
                            throw new \DomainException('ستون column_number: این صفحه محصل دارد و نمی‌تواند خالی ثبت شود.');
                        }
                        $image->update(['status_id' => 2, 'total_students' => 0]);
                        return ['empty', 'صفحه بدون محصل ثبت شد.'];
                    }
                    $identity = array_intersect_key($row, array_flip(['name', 'father_name', 'university_id', 'faculty_id', 'department_id']));
                    $existing = Archivedata::withTrashed()->where('archive_id', $archiveId)->where($identity)
                        ->where(function ($query) use ($row) {
                            $query->where('grandfather_name', $row['grandfather_name']);
                            if ($row['grandfather_name'] === null) {
                                $query->orWhere('grandfather_name', '');
                            }
                        })->first();
                    if ($existing && !$existing->trashed()) {
                        $page = $existing->archiveimage?->book_pagenumber ?? '—';
                        return ['duplicate', "محصل از قبل در صفحه {$page} موجود است؛ تکراری درج نشد."];
                    }
                    if ($occupied = $records->where('column_number', $row['column_number'])->first()) {
                        throw new \DomainException('ستون column_number: این شماره محصل در صفحه قبلاً استفاده شده است.');
                    }
                    $attributes = $row;
                    $attributes['archive_id'] = $archiveId;
                    $attributes['archiveimage_id'] = $image->id;
                    $attributes['status_id'] = 4;
                    $attributes['qc_status_id'] = 1;
                    $attributes['description'] = 'File Uploaded By System';
                    // A soft-deleted identity still occupies the database unique key.
                    if ($existing) {
                        $existing->fill($attributes);
                        $existing->deleted_at = null;
                        $existing->save();
                        $student = $existing;
                    } else {
                        $student = Archivedata::create($attributes);
                    }
                    if ($hasLog) {
                        DB::table('uploaded_csv_logs')->insert(['archivedata_id' => $student->id, 'batch_id' => $batch, 'created_at' => now()]);
                    }
                    $image->update(['status_id' => 4, 'total_students' => $image->archivedatas()->where('archive_id', $archiveId)->count()]);
                    $archive->update(['status_id' => 4]);
                    ArchiveRole::where('archive_id', $archiveId)->update(['status_id' => 4]);
                    return ['inserted', 'محصل در این صفحه درج شد.'];
                }, 3);
                [$detail['status'], $detail['message']] = $result;
            } catch (ValidationException $e) {
                $detail['message'] = implode('؛ ', $e->validator->errors()->all());
            } catch (\DomainException $e) {
                $detail['message'] = $e->getMessage();
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('Archive CSV database error', ['archive_id' => $archiveId, 'row' => $line, 'exception' => $e]);
                $detail['message'] = $e->getCode() === '23000'
                    ? 'شناسه یا مشخصات محصل با رکورد موجود تداخل دارد؛ ستون‌های kankor_id و مشخصات محصل را بررسی کنید.'
                    : 'ذخیره ردیف ممکن نشد؛ نوع و طول مقادیر ستون‌ها را بررسی کنید.';
            }
            $report['total']++;
            $report[$detail['status']]++;
            $page = (string) ($detail['page'] ?? '—');
            $report['pages'][$page] ??= ['inserted' => 0, 'duplicate' => 0, 'error' => 0, 'empty' => 0];
            $report['pages'][$page][$detail['status']]++;
            $report['details'][] = $detail;
        }
        if (!$report['total']) {
            throw ValidationException::withMessages(['csv_file' => 'فایل فقط عنوان ستون‌ها یا ردیف‌های خالی دارد؛ هیچ محصلی برای درج موجود نیست.']);
        }
        return $report;
    }

    private function validateRow(array &$row, int $archiveId): void
    {
        foreach (['archive_id', 'archiveimage_id', 'column_number', 'university_id', 'faculty_id', 'department_id', 'grade_id', 'semester_type_id', 'averageOfScores'] as $key) {
            if ($row[$key] !== null) {
                $row[$key] = strtr($row[$key], array_combine(preg_split('//u', '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', -1, PREG_SPLIT_NO_EMPTY), str_split('01234567890123456789')));
            }
        }
        $empty = $row['column_number'] === '0';
        $rules = [
            'archive_id' => ['nullable', 'integer', 'in:' . $archiveId],
            'archiveimage_id' => ['required', 'integer', 'min:1'],
            'column_number' => ['required', 'integer', 'min:0'],
        ];
        if (!$empty) {
            $rules += [
                'university_id' => ['required', 'integer', 'exists:universities,id'],
                'faculty_id' => ['required', 'integer', 'exists:faculties,id'],
                'department_id' => ['required', 'integer', 'exists:departments,id'],
                'grade_id' => ['nullable', 'integer', 'exists:grades,id'],
                'semester_type_id' => ['nullable', 'integer', 'exists:semester_type,id'],
                'name' => ['required', 'string', 'max:191'],
                'father_name' => ['required', 'string', 'max:191'],
                'year_of_inclusion' => ['required', 'string', 'max:191'],
                'graduated_year' => ['required', 'string', 'max:191'],
                'averageOfScores' => ['nullable', 'numeric', 'between:0,100'],
            ];
            foreach (['last_name', 'grandfather_name', 'school', 'school_graduation_year', 'tazkira_number', 'birth_date', 'birth_place', 'time', 'kankor_id', 'year_of_inclusion', 'graduated_year', 'transfer_year', 'leave_year', 'failled_year', 'monograph_date', 'monograph_number', 'monograph_title'] as $field) {
                $rules[$field] ??= ['nullable', 'string', 'max:191'];
            }
        }
        $labels = array_combine(array_keys($rules), array_keys($rules));
        foreach (['name'=>'نام', 'father_name'=>'نام پدر', 'grandfather_name'=>'نام پدرکلان', 'archiveimage_id'=>'شماره صفحه', 'column_number'=>'شماره محصل', 'university_id'=>'پوهنتون', 'faculty_id'=>'پوهنځی', 'department_id'=>'دیپارتمنت', 'year_of_inclusion'=>'سال شمولیت', 'graduated_year'=>'سال فراغت', 'averageOfScores'=>'اوسط نمرات'] as $field => $label) {
            $labels[$field] = "$field ($label)";
        }
        Validator::make($row, $rules, [
            'required' => 'ستون :attribute خالی است؛ مقدار آن را وارد کنید.',
            'integer' => 'ستون :attribute باید عدد صحیح باشد.',
            'exists' => 'ستون :attribute: این شناسه در سیستم موجود نیست.',
            'in' => 'ستون :attribute باید شناسه همین کتاب باشد.',
            'min' => 'مقدار ستون :attribute باید حداقل :min باشد.',
            'max' => 'ستون :attribute نباید بیشتر از :max حرف باشد.',
            'numeric' => 'ستون :attribute باید عدد باشد.',
            'between' => 'ستون :attribute باید بین :min و :max باشد.',
        ], $labels)->validate();
        if (!$empty) {
            $archive = Archive::findOrFail($archiveId);
            if ((int) $row['university_id'] !== (int) $archive->university_id) {
                throw new \DomainException('ستون university_id: پوهنتون با پوهنتون این کتاب مطابقت ندارد.');
            }
            if (!DB::table('faculties')->where('id', $row['faculty_id'])->where('university_id', $row['university_id'])->exists()) {
                throw new \DomainException('ستون faculty_id: پوهنځی مربوط به پوهنتون انتخاب‌شده نیست.');
            }
            if (!DB::table('departments')->where('id', $row['department_id'])->where('faculty_id', $row['faculty_id'])->exists()) {
                throw new \DomainException('ستون department_id: دیپارتمنت مربوط به پوهنځی انتخاب‌شده نیست.');
            }
        }
    }
}
