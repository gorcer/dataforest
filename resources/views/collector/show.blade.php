@extends('layouts.app')

@section('content')

<div class="container">
    <div class="justify-content-center row">
        <div class="col-md-2">
            @foreach($allCollectors as $collector)
                <div class="row">
                    <div class="col-12">
                        <a href="{{route('collector.show', ['collector' => $collector])}}">
                            {{$collector->name}}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-10">

            <h1>{{$mainCollector->name}}</h1>


            @if (sizeof($mainCollector->getStat()) == 0)
                <div class="warn">
                    Данные ожидают загрузки, зайдите попозже.
                </div>
            @else
                <div>

                @php
                    $stat = $mainCollector->getStat();
                    $lastValue = reset($stat);
                    unset($lastValue['dt']);

                    $link=route('collector.frame', ['id'=>$mainCollector->id, 'group'=>'by_day', 'type'=> 'diagram'] );

                @endphp

                    <div class="row">
                        <div class="col-6">
                            {!!Form::select('type', '',
                            ['diagram' => 'diagram',
                            'JSON' => 'json',
                            'table' => 'data table',
                            'value' => 'last value',
                            ],'diagram'
                            ) !!}
                        </div>

                    </div>


                  <div class="tab-content">
                      <div class="tab-pane active" id='diagram'>
                        <iframe class="col-12" height="500px" src="{{$link}}?height=400px"></iframe>
                      </div>

                      <div class="tab-pane" id="value">
                          @if (sizeof($lastValue)>1)
                            @include('table', ['data' => [ $lastValue ]])
                          @else
                            <h1>{{$lastValue['value']}}</h1>
                          @endif
                      </div>


                      <div class="tab-pane  overflow-auto resultTable" id='JSON'>
                          <pre>
                            {!!json_encode($stat, JSON_PRETTY_PRINT)!!}
                          </pre>
                      </div>

                      <div class="tab-pane  overflow-auto resultTable" id='table'>
                            @include('table', ['data' => $stat])
                      </div>
                  </div>


              </div>
            @endif


        </div>


    </div>
</div>
@endsection
