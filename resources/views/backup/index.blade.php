@extends('layouts.app')

@section('content')
    <h2>نسخه‌های پشتیبان</h2>
    @if (session('message'))
        <div class="alert alert-info">{{ session('message') }}</div>
    @endif
    <form method="post" action="{{ route('archive-backup.store') }}">
        @csrf
        <button class="btn btn-primary" type="submit">ایجاد نسخهٔ پشتیبان</button>
    </form>
    <table class="table">
        <thead><tr><th>نام فایل</th><th>عملیات</th></tr></thead>
        <tbody>
            @forelse ($backups as $file)
                <tr>
                    <td>{{ $file }}</td>
                    <td>
                        <a class="btn btn-default" href="{{ route('archive-backup.download', $file) }}">دانلود</a>
                        <form method="post" action="{{ route('archive-backup.destroy', $file) }}"
                              style="display:inline" onsubmit="return confirm('این نسخهٔ پشتیبان حذف شود؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="2">هنوز نسخهٔ پشتیبان موجود نیست.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
