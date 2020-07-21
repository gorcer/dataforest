@extends('layouts.clear')


@php

$labels = [];
$values=[];
$dataset=[];


// Формируем датасеты для графика
foreach(array_reverse($stat) as $items) {
    $labels[]='"'.$items['dt'].'"';

    // Оставляем только поля со значениями
    foreach($items as $f => $v) {

    if ($f == 'dt') continue;

    if (is_array($v)) {
         $dataset[$f][]='';
        } else {
         $dataset[$f][]=$v;
        }
        }
}





$style=[];

$width = request()->width;
if ($width) {
$width = 'width:'.$width.'';
$style[]=$width;
}


$height = request()->height;
if ($height) {
$height = 'height:'.$height.'';
$style[]=$height;
}


@endphp

@section('footer')

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <!-- Latest compiled and minified JavaScript -->
{{--
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
--}}

    <script>
        var myChart;
        var dataset=[];

{{--
        var start = moment({!!request()->start ? "'" . request()->start. "'" : ''  !!});
        var end = moment({!!request()->start ? "'" . request()->end. "'" : ''  !!});
--}}
        $(document).ready(function(){

            @foreach($dataset as $f=>$set)
                var color =  chartPallete.shift();
                dataset['{{$f}}'] = {
                    label: '{{$f}}',
                    data: [{{join(',', $set)}}],
                    backgroundColor: color,
                        borderColor: color,
                        fill: false
                } ;
            @endforeach




        var ctx = document.getElementById('myChart').getContext('2d');

        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [{!!join(',', $labels)!!}],
        },
        options: {
            maintainAspectRatio: false,
                responsive: {{sizeof($style)==0?'true':'false'}}
        }

        });

        refreshGraph();

        });


    </script>
@endsection

@section('content')





        <div class=" {{ (request()->withTools?'d-block':'d-none') }}">

            @include('stat._toolbox')

        </div>




   <canvas id="myChart" style="{{join(';', $style)}}" ></canvas>






@endsection