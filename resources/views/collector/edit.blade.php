@extends('layouts.app')

@section('content')

@php

$fields = $collector->getFields(true, false);

@endphp

<div class="container">
    <script>
        var testUrl="{{route('collector.test')}}";
        var findXPathUrl="{{route('collector.findXPath')}}";
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
                {!!Form::text('http_value', 'Текущее значение на странице')!!}

                <div class="row">
                    <div class="col-md-12">

                        <label for="inp-http_xpath" class="">Путь</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <button id="findXPath" class="btn btn-outline-secondary" type="button">Find</button>

                                <div class="d-none" id="findXPath-loading">

                                    <button class="btn btn-secondary" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Loading...
                                    </button>
                                </div>
                            </div>
                            <input type="text" id="http_xpath" name="http_xpath" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" value="{{$collector->http_xpath}}">
                        </div>

                    </div>
                </div>
            </div>
        @break

        @case('sql')
            <div id="sql">
                {!!Form::text('db_connection', 'Database',  $collector->db_connection)->placeholder('mysql://user:password@server:port/database')!!}
                {!!Form::textarea('db_query', 'Query', $collector->db_query)->attrs(['rows' => 10, 'cols' => 30])!!}
            </div>
        @break

    @endswitch

    <div class="row mb-5">
        <div class="col">

            <h4>Calculated Fields</h4>
            <div class="calculatedFields">
                @if (isset($collector->calculated) )
                @foreach($collector->calculated as $name => $field)
                    <div class="form-row mb-3">
                        <div class="col-2">
                            <input type="text" name="calcFieldName[{{$name}}]" value="{{$name}}" placeholder="field name" class="form-control"/>
                        </div>
                        <div class="col-2">
                            <input type="text" name="calcFieldVal[{{$name}}]"  value="{{$field}}" placeholder="formula, fieldN + fieldM ..." class="form-control"/>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-danger delete" type="button">Delete</button>
                        </div>
                    </div>
                @endforeach
                @endif
            </div>

            <script id="exampleCalcField"  type="text/template">
                <div class="form-row mb-3">
                    <div class="col-2">
                        <input type="text" name="calcFieldName[]" placeholder="field name" class="form-control"/>
                    </div>
                    <div class="col-2">
                        <input type="text" name="calcFieldVal[]" placeholder="formula, fieldN + fieldM ..." class="form-control"/>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-danger delete" type="button">Delete</button>
                    </div>
                </div>

            </script>

            <button class="btn btn-success" aria-label="Left Align" id="addCalcField" type="button">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                Add new calculated field
            </button>

        </div>
    </div>


    {!!Form::button("Try it")->color("primary")->attrs(['id'=>'tryIt'])!!}

    <div class="d-none" id="tryIt-loading">

        <button class="btn btn-primary" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Loading...
        </button>
    </div>

    <div class="result d-none mt-5">
        <h3>Результат:</h3>

        <div class="problem text-danger">

        </div>

        <div class="resultTable overflow-auto">

        </div>

    </div>

    <div class="row">
        @if(sizeof($fields) > 0)
            <div class='mt-5 col-6'>
                <h2>Fields</h2>
                @foreach($fields as $field)
                    <div class="row no-labels">
                        <div class='col-4'>{{$field}}</div>
                        <div class="col-4">
                            {!!Form::select('aggregate['.$field.']', null,
                            ['avg' => 'Average',
                            'sum' => 'Sum',
                            'min' => 'Min',
                            'max' => 'Max',
                            'hide' => 'Hidden',
                            ],
                            (($collector->aggregate && isset($collector->aggregate[$field]))?$collector->aggregate[$field]:false)
                            ) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif



    </div>

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
