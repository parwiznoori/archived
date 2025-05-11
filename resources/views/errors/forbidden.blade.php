<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" dir="rtl">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title>{{ config('app.name', 'Sib') }}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        {!! Html::style('css/all.css') !!}                 
        {!! Html::style('css/error-rtl.css') !!}                 
    </head>
    <!-- END HEAD -->
    <body class=" page-500-full-page">
        <div class="row">
            <div class="col-md-12 page-500">
                <div class=" number font-red"> <i class="fa fa-ban" aria-hidden="true" style="top: 20px;position: relative;"></i> </div>
                <div class=" details">
                    <h3 style="font-family:nazanin">شما اجازه دسترسی به این صفحه را ندارید!</h3>                    
                    <p>
                        <a href="{{ /redirect()->back() }}" class="btn red btn-outline"> بازگشت به صفحه اصلی </a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>