
@extends('layouts.clear')


@section('content')


@include('stat._toolbox')


<pre>{!!json_encode($stat, JSON_PRETTY_PRINT)!!}</pre>

@endsection

