@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
   
    

    <style>

        @import url(https://fonts.googleapis.com/css?family=Roboto+Condensed:700);
        
        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed, 
        figure, figcaption, footer, header, hgroup, 
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }
        
        body {
            background-color: #f2f2f2;
        }
        
        header {
            width: 100%;
            background-color: #77cdb4;
            text-align: center;
        }
        
        h1 {
            font-family: 'Roboto Condensed', sans-serif;
            color: #FFF;
            font-size: 2.3em;
        }
        
        em {
            color: #232027;
        }
        
        .wrapper {
            width: 100%;
            margin: 40px auto;
        }
        
        div.gallery {
            margin-top: 50px;
        }
        
        div.gallery ul {
            list-style-type: none;
            margin-left: 50px;
        }
        
        /* animation */
        div.gallery ul li, div.gallery li img {
            -webkit-transition: all 0.1s ease-in-out;
              -moz-transition: all 0.1s ease-in-out;
              -o-transition: all 0.1s ease-in-out;
              transition: all 0.1s ease-in-out;
        }
        
        div.gallery ul li {
            position: relative;
            float: left;
            width:750px;    
            height: 600px;
            margin: 1px;
            padding: 1px;
            z-index: 0;
        }
        
        /* Make sure z-index is higher on hover */
        /* Ensure that hover image overlapped the others */
        div.gallery ul li:hover {
            z-index: 0;
        }
        
        /* Image is position nicely under li */
        div.gallery ul li img {
            position: absolute;
            left: 0;
            top: 0;
            border: 1px solid #dddddd;
            padding: 5px;
            width: 750px;
            height:600px;
            background: #f0f0f0;
        }
        
         div.gallery ul li img:hover {
            width:  900px;
            height: 700px;
            margin-top: -100px;
            margin-left: -100px;
            
        } 
        
        p.attribution {
            font-family: 'Consolas';
            color: #000;
            clear: both;
            text-align: center;
            line-height: 25px;
            padding-top: 30px;
        }
        
        p.attribution a {
            color: #4c8d7c;
        }
        
        /* Responsive hack */
        @media only screen and (min-width: 499px) and (max-width: 1212px) {
            .wrapper {
                width: 500px;
            }
        }
        
        @media only screen and (max-width: 498px) {
            .wrapper {
                width: 300px;
            }
        
            div.gallery ul {
                list-style-type: none;
                margin: 0;
            }
        }

        
        </style>
</head>

<body>
    <header>
        <h1>عکس های (<em>{{ $archive->book_name }}</em>)  پوهنتون ( <em>{{ $archive->university->name }} </em> ) </h1>

    </header>


    <div class="wrapper">
		<div class="gallery">
			<ul>
				
                @foreach($archive->images as $image)
                <li><img src="{{ asset($image->path) }}"></li>
                @endforeach
			</ul>
		</div>
	</div>


</body>

</html>
@endsection
