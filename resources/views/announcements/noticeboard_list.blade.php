@extends('layouts.app')

@section('content')
    <style>
        .todo-container {
            margin: 20px auto;
            padding: 20px;
            max-width: 800px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            direction: rtl; /* For right-to-left languages like Persian/Dari */
            font-family: 'Tahoma', sans-serif;
        }

        .todo-projects-container {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .todo-projects-container li {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .todo-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

    </style>

    <div class="todo-container">
        <div class="row">
            <div class="col-md-12">
                <!-- Add the image -->
                <ul class="todo-projects-container">
                    <li>به سیستم آرشیف محصلان وزارت تحصیلات عالی خوش آمدید!</li>
                </ul>

            </div>
            <img  src="{{ url('img/wezarat-logo.jpg') }}" class="todo-image">

        </div>
    </div>

    </div>

@endsection('content')

