@extends('layouts.app')

@section('content')
<div class="container">
    <div class="justify-content-center row">
            <div class="col-md-5">

                <h1>Data Logger</h1>
                <p class="lead">
                    Пришло время добавить нового сборщика!
                </p>

                @include('collector.create')
            </div>


    </div>
</div>
@endsection
