@extends('layouts.clear')


@section('footer')

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.css" rel="stylesheet">


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>




    <script src="{{ asset('js/chart.js') }}"></script>
@endsection

@section('content')

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



    {!!Form::open()->id('graph')!!}
        <div class="d-flex flex-row toolbox">
            <div class="mr-2 col-2 {{($hiddenSelect ? 'd-none' : '')}}">
                {!!Form::select('field[]', '',
                $fields,
                $selectedFields
                )->id('selectField')->multiple() !!}
            </div>
            <div class="col-2">

                {!!Form::select('grouping', '',
                ['by_days' => 'by Days',
                'by_weeks' => 'by Weeks',
                'by_month' => 'by_month',
                'by_year' => 'by_year',
                ], request()->group
                )->id('grouping') !!}
            </div>

            <div class="dropdown col-2">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Get Link
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <div class="container">

                        <textarea id="reportLink">{{$link}}</textarea>


                        <div class="mt-2">
                            <input type="input" name="width" placeholder="width">
                        </div>

                        <div class="mt-2">
                            <input type="input" name="height" placeholder="height" value="400px">
                        </div>

                        <div class="mt-2">
                            <input type="checkbox" name="withTools"/> with tool box
                        </div>

                        <button class="btn btn-primary mt-2">Copy to clipboard</button>
                    </div>
                </div>
            </div>

        </div>

    {!!Form::close()!!}



    <canvas id="myChart" style="{{join(';', $style)}}" ></canvas>



<script>
    var myChart;
    var dataset=[];
    var reportLink = '{{$link}}';

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