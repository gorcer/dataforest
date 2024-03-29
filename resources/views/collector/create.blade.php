{{-- https://github.com/netojose/laravel-bootstrap-4-forms --}}

@extends('layouts.app')

@section('content')

<div class="container">
<script>
    var testUrl="{{route('collector.test')}}";
    var findXPathUrl="{{route('collector.findXPath')}}";

</script>
    <h1>Time to create new collector!</h1>

    {!!Form::open()->id('collectorForm')->route('collector.store')!!}


    {!!Form::text('name', 'Name')!!}

    {!!Form::select('period', 'collect period',
    ['hourly' => 'Hourly',
    'daily' => 'Daily',
    'weekly' => 'Every monday',
    'monthly' => 'Monthly'
    ],
    'daily'
    ) !!}

    {!!Form::select('type', 'Collector type',
        [0 => 'Change collector type',
        'http' => 'From website',
        'sql' => 'SQL-query',
        'api' => 'API']
    ) !!}


    <div class="tab-content createCollector">


        <div class="tab-pane" id="http">
            {!!Form::text('http_url', 'Page URL')!!}
            {!!Form::text('http_value', 'Current value on a page')!!}

            <div class="row">
                <div class="col-md-12">

                    <label for="inp-http_xpath" class="">Path</label>
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
                        <input type="text" id="http_xpath" name="http_xpath" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                    </div>

                </div>
            </div>

        </div>


        <div class="tab-pane" id="sql">

            {!!Form::text('db_connection', 'Database <a data-trigger="hover" data-html="true" data-toggle="popover" title="How to ..." data-content="
             1) Create new user in your mysql for dataforest <br/>
             2) Grant select permission to your data tables <br/>
             3) Allow connections from ip 89.223.88.238">
                ( ? )
            </a>')->placeholder('mysql://user:password@server:port/database')!!}
            {!!Form::textarea('db_query', 'Query')->attrs(['rows' => 10, 'cols' => 30])!!}

        </div>

        <div class="tab-pane" id="api">

            Go edit collector after create and get the link for update!

        </div>

        {!!Form::button("Try it")->color("primary")->attrs(['id'=>'tryIt'])!!}

        <div class="d-none" id="tryIt-loading">

            <button class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Загрузка...
            </button>
        </div>

        <div class="result d-none mt-5">
            <h3>Result:</h3>

            <div class="problem text-danger">

            </div>

            <div class="resultTable overflow-auto">

            </div>

        </div>

        {!!Form::submit("Save")->color("success")->attrs(['id'=>'save', 'class' => 'd-none'])!!}
    </div>

</div>

{!!Form::close()!!}

@endsection