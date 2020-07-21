@extends('layouts.app')

@section('content')

<div class="container">
    <div class="justify-content-center row">
        <div class="col-md-2 d-flex flex-column sidebar">
            @foreach($allCollectors as $collector)

                        <a class="pt-2" href="{{route('collector.show', ['collector' => $collector])}}">
                            {{$collector->name}}
                        </a>

            @endforeach

            <a href="{{route('collector.create')}}" class="mt-3 btn btn-secondary">new</a>
        </div>

        <div class="col-md-10">

            <h1>{{$mainCollector->name}}</h1>
            <div class='d-flex flex-row'>
                <a href="{{route('collector.edit', ['collector' => $mainCollector])}}" class="btn btn-secondary">Edit</a>
                <a href="{{route('collector.process', ['id' => $mainCollector->id])}}" class="ml-3 btn btn-secondary">update</a>
            </div>


            @if ($mainCollector->statCount() == 0)
                <div class="warn">
                    Данные ожидают загрузки, зайдите попозже.
                </div>
            @else
                <div>

                @php
                    $stat = $mainCollector->getStat();
                    $lastValue = reset($stat);
                    unset($lastValue['dt']);



                @endphp

                    <div class="row">
                        <div class="col-6">
                            {!!Form::select('type', '',
                            ['diagram' => 'diagram',
                            'table' => 'data table',
                            'JSON' => 'json',
                            'value' => 'last value',
                            ],'diagram'
                            ) !!}
                        </div>

                    </div>


                  <div class="tab-content">
                      <div class="tab-pane active" id='diagram'>
                        <iframe class="col-12" height="600px" src="{{ route('collector.frame', ['id'=>$mainCollector->id, 'group'=>'by_days', 'type'=> 'diagram'] ) }}?height=400px&cl=1&withTools=1"></iframe>
                      </div>

                      <div class="tab-pane" id="value">
                          @php
                            $lastValue = reset($stat);
                          @endphp

                          @if($lastValue)
                              @if (sizeof($lastValue)>1)
                                @include('table', ['data' => [ $lastValue ]])
                              @else
                                 <h1>{{$lastValue['value']}}</h1>
                              @endif
                          @else
                              <h1>No data</h1>
                          @endif


                      </div>


                      <div class="tab-pane  overflow-auto resultTable" id='JSON'>
                          <pre>
                            {!!json_encode($stat, JSON_PRETTY_PRINT)!!}
                          </pre>
                      </div>

                      <div class="tab-pane  overflow-auto resultTable" id='table'>

                          <iframe class="col-12" height="600px" src="{{ route('collector.frame', ['id'=>$mainCollector->id, 'group'=>'by_days', 'type'=> 'table'] ) }}?height=400px&cl=1&withTools=1"></iframe>

                      </div>
                  </div>


              </div>
            @endif


        </div>


    </div>
</div>
@endsection
