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



// Получаем поля
$fields = $collector->getFields();

$hiddenSelect = sizeof($fields) == 1;

$link=route('collector.frame', ['id'=>$collector->id, 'group'=>'%group%', 'type'=> 'diagram'] );

$selectedFields = request()->field;

if (!$selectedFields || sizeof($selectedFields) == 0) {
$selectedFields =  reset($fields);
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
{{--
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
--}}

    <script>
        var myChart;
        var dataset=[];
        var reportLink = '{{$link}}';
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





        <div class=" {{ (request()->withTools?'d-flex':'d-none') }} flex-row toolbox">
            <div class="mr-2 col-2 {{($hiddenSelect ? 'd-none' : '')}}">
                {!!Form::select('field[]', '',
                $fields,
                $selectedFields
                )->id('selectField')->multiple() !!}
            </div>
            <div class="col-2">

                {!!Form::select('grouping', '',
                [
                'by_hours' => 'by Hours',
                'by_days' => 'by Days',
                'by_weeks' => 'by Weeks',
                'by_month' => 'by Month',
                'by_year' => 'by Year',
                ], (request()->group?request()->group:'by_month')
                )->id('grouping') !!}
            </div>
            <div class="mr-2">

                {!!Form::select('period', '',
                [
                'today' => 'Today',
                'yesteraday' => 'Yesteraday',
                '7_days' => 'Last 7 Days',
                '30_days' => 'Last 30 Days',
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
                ], (request()->group?request()->period:'7_days')
                )->id('period') !!}

            </div>

            <div class="d-none">

                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>

            </div>


            <div class="dropdown col-4 {{ (request()->cl?'':'d-none') }}">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Get Link
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <div class="container">

                        <textarea id="reportLink">{{$link}}</textarea>

                        <form id="toolBox">
                            <div class="mt-2">
                                <input type="input" name="width" placeholder="width">
                            </div>

                            <div class="mt-2">
                                <input type="input" name="height" placeholder="height" value="400px" >
                            </div>

                            <div class="mt-2">
                                <input type="checkbox" name="withTools" {!!request()->withTools?'checked="checked"':''!!}/> with tool box
                            </div>

                        </form>

                        <a class="btn btn-primary mt-2" id="copy2clipboard" href="#">Copy to clipboard</a>
                    </div>
                </div>

                <div class="d-none" id="copied">
                    Copied...
                </div>
            </div>



        </div>






    <canvas id="myChart" style="{{join(';', $style)}}" ></canvas>





@endsection