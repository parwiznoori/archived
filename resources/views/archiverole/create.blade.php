@extends('layouts.app')

@section('content')
    <section class="archive-assignment" aria-labelledby="archive-assignment-title">
        <header class="archive-assignment-header">
            <span class="archive-assignment-icon" aria-hidden="true"><i class="fa fa-tasks"></i></span>
            <h1 id="archive-assignment-title">{{ __('ثبت وظایف آرشیف') }}</h1>
        </header>

        {!! Form::open(['route' => 'archiverole.store', 'method' => 'post']) !!}
            <div class="archive-assignment-fields">
                <div class="archive-assignment-field @error('role_id') has-error @enderror">
                    {!! Form::label('role_id', trans('general.role')) !!}
                    {!! Form::select('role_id', $archiveRoles->pluck('title', 'id'), null, [
                        'class' => 'form-control',
                        'placeholder' => __('انتخاب وظیفه'),
                        'id' => 'role_id',
                    ]) !!}
                    @error('role_id')
                        <p class="archive-assignment-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="archive-assignment-field @error('university_id') has-error @enderror">
                    {!! Form::label('university_id', trans('general.university')) !!}
                    {!! Form::select('university_id', $universities, null, [
                        'class' => 'form-control',
                        'placeholder' => __('انتخاب پوهنتون'),
                        'id' => 'university_id',
                    ]) !!}
                    @error('university_id')
                        <p class="archive-assignment-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="archive-assignment-field @error('user_id') has-error @enderror">
                    {!! Form::label('user_id', trans('general.users')) !!}
                    {!! Form::select('user_id', $archiveUsers, null, [
                        'class' => 'form-control',
                        'data-url' => route('api.archiveUserRoleLoad'),
                        'placeholder' => __('انتخاب کاربر'),
                        'id' => 'user_id',
                    ]) !!}
                    @error('user_id')
                        <p class="archive-assignment-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="archive-assignment-books">
                    <div class="archive-assignment-field {{ $errors->has('archive_ids') || $errors->has('archive_ids.*') ? 'has-error' : '' }}">
                        <label for="archive_ids"><i class="fa fa-book" aria-hidden="true"></i> {{ trans('general.book_name') }}</label>
                        <select name="archive_ids[]" id="archive_ids" class="form-control" multiple
                                data-url="{{ route('api.archiveBookRoleLoadMultiple') }}"
                                aria-describedby="archive-assignment-book-help"></select>
                        <p id="archive-assignment-book-help" class="archive-assignment-help" aria-live="polite">
                            {{ __('ابتدا وظیفه و پوهنتون را انتخاب کنید') }}
                        </p>
                        @error('archive_ids')
                            <p class="archive-assignment-error">{{ $message }}</p>
                        @enderror
                        @error('archive_ids.*')
                            <p class="archive-assignment-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="archive-assignment-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-check" aria-hidden="true"></i> {{ trans('general.save') }}
                </button>
                <a href="{{ route('archiverole.index') }}" class="btn btn-default">{{ trans('general.cancel') }}</a>
            </div>
        {!! Form::close() !!}
    </section>
@endsection

@push('scripts')
<script>
    $(function () {
        const $role = $('#role_id');
        const $university = $('#university_id');
        const $user = $('#user_id');
        const $books = $('#archive_ids');
        const $bookHelp = $('#archive-assignment-book-help');
        const selectOptions = { language: 'fa', allowClear: true, width: '100%' };

        function updateAvailableFields() {
            const hasRole = Boolean($role.val());
            const canChooseBooks = hasRole && Boolean($university.val());

            $user.prop('disabled', !hasRole);
            $books.prop('disabled', !canChooseBooks);
            $bookHelp.text(canChooseBooks
                ? @js(__('نام کتاب را جستجو کنید. می‌توانید چندین کتاب را انتخاب کنید.'))
                : @js(__('ابتدا وظیفه و پوهنتون را انتخاب کنید')));
        }

        updateAvailableFields();

        $university.select2({
            ...selectOptions,
            placeholder: @js(__('انتخاب پوهنتون')),
        });

        $user.select2({
            ...selectOptions,
            placeholder: @js(__('انتخاب کاربر')),
            ajax: {
                url: () => $user.attr('data-url') + '/' + encodeURIComponent($role.val()),
                dataType: 'json',
                delay: 500,
                data: params => ({ q: params.term || '' }),
                processResults: data => ({ results: data }),
            },
        });

        $books.select2({
            ...selectOptions,
            placeholder: @js(__('جستجو و انتخاب کتاب‌ها')),
            ajax: {
                url: () => $books.attr('data-url') + '/' + encodeURIComponent($university.val()) + '/' + encodeURIComponent($role.val()),
                dataType: 'json',
                delay: 500,
                data: params => ({ q: params.term || '', page: params.page || 1 }),
                processResults: data => Array.isArray(data) ? { results: data } : data,
                cache: true,
            },
        });

        $role.add($university).on('change', function () {
            $books.val(null).trigger('change');
            $user.val(null).trigger('change');
            updateAvailableFields();
        });
    });
</script>
@endpush

@push('styles')
<style>
    .archive-assignment {
        --assignment-accent: #245d91;
        --assignment-border: #dce6ef;
        --assignment-tint: #f2f7fc;
        max-width: 900px;
        margin: 8px auto 24px;
        border: 1px solid var(--assignment-border);
        border-radius: 14px;
        background: #fff;
        color: #20354a;
        box-shadow: 0 8px 26px rgba(22, 45, 70, .08);
    }

    .archive-assignment-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px clamp(16px, 4vw, 30px);
        border-bottom: 1px solid var(--assignment-border);
        border-radius: 14px 14px 0 0;
        background: var(--assignment-tint);
    }

    .archive-assignment-icon {
        display: grid;
        place-items: center;
        flex: 0 0 42px;
        height: 42px;
        border-radius: 10px;
        background: var(--assignment-accent);
        color: #fff;
        font-size: 17px;
    }

    .archive-assignment-header h1 {
        margin: 0;
        font-size: clamp(20px, 3vw, 26px);
        font-weight: 700;
    }

    .archive-assignment-fields {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        padding: 24px clamp(16px, 4vw, 30px);
    }

    .archive-assignment-field {
        min-width: 0;
    }

    .archive-assignment-field > label {
        display: block;
        margin-bottom: 7px;
        font-weight: 700;
    }

    .archive-assignment-field :is(.form-control, .select2-selection) {
        min-height: 42px;
        border: 1px solid var(--assignment-border);
        border-radius: 8px;
        box-shadow: none;
    }

    .archive-assignment-field .select2-selection--single {
        display: flex;
        align-items: center;
        padding-inline: 4px;
    }

    .archive-assignment-field .select2-selection--single .select2-selection__rendered {
        width: 100%;
    }

    .archive-assignment-field .select2-selection--single .select2-selection__arrow {
        height: 100%;
    }

    .archive-assignment-field .select2-selection--multiple {
        padding-inline: 6px;
    }

    .archive-assignment-field .select2-selection--multiple .select2-selection__choice {
        max-width: 100%;
        border: 0;
        border-radius: 5px;
        background: #e6eff8;
        color: #234d73;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .archive-assignment-field .select2-selection--multiple .select2-search__field {
        max-width: 100%;
        min-height: 28px;
    }

    .archive-assignment-field :is(.form-control:focus, .select2-container--focus .select2-selection, .select2-container--open .select2-selection) {
        border-color: var(--assignment-accent);
        box-shadow: 0 0 0 3px rgba(36, 93, 145, .15);
    }

    .archive-assignment-field.has-error :is(.form-control, .select2-selection) {
        border-color: #b42318;
    }

    .archive-assignment-books {
        grid-column: 1 / -1;
        padding: 15px;
        border: 1px solid var(--assignment-border);
        border-radius: 10px;
        background: var(--assignment-tint);
    }

    .archive-assignment-books label i {
        margin-inline-end: 4px;
        color: var(--assignment-accent);
    }

    .archive-assignment-help,
    .archive-assignment-error {
        margin: 7px 0 0;
        font-size: 13px;
    }

    .archive-assignment-help {
        color: #53677d;
    }

    .archive-assignment-error {
        color: #b42318;
    }

    .archive-assignment-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 14px clamp(16px, 4vw, 30px);
        border-top: 1px solid var(--assignment-border);
        border-radius: 0 0 14px 14px;
        background: #f8fafc;
    }

    .archive-assignment-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 100px;
        min-height: 42px;
        border-radius: 8px !important;
    }

    @media (max-width: 767px) {
        .archive-assignment-fields {
            grid-template-columns: minmax(0, 1fr);
            gap: 16px;
        }

        .archive-assignment-actions .btn {
            flex: 1 1 auto;
        }
    }
</style>
@endpush
