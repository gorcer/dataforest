@extends('layouts.clear')


@section('content')


    @include('stat._toolbox')

    @if($lastValue)
        @if (sizeof($lastValue)>1)
            @include('table', ['data' => [ $lastValue ]])
        @else
            <h1>{{$lastValue['value']}}</h1>
        @endif
    @else
        <h1>No data</h1>
    @endif

@endsection
