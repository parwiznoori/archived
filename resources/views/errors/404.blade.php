@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
{{-- @section('message', __('Not Found')) --}}
@section('message')


<h3 style="font-family:nazanin">{{__('Not Found') }}</h3>                    
<p>
    <a href="{{ url('/') }}" class="btn primary btn-outline"> بازگشت به صفحه اصلی </a>
</p>

@endsection
