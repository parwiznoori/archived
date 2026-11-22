@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/archive-gallery.css') }}?v={{ filemtime(public_path('css/archive-gallery.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="archive-gallery" id="archiveGallery" dir="rtl" tabindex="-1">
    <header class="ag-heading">
        <div><span class="ag-eyebrow">گالری صفحات کتاب</span><h2>{{ $archive->book_name }}</h2><p>{{ $archive->university->name ?? '—' }} <span>·</span> {{ $archive->images->count() }} صفحه</p></div>
        <a class="ag-button" href="{{ route('archive.index') }}">بازگشت به کتاب‌ها</a>
    </header>
    @if($archive->images->isEmpty())
        <div class="ag-empty"><i class="fa fa-picture-o" aria-hidden="true"></i><h3>هنوز عکسی برای این کتاب موجود نیست.</h3></div>
    @else
        @php
            $galleryPages = $archive->images->map(fn ($image) => [
                'page' => $image->book_pagenumber,
                'preview' => route('archive.page-preview', ['archiveId' => $archive->id, 'imageId' => $image->id]),
                'original' => asset(ltrim($image->path, '/')),
            ])->values();
        @endphp
        <section class="ag-reader" aria-label="نمایشگر صفحات کتاب">
            <div class="ag-toolbar">
                <div class="ag-page-title">صفحه <strong id="agPage">{{ $archive->images->first()->book_pagenumber }}</strong><span class="ag-muted" id="agPosition">۱ از {{ $archive->images->count() }}</span></div>
                <div class="ag-tools">
                    <button type="button" class="ag-button ag-icon" data-action="zoom-in" aria-label="بزرگ‌نمایی" title="بزرگ‌نمایی"><i class="fa fa-search-plus" aria-hidden="true"></i></button>
                    <button type="button" class="ag-button ag-icon" data-action="zoom-out" aria-label="کوچک‌نمایی" title="کوچک‌نمایی"><i class="fa fa-search-minus" aria-hidden="true"></i></button>
                    <button type="button" class="ag-button" data-action="reset">اندازه مناسب</button>
                    <button type="button" class="ag-button ag-icon" data-action="rotate" aria-label="چرخاندن عکس" title="چرخاندن عکس"><i class="fa fa-rotate-right" aria-hidden="true"></i></button>
                    <button type="button" class="ag-button ag-icon" data-action="fullscreen" aria-label="تمام‌صفحه" title="تمام‌صفحه"><i class="fa fa-expand" aria-hidden="true"></i></button>
                    <a class="ag-button" id="agOriginal" href="{{ $galleryPages[0]['original'] }}" target="_blank" rel="noopener">مشاهده عکس اصلی <i class="fa fa-external-link" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="ag-stage" id="agStage" aria-busy="true">
                <div class="ag-status" id="agStatus" role="status"><span class="ag-spinner"></span><span>در حال بارگذاری صفحه…</span></div>
                <div class="ag-canvas" id="agCanvas">
                    <img id="agImage" src="{{ $galleryPages[0]['preview'] }}" alt="صفحه {{ $galleryPages[0]['page'] }}" decoding="async" fetchpriority="high">
                </div>
                <div class="ag-error" id="agError" role="alert" hidden><i class="fa fa-picture-o" aria-hidden="true"></i><p>بارگذاری عکس ممکن نشد.</p><button type="button" class="ag-button" data-action="retry">تلاش دوباره</button><p class="ag-muted">می‌توانید از دکمه «مشاهده عکس اصلی» استفاده کنید.</p></div>
            </div>
            <div class="ag-navigation">
                <div class="ag-nav-buttons"><button type="button" class="ag-button" data-action="first" aria-label="صفحه اول">اول</button><button type="button" class="ag-button ag-primary" data-action="previous">صفحه قبلی</button><button type="button" class="ag-button ag-primary" data-action="next">صفحه بعدی</button><button type="button" class="ag-button" data-action="last" aria-label="صفحه آخر">آخر</button></div>
                <form id="agJump" class="ag-jump"><label for="agPageInput">رفتن به صفحه</label><input type="number" id="agPageInput" required min="1" value="{{ $galleryPages[0]['page'] }}" aria-label="شماره صفحه"><button type="submit" class="ag-button">برو</button></form>
                <button type="button" class="ag-button" data-action="play" aria-pressed="false" id="agPlay">شروع اسلایدشو</button>
            </div>
            <p class="ag-notice" id="agNotice" role="status" aria-live="polite"></p>
        </section>
        <section class="ag-page-picker"><div class="ag-picker-title"><h3>صفحات کتاب</h3><span class="ag-muted">برای مشاهده، شماره صفحه را انتخاب کنید.</span></div><div class="ag-page-list" aria-label="انتخاب صفحه">
            @foreach($archive->images as $image)
                <button type="button" class="ag-page-button {{ $loop->first ? 'is-active' : '' }}" data-page-index="{{ $loop->index }}" aria-label="صفحه {{ $image->book_pagenumber }}" aria-current="{{ $loop->first ? 'page' : 'false' }}">{{ $image->book_pagenumber }}</button>
            @endforeach
        </div></section>
        <script type="application/json" id="agData">{!! json_encode($galleryPages, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
        <script src="{{ asset('js/archive-gallery.js') }}?v={{ filemtime(public_path('js/archive-gallery.js')) }}" defer></script>
    @endif
</div>
@endsection