@extends('layouts.app')

@section('content')

@php

$fields = $collector->getFields();

@endphp

<div class="container">
    <script>
        var testUrl="{{route('collector.test')}}";
    </script>

    <h1>{{$collector->name}}</h1>

    {!!Form::open()->id('collectorForm')->route('collector.update', ['collector'=>$collector])->method('put')!!}


    {!!Form::text('name', 'Название', $collector->name)!!}


    {!!Form::select('period', 'Периодичность',
    ['hourly' => 'Каждый час',
    'daily' => 'Каждый день',
    'weekly' => 'Каждый понедельник',
    'monthly' => 'Ежемесячно'
    ],
    $collector->period
    ) !!}

    {!!Form::hidden('type', $collector->type)!!}

    @switch($collector->type)

        @case('http')
            <div id="http">
                {!!Form::text('http_url', 'Адрес страницы', $collector->http_url)!!}
                {!!Form::text('http_xpath', 'XPath', $collector->http_xpath)!!}
            </div>
        @break

        @case('sql')
            <div id="sql">
                {!!Form::text('sql_dbname', 'База данных', $collector->sql_dbname)!!}
                {!!Form::textarea('sql_query', 'Запрос', $collector->sql_query)->attrs(['rows' => 10, 'cols' => 30])!!}
            </div>
        @break

    @endswitch

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

    </div>

    @if(sizeof($fields) > 0)
        <div class='mt-5'>
            <h2>Fields</h2>
            @foreach($fields as $field)
                <div class="row no-labels">
                    <div class='mr-4 col-2'>{{$field}}</div>
                    <div>
                        {!!Form::select('aggregate['.$field.']', null,
                        ['avg' => 'Average',
                        'sum' => 'Sum',
                        'min' => 'Min',
                        'max' => 'Max'
                        ],
                        (($collector->aggregate && isset($collector->aggregate[$field]))?$collector->aggregate[$field]:false)
                        ) !!}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="row mt-5">

        <div class="col-6">
            {!!Form::submit("Save")->color("success")->attrs(['id'=>'save'])!!}
        </div>
        <div class="col-6">
            <a href="{{route('collector.delete', ['id' => $collector])}}" class="btn btn-danger">Delete</a>
        </div>
    </div>

    {!!Form::close()!!}

</div>


@endsection