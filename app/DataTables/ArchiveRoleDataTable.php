<?php

namespace App\DataTables;

use App\Models\ArchiveRole;
use Assert\InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Services\DataTable;

class ArchiveRoleDataTable extends DataTable
{
    private const TEXT_COLUMNS = [
        'user_name' => ['user', 'name'],
        'book_name' => ['archive', 'book_name'],
        'university_name' => ['archive.university', 'name'],
        'role_title' => ['role', 'title'],
    ];

    private const STATUSES = [
        'status_id' => [
            1 => ['حالت معمولی', ''],
            2 => ['صرف نظر', 'badge-primary'],
            3 => ['ناتکمیل', 'badge-warning'],
            4 => ['تکمیل', 'badge-success'],
        ],
        'qc_status_id' => [
            1 => ['حالت معمولی', ''],
            2 => ['پروسس', 'badge-primary'],
            3 => ['تايید', 'badge-success'],
            4 => ['رد', 'badge-danger'],
        ],
    ];

    public function query(): Builder
    {
        return ArchiveRole::query()->with(['user:id,name', 'archive.university:id,name', 'role:id,title']);
    }

    public function dataTable(Builder $query)
    {
        $table = datatables()->eloquent($query)
            ->addColumn('user_name', fn (ArchiveRole $assignment) => $assignment->user->name ?? 'N/A')
            ->addColumn('book_name', fn (ArchiveRole $assignment) => $assignment->archive->book_name ?? 'N/A')
            ->addColumn('university_name', fn (ArchiveRole $assignment) => $assignment->archive->university->name ?? 'N/A')
            ->addColumn('role_title', fn (ArchiveRole $assignment) => $assignment->role->title ?? 'N/A')
            ->editColumn('created_at', fn (ArchiveRole $assignment) => $assignment->created_at
                ? Jalalian::fromCarbon($assignment->created_at)->format('Y/m/d')
                : 'N/A')
            ->filterColumn('created_at', $this->filterDate(...))
            ->orderColumn('created_at', fn (Builder $query, string $direction) => $query
                ->orderBy('archive_entry_users.created_at', $direction)
                ->orderBy('archive_entry_users.id', $direction))
            ->filter(function (Builder $query) {
                $keyword = trim(request()->string('search.value')->toString());

                if ($keyword === '') {
                    return;
                }

                $query->where(function (Builder $query) use ($keyword) {
                    foreach (self::TEXT_COLUMNS as [$relation, $field]) {
                        $query->orWhereHas($relation, fn (Builder $related) => $related->where($field, 'like', "%{$keyword}%"));
                    }
                });
            });

        foreach (self::TEXT_COLUMNS as $column => [$relation, $field]) {
            $table->filterColumn($column, fn (Builder $query, string $keyword) => $query
                ->whereHas($relation, fn (Builder $related) => $related->where($field, 'like', "%{$keyword}%")));
        }

        foreach (self::STATUSES as $column => $statuses) {
            $table->editColumn($column, fn (ArchiveRole $assignment) => $this->statusBadge($assignment->archive?->{$column}, $statuses))
                ->filterColumn($column, function (Builder $query, string $keyword) use ($column, $statuses) {
                    $ids = array_keys(array_filter($statuses, fn (array $status, int $id) =>
                        (string) $id === $keyword || str_contains(__($status[0]), $keyword), ARRAY_FILTER_USE_BOTH));

                    $query->whereHas('archive', fn (Builder $archive) => $archive->whereIn($column, $ids));
                });
        }

        $columns = array_keys($this->getColumns());

        return $table->rawColumns(array_keys(self::STATUSES))
            ->whitelist($columns)
            ->only($columns);
    }

    private function statusBadge(?int $id, array $statuses): string
    {
        if (!$id) {
            return '';
        }

        [$label, $class] = $statuses[$id] ?? ['N/A', ''];

        return $class === ''
            ? e(__($label))
            : '<span class="' . $class . ' m-1 p-1">' . e(__($label)) . '</span>';
    }

    private function filterDate(Builder $query, string $keyword): void
    {
        $keyword = CalendarUtils::convertNumbers(str_replace('/', '-', $keyword), true);

        if (!preg_match('/^(\d{4})(?:-(\d{1,2})(?:-(\d{1,2}))?)?$/D', $keyword, $parts)) {
            $query->whereRaw('1 = 0');
            return;
        }

        try {
            $date = new Jalalian((int) $parts[1], (int) ($parts[2] ?? 1), (int) ($parts[3] ?? 1));
            $start = $date->toCarbon()->startOfDay();
            $end = match (count($parts)) {
                2 => $date->addYears(1)->toCarbon(),
                3 => $date->addMonths(1)->toCarbon(),
                4 => $start->copy()->addDay(),
            };
        } catch (InvalidArgumentException) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->where('archive_entry_users.created_at', '>=', $start)
            ->where('archive_entry_users.created_at', '<', $end);
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax(route('archiverole.index'))
            ->parameters(array_merge($this->getBuilderParameters(), [
                'dom' => '<"top"Blf>rt<"bottom"ip>',
                'order' => [[7, 'desc']],
            ]));
    }

    protected function getColumns(): array
    {
        return [
            'id' => ['title' => trans('general.id'), 'footer' => ['class' => 'numeric-filter']],
            'user_name' => ['title' => trans('general.user_name'), 'orderable' => false],
            'book_name' => ['title' => trans('general.book_name'), 'orderable' => false],
            'university_name' => ['title' => trans('general.university'), 'orderable' => false],
            'role_title' => ['title' => trans('general.role'), 'orderable' => false],
            'status_id' => ['title' => trans('general.status'), 'orderable' => false],
            'qc_status_id' => ['title' => trans('general.accept_or_refuse'), 'orderable' => false],
            'created_at' => ['title' => trans('تاریخ ثبت وظایف'), 'footer' => ['class' => 'hijri-date-filter']],
        ];
    }
}
