@extends('layouts.clear')


@section('content')

    @if (request()->withTools)
        @include('stat._toolbox')
    @endif

    @if($stat)
        @include('table', ['data' => $stat])
    @else
        No data
    @endif

@endsection