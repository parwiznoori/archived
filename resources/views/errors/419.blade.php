@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message')


<h3 style="font-family:nazanin">{{__('Page Expired') }}</h3>                    
<p>
    <a href="{{ url('/') }}" class="btn primary btn-outline"> بازگشت به صفحه اصلی </a>
</p>

@endsection
