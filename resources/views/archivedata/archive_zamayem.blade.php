@extends('layouts.app')

@section('content')
<div class="portlet box">
    <div class="portlet-body">
        <div class="panel-body">
            {{-- <div class="container-fluid"> --}}
                <div class="card-header">
                    <h4>ضمایم کتاب فارغان : {{ $archive->name }}</h4>
                </div>
                <hr>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('archive_zamayem_update', $id) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="title">عنوان (اختیاری)</label>
                            <input type="text" class="form-control" id="title" name="title">
                        </div>

                        <div class="form-group">
                            {{-- <label for="zamayem_img">آپلود تصاویر</label> --}}
                            <input type="file" class="form-control-file" id="zamayem_img" name="zamayem_img[]" multiple required>
                            <small class="form-text text-muted">
                                می‌توانید چندین تصویر را همزمان آپلود کنید. حداکثر حجم هر تصویر: ۵ مگابایت
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary"> {{ trans('general.save') }}</button>
                         <a href="{{ route('archivedata.index') }}" class="btn btn-outline-secondary px-4 ml-2">
                                    {{ trans('general.cancel') }}
                          </a>
                    </form>

                    <hr>

                    
                    
                    @if($zamayems->isEmpty())
                        <div class="alert alert-info">هنوز تصویری آپلود نشده است.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">تصویر</th>
                                        <th width="25%">عنوان</th>
                                        <th width="20%">تاریخ آپلود</th>
                                        <th width="15%">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($zamayems as $index => $zamayem)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if ($zamayem->zamayem_img)
                                                    <a href="{{ asset($zamayem->zamayem_img) }}" target="_blank">
                                                        <img src="{{ asset($zamayem->zamayem_img) }}" class="img-thumbnail" width="100" alt="ضمیمه">
                                                    </a>
                                                @else
                                                    <span class="text-muted">بدون تصویر</span>
                                                @endif
                                            </td>
                                            <td>{{ $zamayem->title ?? 'بدون عنوان' }}</td>
                                            <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($zamayem->created_at)->format('Y/m/d H:i') }}</td>
                                            <td>
                                                <form action="{{ route('archive_zamayem.destroy', $zamayem->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('آیا از حذف این تصویر مطمئن هستید؟')">
                                                        حذف
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection