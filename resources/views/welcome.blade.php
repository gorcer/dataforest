@extends('layouts.app')

@section('content')
<div class="container">
    <div class="justify-content-center row">
            <div class="col-md-12">

                <div class="jumbotron lead">
                    <h1 class="display-4">{{ config('app.name', 'DataLogger') }}</h1>
                    <p>
                         Это мощный и одновременно простой инструмент,
                        предоставляющий огромные возможности для сбора данных
                        их обработки и графического отображения.
                    </p>

                    <p>
                        Вам достаточно указать ссылку на интернет сайт и число которое требуется отслеживать и наш сервис займется его регулярной проверкой.
                        Данные будут собраны, сгруппированы и представлены для вас в удобном виде - таблица, график, json.
                    </p>

                    <p>
                        Так же возможно подключение к базе данных и сбор данных по вашему SQL-запросу.
                    </p>
                </div>

            </div>

            <div class="col-md-12">
                <h1>Варианты использования</h1>

                <div class="card-deck">

                    <div class="card" style="width: 18rem;">
                        <div class="mx-auto mt-3">
                            <img class="" src="img/money-graph.png" alt="Отслеживание финансовых показателей">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Отслеживание финансовых показателей</h5>
                            <p class="card-text">Мониторинг изменения стоимости акций и оповещение при резких скачках.</p>
                            <a href="#" class="btn btn-primary">Узнать больше</a>
                        </div>
                    </div>

                    <div class="card" style="width: 18rem;">
                        <div class="mx-auto mt-3">
                            <img class="" src="img/cottage.png" alt="Умный дом">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Умный дом</h5>
                            <p class="card-text">Контроль показателей умного дома, оповещение о критическом изменении температуры.</p>
                            <a href="#" class="btn btn-primary">Узнать больше</a>
                        </div>
                    </div>

                    <div class="card" style="width: 18rem;">
                        <div class="mx-auto mt-3">
                            <img class="" src="img/gear.png" alt="KPI процессов">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">KPI процессов</h5>
                            <p class="card-text">Отслеживание показателей бизнес-процессов. Контроль работы сотрудников и отделов.</p>
                            <a href="#" class="btn btn-primary">Узнать больше</a>
                        </div>
                    </div>

                    <div class="card" style="width: 18rem;">
                        <div class="mx-auto mt-3">
                            <img class="" src="img/sale.png" alt="KPI процессов">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Контроль цен</h5>
                            <p class="card-text">Мониторинг цен на товары, услуги, недвижимость, автомобили в онлайн магазинах и на досках объявлений.</p>
                            <a href="#" class="btn btn-primary">Узнать больше</a>
                        </div>
                    </div>



                </div>

            </div>

            <div class="mt-5 col-md-12 mb-5">
                <h1>Как это выглядит</h1>
                <img src="/img/screen.png" class="img-fluid"/>
            </div>

            <hr class="mt-5 mb-5"/>

            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <a href="{{ route('register') }}" type="button" class="btn btn-lg btn-success">Зарегистрироваться и попробовать бесплатно</a>
                    </div>
                </div>
            </div>


    </div>
</div>
@endsection
