@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js" defer></script>
    
    <style>
        @import url(https://fonts.googleapis.com/css?family=Roboto+Condensed:700);
        
        /* CSS Reset */
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
            box-sizing: border-box;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: #f2f2f2;
            font-family: 'Roboto Condensed', sans-serif;
        }
        
        header {
            width: 100%;
            background-color: #77cdb4;
            text-align: center;
            padding: 2rem 1rem;
            margin-bottom: 2rem;
        }
        
        h1 {
            color: #FFF;
            font-size: clamp(1.5rem, 4vw, 2.3rem);
            line-height: 1.4;
        }
        
        em {
            color: #232027;
            font-style: italic;
        }
        
        .wrapper {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Slideshow Container - Portrait Orientation */
        .slideshow-container {
            position: relative;
            width: 100%;
            height: 85vh; /* Taller for portrait */
            min-height: 700px;
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f0f0;
        }
        
        .slide.active {
            opacity: 1;
            z-index: 1;
        }
        
        /* Portrait image styling */
        .slide-image {
            max-width: 100%; /* More width for portrait */
            max-height: 90%;
            width: auto;
            height: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        /* Error message styling */
        .error-message {
            color: #ff6b6b;
            font-size: 1.2rem;
            text-align: center;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            border: 2px solid #ff6b6b;
            font-family: 'Roboto Condensed', sans-serif;
            max-width: 80%;
        }
        
        /* Navigation Buttons */
        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            border: none;
            color: white;
            font-size: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .nav-btn:hover {
            background: rgba(0,0,0,0.8);
            transform: translateY(-50%) scale(1.1);
        }
        
        .prev-btn {
            left: 20px;
        }
        
        .next-btn {
            right: 20px;
        }
        
        /* Slide Indicators */
        .slide-indicators {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 10px;
            z-index: 10;
        }
        
        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .indicator.active {
            background: white;
            transform: scale(1.2);
        }
        
        .indicator:hover {
            background: rgba(255,255,255,0.8);
        }
        
        /* Slide Counter */
        .slide-counter {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            z-index: 10;
        }
        
        /* Controls */
        .slideshow-controls {
            display: flex;
            justify-content: center;
            gap: 0.8rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .control-btn {
            background: #77cdb4;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            min-width: 120px;
        }
        
        .control-btn:hover {
            background: #5bb598;
            transform: translateY(-2px);
        }
        
        .control-btn:disabled {
            background: #cccccc;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Special buttons for first/last */
        .control-btn.special {
            background: #4a9c8a;
            font-weight: bold;
        }
        
        .control-btn.special:hover {
            background: #3d8273;
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #77cdb4;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Debug info */
        .debug-info {
            background: #fff;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 8px;
            border-left: 4px solid #77cdb4;
            font-family: monospace;
            font-size: 0.9rem;
        }
        
        /* Progress Bar */
        .progress-bar {
            width: 100%;
            height: 4px;
            background: rgba(255,255,255,0.3);
            position: absolute;
            top: 0;
            left: 0;
            z-index: 10;
        }
        
        .progress {
            height: 100%;
            background: #77cdb4;
            width: 0%;
            transition: width 0.1s linear;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .slideshow-container {
                height: 60vh;
                min-height: 500px;
            }
            
            .nav-btn {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
            
            .prev-btn {
                left: 10px;
            }
            
            .next-btn {
                right: 10px;
            }
            
            .error-message {
                font-size: 1rem;
                padding: 1.5rem;
            }
            
            .control-btn {
                padding: 8px 16px;
                font-size: 0.8rem;
                min-width: 100px;
            }
            
            .slideshow-controls {
                gap: 0.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .slideshow-container {
                height: 50vh;
                min-height: 400px;
            }
            
            .nav-btn {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            
            .error-message {
                font-size: 0.9rem;
                padding: 1rem;
            }
            
            .control-btn {
                padding: 6px 12px;
                font-size: 0.7rem;
                min-width: 80px;
            }
            
            .slideshow-controls {
                gap: 0.3rem;
            }
        }
    </style>
</head>

<body>
   <header>
    <h1>عکس های کتاب (<em>{{ $archive->book_name }}</em>) پوهنتون (<em>{{ $archive->university->name }}</em>)</h1>
    <div style="margin-top: 1rem; color: rgb(0, 0, 0); font-size: 2.0rem;">
        <strong>تعداد صفحات: {{ count($archive->images) }}</strong>
    </div>
</header>

    <div class="wrapper">
        <!-- Slideshow Container -->
        <div class="slideshow-container" id="slideshow">
            <div class="progress-bar">
                <div class="progress" id="progress"></div>
            </div>
            
            <!-- Slides will be dynamically inserted here -->
            
            <!-- Navigation Buttons -->
            <button class="nav-btn prev-btn" onclick="changeSlide(-1)">‹</button>
            <button class="nav-btn next-btn" onclick="changeSlide(1)">›</button>
            
            <!-- Slide Counter -->
            <div class="slide-counter">
                <span id="current-slide">1</span> / <span id="total-slides">{{ count($archive->images) }}</span>
            </div>
            
            <!-- Slide Indicators -->
            <div class="slide-indicators" id="indicators">
                <!-- Indicators will be dynamically inserted here -->
            </div>
        </div>
        
        <!-- Controls -->
        <div class="slideshow-controls">
           
             <button class="control-btn special" onclick="goToFirstSlide()">⏮️ صفحه اول</button>
            <button class="control-btn" onclick="changeSlide(-1)">قبلی</button>
            <button class="control-btn" onclick="changeSlide(1)">بعدی</button>
            <button class="control-btn special" onclick="goToLastSlide()">⏭️ صفحه آخر</button>

             <button class="control-btn" onclick="startSlideshow()" id="start-btn">شروع اسلایدشو</button>
            <button class="control-btn" onclick="stopSlideshow()" id="stop-btn" disabled>توقف اسلایدشو</button>
        </div>
    </div>

    <script>
        // Configuration
        const config = {
            images: @json($archive->images->pluck('path')),
            preloadCount: 3,
            slideDuration: 5000,
            lazyLoadThreshold: 5
        };

        // Debug log
        console.log('Images config:', config.images);
        console.log('Total images:', config.images.length);

        let currentSlide = 0;
        let slideshowInterval;
        let isSlideshowRunning = false;
        let loadedImages = new Set();

        // Initialize slideshow
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing slideshow...');
            
            if (config.images.length === 0) {
                showNoImagesMessage();
                return;
            }
            
            initializeSlideshow();
            loadInitialSlides();
            createIndicators();
        });

        function showNoImagesMessage() {
            const slideshow = document.getElementById('slideshow');
            slideshow.innerHTML = `
                <div class="error-message" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📷</div>
                        <div style="font-weight: bold; margin-bottom: 0.5rem;">No images found</div>
                        <div style="font-size: 0.9rem; color: #666;">
                            There are no images available for this archive.
                        </div>
                    </div>
                </div>
            `;
            
            // Hide controls
            document.querySelector('.slideshow-controls').style.display = 'none';
        }

        function initializeSlideshow() {
            const slideshow = document.getElementById('slideshow');
            
            // Clear existing slides
            const existingSlides = slideshow.querySelectorAll('.slide');
            existingSlides.forEach(slide => slide.remove());
            
            // Create slide containers
            config.images.forEach((image, index) => {
                const slide = document.createElement('div');
                slide.className = 'slide';
                slide.id = `slide-${index}`;
                
                // Add loading placeholder
                slide.innerHTML = `
                    <div class="loading-spinner"></div>
                    <img 
                        class="slide-image" 
                        style="display: none;" 
                        onload="onImageLoad(${index})"
                        onerror="onImageError(${index})"
                    >
                `;
                
                // Insert slide before the navigation buttons
                const navButtons = slideshow.querySelector('.nav-btn');
                slideshow.insertBefore(slide, navButtons);
            });
            
            console.log('Created', config.images.length, 'slides');
            
            // Activate first slide
            showSlide(0);
        }

        function loadInitialSlides() {
            // Load current slide and next few slides
            for (let i = 0; i <= Math.min(config.preloadCount, config.images.length - 1); i++) {
                loadSlideImage(i);
            }
        }

        function loadSlideImage(index) {
            if (loadedImages.has(index)) return;
            
            const slide = document.getElementById(`slide-${index}`);
            if (!slide) {
                console.error('Slide not found:', index);
                return;
            }
            
            const img = slide.querySelector('img');
            if (!img) {
                console.error('Image element not found in slide:', index);
                return;
            }
            
            // Remove any existing error message when retrying
            const existingError = slide.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            
            // Show spinner
            const spinner = slide.querySelector('.loading-spinner');
            if (spinner) {
                spinner.style.display = 'inline-block';
            }
            
            // Hide image initially
            img.style.display = 'none';
            
            // Build the full image URL
            const imagePath = config.images[index];
            const fullImageUrl = "{{ url('/') }}/" + imagePath.replace(/^\//, '');
            
            console.log('Loading image:', index, fullImageUrl);
            
            img.src = fullImageUrl;
            img.alt = `{{ $archive->book_name }} - Image ${index + 1}`;
            
            loadedImages.add(index);
        }

        function onImageLoad(index) {
            console.log('Image loaded successfully:', index);
            const slide = document.getElementById(`slide-${index}`);
            const spinner = slide.querySelector('.loading-spinner');
            const img = slide.querySelector('img');
            
            if (spinner) spinner.style.display = 'none';
            if (img) img.style.display = 'block';
        }

        function onImageError(index) {
            console.error('Image failed to load:', index, config.images[index]);
            const slide = document.getElementById(`slide-${index}`);
            const spinner = slide.querySelector('.loading-spinner');
            const img = slide.querySelector('img');
            
            // Hide spinner and image
            if (spinner) spinner.style.display = 'none';
            if (img) img.style.display = 'none';
            
            // Remove any existing error message
            const existingError = slide.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            
            // Create and show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `
                <div style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                    <div style="font-weight: bold; margin-bottom: 0.5rem;">Image failed to load</div>
                    <div style="font-size: 0.9rem; color: #666;">
                        Image: {{ $archive->book_name }} - ${index + 1}<br>
                        Path: ${config.images[index]}
                    </div>
                </div>
            `;
            slide.appendChild(errorDiv);
        }

        function showSlide(index) {
            if (config.images.length === 0) return;
            
            // Hide all slides
            document.querySelectorAll('.slide').forEach(slide => {
                slide.classList.remove('active');
            });
            
            // Show current slide
            const currentSlideElement = document.getElementById(`slide-${index}`);
            if (currentSlideElement) {
                currentSlideElement.classList.add('active');
            }
            
            // Update UI
            document.getElementById('current-slide').textContent = index + 1;
            updateIndicators(index);
            updateProgressBar();
            
            // Lazy load nearby slides
            lazyLoadNearbySlides(index);
        }

        function lazyLoadNearbySlides(currentIndex) {
            for (let i = Math.max(0, currentIndex - config.lazyLoadThreshold); 
                 i <= Math.min(config.images.length - 1, currentIndex + config.lazyLoadThreshold); 
                 i++) {
                loadSlideImage(i);
            }
        }

        function changeSlide(direction) {
            if (config.images.length === 0) return;
            
            const newIndex = (currentSlide + direction + config.images.length) % config.images.length;
            currentSlide = newIndex;
            showSlide(currentSlide);
            resetSlideshowTimer();
        }

        function goToSlide(index) {
            if (index >= 0 && index < config.images.length) {
                currentSlide = index;
                showSlide(currentSlide);
                resetSlideshowTimer();
            }
        }

        // NEW: Go to first page
        function goToFirstSlide() {
            if (config.images.length === 0) return;
            goToSlide(0);
        }

        // NEW: Go to last page
        function goToLastSlide() {
            if (config.images.length === 0) return;
            goToSlide(config.images.length - 1);
        }

        function startSlideshow() {
            if (isSlideshowRunning || config.images.length === 0) return;
            
            isSlideshowRunning = true;
            document.getElementById('start-btn').disabled = true;
            document.getElementById('stop-btn').disabled = false;
            
            slideshowInterval = setInterval(() => {
                changeSlide(1);
            }, config.slideDuration);
        }

        function stopSlideshow() {
            isSlideshowRunning = false;
            document.getElementById('start-btn').disabled = false;
            document.getElementById('stop-btn').disabled = true;
            
            if (slideshowInterval) {
                clearInterval(slideshowInterval);
            }
        }

        function resetSlideshowTimer() {
            if (isSlideshowRunning) {
                stopSlideshow();
                startSlideshow();
            }
        }

        function updateProgressBar() {
            const progress = document.getElementById('progress');
            if (isSlideshowRunning) {
                progress.style.width = '0%';
                setTimeout(() => {
                    progress.style.width = '100%';
                }, 10);
            } else {
                progress.style.width = '0%';
            }
        }

        function createIndicators() {
            const indicators = document.getElementById('indicators');
            indicators.innerHTML = '';
            
            config.images.forEach((_, index) => {
                const indicator = document.createElement('button');
                indicator.className = `indicator ${index === 0 ? 'active' : ''}`;
                indicator.onclick = () => goToSlide(index);
                indicators.appendChild(indicator);
            });
        }

        function updateIndicators(index) {
            document.querySelectorAll('.indicator').forEach((indicator, i) => {
                indicator.classList.toggle('active', i === index);
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                changeSlide(-1);
            } else if (e.key === 'ArrowRight') {
                changeSlide(1);
            } else if (e.key === ' ') {
                e.preventDefault();
                if (isSlideshowRunning) {
                    stopSlideshow();
                } else {
                    startSlideshow();
                }
            } else if (e.key === 'Escape') {
                stopSlideshow();
            } else if (e.key === 'Home') {
                goToFirstSlide();
            } else if (e.key === 'End') {
                goToLastSlide();
            }
        });

        // Auto-start slideshow after 3 seconds if there are images
        if (config.images.length > 0) {
            setTimeout(() => {
                startSlideshow();
            }, 3000);
        }
    </script>
</body>
</html>
@endsection