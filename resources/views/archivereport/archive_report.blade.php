@extends('layouts.app')

@section('content')

    <div class="row">
        @php
            $stats = [
                ['title' => 'تعداد کتاب های ارشیف شده', 'id' => 'archive-books-pie-chart', 'count' => $totalArchive, 'color' => '#36A2EB'],
                ['title' => 'تعداد محصلین ارشیف شده', 'id' => 'archived-students-pie-chart', 'count' => $totalArchivedata, 'color' => '#FF6384'],
                ['title' => 'تعداد اسناد ثبت شده محصلین', 'id' => 'archived-documents-pie-chart', 'count' => $reportarchivedoc, 'color' => 'lightgreen'],
                ['title' => 'تعداد کتاب های تکمیل شده', 'id' => 'completed-books-pie-chart', 'count' => optional($totalArchivestatus->where('status_id', 4)->first())->count ?? 0, 'color' => '#4BC0C0'],
                ['title' => 'تعداد کتاب های ناتکمیل', 'id' => 'incomplete-books-pie-chart', 'count' => optional($totalArchivestatus->where('status_id', 3)->first())->count ?? 0, 'color' => '#FFCE56'],
                ['title' => 'تعداد کتاب های تحت پروسس', 'id' => 'processing-books-pie-chart', 'count' => optional($totalArchiveqcstatus->where('qc_status_id', 2)->first())->count ?? 0, 'color' => '#7E57C2'],
                ['title' => 'تعداد کتاب های تائید شده', 'id' => 'approved-books-pie-chart', 'count' => optional($totalArchiveqcstatus->where('qc_status_id', 3)->first())->count ?? 0, 'color' => '#4CAF50'],
                ['title' => 'تعداد کتاب های رد شده', 'id' => 'rejected-books-pie-chart', 'count' => optional($totalArchiveqcstatus->where('qc_status_id', 4)->first())->count ?? 0, 'color' => '#F44336']
            ];
        @endphp
{{--        ['title' => 'تعداد کتاب های صرف نظر شده', 'id' => 'discarded-books-pie-chart', 'count' => optional($totalArchivestatus->where('status_id', 2)->first())->count ?? 0, 'color' => '#FF9F40'],--}}

        @foreach ($stats as $stat)
            <div class="col-md-3 col-sm-12">
                <div class="portlet">
                    <div class="row">
                        <h3 class="text-center">{{ $stat['title'] }}</h3>
                        <div class="col-md-12 charts-canvas">
                            <canvas id="{{ $stat['id'] }}" width="50" height="70"></canvas>
                            <h1>{{ $stat['count'] }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Chart.js and CanvasJS Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.min.js"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

    <!-- Chart.js Bar Charts -->
    <script>
        const stats = @json($stats);

        stats.forEach(stat => {
            new Chart(document.getElementById(stat.id), {
                type: 'bar',
                data: {
                    datasets: [{
                        label: stat.title,
                        backgroundColor: [stat.color],
                        data: [stat.count]
                    }]
                },
                options: {
                    title: { display: true },
                    legend: {
                        position: 'bottom',
                        reverse: true,
                        labels: { fontFamily: 'nazanin' } // Font customization
                    }
                }
            });
        });
    </script>

    <!-- CanvasJS Pie Chart -->
    <div id="chartContainer" style="height: 600px; width: 100%;"></div>
    <script>
        window.onload = function() {
            const pieData = @json($stats).map(stat => ({
                y: stat.count,
                label: stat.title,
                color: stat.color // Assign color from the stats array
            }));

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                title: {
                    text: "راپور معلومات بشکل یکجایی",
                    fontFamily: "nazanin",
                    fontColor: "black",
                    fontSize: 24,
                },
                data: [{
                    type: "pie",
                    startAngle: 240,
                    yValueFormatString: "(##0.)\"\"",
                    indexLabel: "{label} {y}",
                    dataPoints: pieData
                }]
            });
            chart.render();
        };
    </script>

@endsection
