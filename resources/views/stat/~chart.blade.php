
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js" defer></script>
<link href="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.css" rel="stylesheet">

<script src="{{ asset('js/bootstrap-multiselect.js') }}"></script>
<link href="{{ asset('css/bootstrap-multiselect.css') }}" rel="stylesheet">


@php
    $labels = [];
    $values=[];
    $dataset=[];


    // Формируем датасеты для графика
    foreach($stat->reverse()->toArray() as $items) {
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
    $fields = $stat->toArray();
    $fields = reset( $fields );
    unset($fields['dt']);
    $result=[];
    foreach(array_keys($fields) as $field) {
        $result[$field] = $field;
    }
    $fields = $result;

    $hiddenSelect = sizeof($fields) == 1;

@endphp


<div class="{{($hiddenSelect ? 'd-none' : '')}}">
    {!!Form::select('field', '',
    $fields,
    array_shift($fields)
    )->id('selectField')->multiple()->attrs(['class' => 'col-6 ']) !!}
</div>


<canvas id="myChart" width="400" height="400"></canvas>

<script>
    var myChart;
    var dataset=[];

    chartPallete = [
        '#4dc9f6',
        '#f67019',
        '#f53794',
        '#537bc4',
        '#acc236',
        '#166a8f',
        '#00a950',
        '#58595b',
        '#8549ba',

        '#8da9f6',
        '#06b019',
        '#05b794',
        '#a3dbc4',
        '#0c3236',
        '#e6aa8f',
        '#b03950',
        '#f8c95b',
        '#f589ba'
    ];

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

    $(document).ready(function(){


    var ctx = document.getElementById('myChart').getContext('2d');
    myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [{!!join(',', $labels)!!}],
        }
    });

    refreshGraph();
    });

    $('#selectField').multiselect({
        onChange: function(option, checked, select) {
            refreshGraph();
        }
    });

</script>