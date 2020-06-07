{{-- https://github.com/netojose/laravel-bootstrap-4-forms --}}

@extends('layouts.app')

@section('content')

<div class="container">
<script>
    var testUrl="{{route('collector.test')}}";
</script>
    <h1>Time to create new collector!</h1>

    {!!Form::open()->id('collectorForm')->route('collector.store')!!}


    {!!Form::text('name', 'Название')!!}

    {!!Form::select('period', 'collect period',
    ['hourly' => 'Hourly',
    'daily' => 'Daily',
    'weekly' => 'Every monday',
    'monthly' => 'Monthly'
    ]
    ) !!}

    {!!Form::select('type', 'Вид сборщика',
        [0 => 'Выберите сборщика',
        'http' => 'Запрос к сайту',
        'sql' => 'SQL-запрос']
    ) !!}


    <div class="tab-content createCollector">


        <div class="tab-pane" id="http">
            {!!Form::text('http_url', 'Адрес страницы')!!}
            {!!Form::text('http_xpath', 'XPath')!!}
        </div>


        <div class="tab-pane" id="sql">

            {!!Form::text('sql_dbname', 'База данных')!!}
            {!!Form::textarea('sql_query', 'Запрос')->attrs(['rows' => 10, 'cols' => 30])!!}

        </div>

        {!!Form::button("Try it")->color("primary")->attrs(['id'=>'tryIt'])!!}

        <div class="d-none" id="tryIt-loading">

            <button class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Загрузка...
            </button>
        </div>

        <div class="result d-none mt-5">
            <h3>Результат:</h3>

            <div class="problem text-danger">

            </div>

            <div class="resultTable overflow-auto">

            </div>

            {!!Form::submit("Save")->color("success")->attrs(['id'=>'save', 'class' => 'd-none'])!!}

        </div>
    </div>

</div>

{!!Form::close()!!}

@endsection