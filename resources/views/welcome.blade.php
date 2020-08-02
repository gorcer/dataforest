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
                        их обработки и визуализации.
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

                        </div>
                    </div>

                    <div class="card" style="width: 18rem;">
                        <div class="mx-auto mt-3">
                            <img class="" src="img/cottage.png" alt="Умный дом">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Умный дом</h5>
                            <p class="card-text">Контроль показателей умного дома, оповещение о критическом изменении температуры.</p>

                        </div>
                    </div>

                    <div class="card" style="width: 18rem;">
                        <div class="mx-auto mt-3">
                            <img class="" src="img/gear.png" alt="KPI процессов">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">KPI процессов</h5>
                            <p class="card-text">Отслеживание показателей бизнес-процессов. Контроль работы сотрудников и отделов.</p>

                        </div>
                    </div>

                    <div class="card" style="width: 18rem;">
                        <div class="mx-auto mt-3">
                            <img class="" src="img/sale.png" alt="KPI процессов">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Контроль цен</h5>
                            <p class="card-text">Мониторинг цен на товары, услуги, недвижимость, автомобили в онлайн магазинах и на досках объявлений.</p>

                        </div>
                    </div>



                </div>

            </div>

            <div class="mt-5 col-md-12 mb-5 text-center">
                <h1>Разберем пример?</h1>
                <h4>Допустим вы хотите отслеживать изменение стоимости жилья в своем районе<br/>
                    чтобы знать когда лучше купить, когда продать и стоит ли в это вкладываться.<br/>
                В этой статье мы разберем как создается сборщик, как он настраивается и как из него получать данные.<br/>
                    </h4>
                <a href="/howto" class="btn btn-primary mt-3">Узнать больше</a>

            </div>

            <hr class="mt-5 mb-5"/>

            <div class="mt-5 col-md-12 mb-5 text-center">
                    <h1>Хотите попробовать?</h1>
                    <div class="d-block">
                        Сейчас у вас есть отличный шанс попробовать все бесплатно так как со следующего месяца подписка станет платной!
                    </div>

                    <a href="{{ route('register') }}" type="button" class="mt-3 btn btn-lg btn-success">Зарегистрироваться и попробовать бесплатно</a>

            </div>


    </div>
</div>
@endsection
