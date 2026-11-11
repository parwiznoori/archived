@extends('layouts.app')

@push('styles')
    <style>
        .qc-page-bar, .qc-zoom-tools {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .qc-page-bar {
            justify-content: space-between;
            padding: 10px 16px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, .25);
        }
        .qc-page-bar a {
            display: inline-block;
            padding: 5px 14px;
            background: rgba(255, 255, 255, .18);
            color: #fff;
            border-radius: 6px;
        }
        .qc-page-bar a:hover, .qc-page-bar a:focus {
            background: rgba(255, 255, 255, .32);
        }
        .qc-review {
            display: grid;
            grid-template-columns: minmax(0, 58fr) minmax(0, 42fr);
            gap: 12px;
            height: calc(100vh - 160px);
            min-height: 320px;
        }
        .qc-scan {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
        }
        .qc-scan-viewport {
            flex: 1;
            min-height: 0;
            overflow: auto;
        }
        .qc-scan-viewport img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .qc-open-zoom {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            padding: 12px;
            border: 0;
            background: transparent;
            cursor: zoom-in;
        }
        .qc-open-zoom span {
            padding: 6px 10px;
            background: #1e293b;
            color: #fff;
            border-radius: 6px;
        }
        .qc-open-zoom:focus { outline-offset: -4px; }
        .qc-zoom-tools { display: none; }
        .qc-records {
            min-height: 0;
            overflow-y: auto;
            padding: 0 4px;
        }
        .qc-record-card {
            margin-bottom: 14px;
            overflow: hidden;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        }
        .qc-record-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            padding: 11px 14px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            font-weight: 700;
        }
        .qc-record-id {
            padding: 2px 9px;
            margin-inline-end: 4px;
            background: #2563eb;
            color: #fff;
            border-radius: 12px;
            font-size: 11px;
        }
        .qc-record-body { padding: 10px 14px; }
        .qc-record-fields { margin: 0; }
        .qc-field-row {
            display: grid;
            grid-template-columns: minmax(0, 40fr) minmax(0, 60fr);
            align-items: center;
            gap: 8px;
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .qc-field-row dt { color: #1e293b; }
        .qc-field-row dd {
            margin: 0;
            padding: 5px 10px;
            background: #f8fafc;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-weight: 600;
            overflow-wrap: anywhere;
        }
        .qc-record-form, .qc-page-form {
            display: grid;
            gap: 8px;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            background: #fef2f2;
        }
        .qc-record-form label, .qc-page-form label { margin: 0; }
        .qc-record-form textarea { resize: vertical; }
        .qc-record-form .btn, .qc-page-form .btn { justify-self: end; }
        .qc-page-form {
            margin: 0 0 10px;
            background: #fffbeb;
            border-color: #fde68a;
        }
        .qc-review.is-expanded {
            position: fixed;
            inset: 0;
            z-index: 9999;
            grid-template-columns: minmax(0, 75fr) minmax(0, 25fr);
            height: 100%;
            min-height: 0;
            gap: 0;
            background: #0f172a;
        }
        .qc-review.is-expanded .qc-scan {
            background: #1e293b;
            border: 0;
            border-radius: 0;
        }
        .qc-review.is-expanded .qc-scan-viewport img { height: auto; max-width: none; }
        .qc-review.is-expanded .qc-open-zoom { display: none; }
        .qc-review.is-expanded .qc-zoom-tools {
            display: flex;
            padding: 8px 12px;
            color: #fff;
        }
        .qc-review.is-expanded .qc-records {
            padding: 12px;
            background: #f8fafc;
        }
        @media (max-width: 767px) {
            .qc-review {
                grid-template-columns: minmax(0, 1fr);
                height: auto;
                min-height: 0;
            }
            .qc-scan { height: 50vh; }
            .qc-review.is-expanded {
                grid-template-columns: minmax(0, 1fr);
                grid-template-rows: minmax(0, 60fr) minmax(0, 40fr);
            }
            .qc-review.is-expanded .qc-scan { height: auto; }
        }
    </style>
@endpush

@section('content')
    @php
        $statusStyles = [1 => 'primary', 3 => 'success', 4 => 'danger'];
        $pageStatus = $archiveStatusList[$archiveqcheckimage->qc_status_id] ?? null;
    @endphp

    <div class="qc-page-bar">
        <nav aria-label="صفحات کتاب">
            @if($previousPageId)
                <a href="{{ route('archiveqcheckdata', ['archive_id' => $archive->id, 'image_archive_id' => $previousPageId]) }}">
                    <i class="fa fa-arrow-right" aria-hidden="true"></i> صفحه قبلی
                </a>
            @endif
            @if($nextPageId)
                <a href="{{ route('archiveqcheckdata', ['archive_id' => $archive->id, 'image_archive_id' => $nextPageId]) }}">
                    صفحه بعدی <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </a>
            @endif
        </nav>
        <span>صفحه {{ $archiveqcheckimage->book_pagenumber }} از {{ $totalPages }}</span>
        @if($pageStatus)
            <span class="badge badge-{{ $statusStyles[$archiveqcheckimage->qc_status_id] ?? 'default' }}">{{ $pageStatus }}</span>
        @endif
    </div>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="qc-review" id="qc-review">
        <div class="qc-scan">
            @if($archiveqcheckimage->path)
                <div class="qc-zoom-tools">
                    <button type="button" class="btn btn-danger btn-sm" data-qc-zoom="close">بستن</button>
                    <button type="button" class="btn btn-default btn-sm" data-qc-zoom="in" aria-label="بزرگ‌نمایی">+</button>
                    <button type="button" class="btn btn-default btn-sm" data-qc-zoom="out" aria-label="کوچک‌نمایی">−</button>
                    <button type="button" class="btn btn-default btn-sm" data-qc-zoom="reset">بازنشانی</button>
                    <bdi id="qc-zoom-level" aria-live="polite">100%</bdi>
                </div>
                <div class="qc-scan-viewport" id="qc-scan-viewport">
                    <img src="{{ asset($archiveqcheckimage->path) }}" alt="تصویر اسکن صفحه {{ $archiveqcheckimage->book_pagenumber }}">
                </div>
                <button type="button" class="qc-open-zoom" data-qc-zoom="open" aria-expanded="false" aria-controls="qc-scan-viewport">
                    <span><i class="fa fa-search-plus" aria-hidden="true"></i> بزرگ‌نمایی</span>
                </button>
            @else
                <p class="text-center">عکس کتاب موجود نیست</p>
            @endif
        </div>

        <section class="qc-records" aria-label="{{ trans('general.archivedata_list') }}">
            @if($archiveqcheckimage->status_id == 2)
                {!! Form::open(['route' => 'archiveqcheckImageSetStatus', 'method' => 'post', 'class' => 'qc-page-form']) !!}
                {!! Form::hidden('recordId', $archiveqcheckimage->id) !!}
                <label for="qc-page-status">تغییر حالت صفحه</label>
                {!! Form::select('approvalStatus', $archiveStatusList, $archiveqcheckimage->qc_status_id, [
                    'class' => 'form-control',
                    'id' => 'qc-page-status',
                    'placeholder' => trans('general.select'),
                ]) !!}
                <button type="submit" class="btn btn-success btn-sm">{{ trans('general.save') }}</button>
                {!! Form::close() !!}
            @else
                @forelse($archiveqcheckdata as $record)
                    @php
                        $fields = [
                            'general.name' => $record->name,
                            'general.last_name' => $record->last_name,
                            'general.father_name' => $record->father_name,
                            'general.grandfather_name' => $record->grandfather_name,
                            'general.school_name' => $record->school,
                            'archiveqc.school_graduation_year' => $record->school_graduation_year,
                            'general.tazkira_number' => $record->tazkira_number,
                            'general.birth_date' => $record->birth_date,
                            'general.birth_place' => $record->birth_place,
                            'general.time' => $record->time,
                            'general.kankor_id' => $record->kankor_id,
                            'general.half_year' => $record->semester_type->name ?? null,
                            'archiveqc.university_enrollment_year' => $record->year_of_inclusion,
                            'archiveqc.university_graduation_year' => $record->graduated_year,
                            'general.transfer_year' => $record->transfer_year,
                            'general.leave_year' => $record->leave_year,
                            'general.failled_year' => $record->failled_year,
                            'general.monograph_date' => $record->monograph_date,
                            'general.monograph_number' => $record->monograph_number,
                            'general.monograph_title' => $record->monograph_title,
                            'general.averageOfScores' => $record->averageOfScores,
                            'general.grade' => $record->grade->name ?? null,
                            'general.description' => $record->description,
                        ];
                        $comment = $record->qc_status_id == 4 ? $record->reject_comments : '';
                        if ($record->qc_status_id == 3) {
                            $comment = \App\Models\Archivedata::QC_APPROVAL_COMMENT;
                        }
                    @endphp

                    <article class="qc-record-card">
                        <header class="qc-record-header">
                            <span>
                                <bdi class="qc-record-id">#{{ $record->id }}</bdi>
                                {{ $record->name }} {{ $record->last_name }}
                            </span>
                            <span class="badge badge-{{ $statusStyles[$record->qc_status_id] ?? 'default' }}">{{ $record->qc_status }}</span>
                        </header>
                        <div class="qc-record-body">
                            <dl class="qc-record-fields">
                                @foreach($fields as $label => $value)
                                    <div class="qc-field-row">
                                        <dt>{{ trans($label) }}</dt>
                                        <dd><bdi>{{ $value === null || $value === '' ? '–' : $value }}</bdi></dd>
                                    </div>
                                @endforeach
                            </dl>
                            {!! Form::open(['route' => 'archiveqcheckdataSetStatus', 'method' => 'post', 'class' => 'qc-record-form']) !!}
                            {!! Form::hidden('recordId', $record->id) !!}
                            <label for="qc-record-status-{{ $record->id }}">{{ trans('general.accept_or_refuse') }}</label>
                            {!! Form::select('approvalStatus', $archiveStatusList, $record->qc_status_id, [
                                'class' => 'form-control qc-record-status',
                                'id' => 'qc-record-status-' . $record->id,
                            ]) !!}
                            <label for="qc-record-comment-{{ $record->id }}">جزئیات تأیید / دلیل رد شدن</label>
                            {!! Form::textarea('reject_comments', $comment, [
                                'class' => 'form-control',
                                'rows' => 4,
                                'id' => 'qc-record-comment-' . $record->id,
                            ]) !!}
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-check" aria-hidden="true"></i> {{ trans('general.save') }}
                            </button>
                            {!! Form::close() !!}
                        </div>
                    </article>
                @empty
                    <p class="alert alert-info">برای این صفحه معلومات محصلان ثبت نشده است.</p>
                @endforelse
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var review = document.getElementById('qc-review');
            var approvalComment = @json(\App\Models\Archivedata::QC_APPROVAL_COMMENT);

            review.querySelectorAll('.qc-record-status').forEach(function (status) {
                status.addEventListener('change', function () {
                    var comment = status.form.querySelector('[name="reject_comments"]');
                    comment.value = status.value === '3' ? approvalComment : '';
                });
            });

            var viewport = document.getElementById('qc-scan-viewport');
            if (!viewport) return;

            var image = viewport.querySelector('img');
            var openButton = review.querySelector('[data-qc-zoom="open"]');
            var closeButton = review.querySelector('[data-qc-zoom="close"]');
            var scaleLabel = document.getElementById('qc-zoom-level');
            var scale = 100;
            var previousOverflow;

            function setScale(value) {
                scale = Math.min(500, Math.max(50, value));
                image.style.width = scale + '%';
                scaleLabel.textContent = scale + '%';
            }

            function resetZoom() {
                setScale(100);
                viewport.scrollTop = 0;
                viewport.scrollLeft = 0;
            }

            function closeZoom() {
                review.classList.remove('is-expanded');
                openButton.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = previousOverflow;
                resetZoom();
                openButton.focus();
            }

            openButton.addEventListener('click', function () {
                previousOverflow = document.body.style.overflow;
                document.body.style.overflow = 'hidden';
                review.classList.add('is-expanded');
                openButton.setAttribute('aria-expanded', 'true');
                resetZoom();
                closeButton.focus();
            });
            closeButton.addEventListener('click', closeZoom);
            review.querySelector('[data-qc-zoom="in"]').addEventListener('click', function () {
                setScale(scale + 20);
            });
            review.querySelector('[data-qc-zoom="out"]').addEventListener('click', function () {
                setScale(scale - 20);
            });
            review.querySelector('[data-qc-zoom="reset"]').addEventListener('click', resetZoom);
            viewport.addEventListener('wheel', function (event) {
                if (!review.classList.contains('is-expanded') || !event.deltaY) return;
                event.preventDefault();
                setScale(scale + (event.deltaY < 0 ? 10 : -10));
            }, { passive: false });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && review.classList.contains('is-expanded')) {
                    closeZoom();
                }
            });
        }());
    </script>
@endpush
