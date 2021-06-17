@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <div class="card">

                <div class="card-header">Your collectors</div>
                    <div class="card-body itemList">
                        @if (sizeof($collectors) > 0)

                                <div class="row">
                                    @foreach($collectors as $collector)
                                        <a class="col-md-3" href="{{route('collector.show', ['collector' => $collector])}}">
                                            {{$collector->name}}
                                        </a>
                                    @endforeach
                                </div>

                        @else
                            Where is no collectors in your account!<br/>
                            Time to create the first!
                        @endif
                            <div class="col-md-12">
                                        <a href="{{route('collector.create')}}" class="mt-3 btn btn-primary">Create new collector</a>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
