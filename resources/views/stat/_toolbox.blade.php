@php


// Получаем поля
$fields = $collector->getFields();
foreach($fields as $k => $field) {
    $fields[$field] = $field;
    unset($fields[$k]);
}

$selectedFields = request()->get('field', false);

if (!$selectedFields || sizeof($selectedFields) == 0) {
    $selectedFields =  [reset($fields)];
}

$hiddenSelect = sizeof($fields) == 1;

$link=route('collector.frame', ['id'=>$collector->id, 'group'=>'%group%', 'type'=> request()->type] );

@endphp

<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script>
    var reportLink = '{{$link}}';
</script>
<div class="d-flex flex-row toolbox">
    <div class="mr-2 col-2 {{($hiddenSelect ? 'd-none' : '')}}">
        {!!Form::select('field[]', '',
        $fields,
        $selectedFields
        )->id('selectField')->multiple() !!}
    </div>
    <div class="col-2">

        {!!Form::select('grouping', '',
        [
        'by_hours' => 'by Hours',
        'by_days' => 'by Days',
        'by_weeks' => 'by Weeks',
        'by_month' => 'by Month',
        'by_year' => 'by Year',
        ], (request()->group?request()->group:'by_month')
        )->id('grouping') !!}
    </div>
    <div class="mr-2">

        {!!Form::select('period', '',
        [
        'today' => 'Today',
        'yesteraday' => 'Yesteraday',
        '7_days' => 'Last 7 Days',
        '30_days' => 'Last 30 Days',
        'this_month' => 'This Month',
        'last_month' => 'Last Month',
        'this_year' => 'This Year',
        'last_year' => 'Last Year'
        ], (request()->period?request()->period:'7_days')
        )->id('period') !!}

    </div>

    <div class="d-none">

        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
            <i class="fa fa-calendar"></i>&nbsp;
            <span></span> <i class="fa fa-caret-down"></i>
        </div>

    </div>


    <div class="dropdown col-4 {{ (request()->cl?'':'d-none') }}">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Get Link
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="container">

                <textarea id="reportLink">{{$link}}</textarea>

                <form id="toolBox">
                    <div class="mt-2">
                        <input type="input" name="width" placeholder="width">
                    </div>

                    <div class="mt-2">
                        <input type="input" name="height" placeholder="height" value="400px" >
                    </div>

                    <div class="mt-2">
                        <input type="checkbox" name="withTools" {!!request()->withTools?'checked="checked"':''!!}/> with tool box
                    </div>

                </form>

                <a class="btn btn-primary mt-2" id="copy2clipboard" href="#">Copy to clipboard</a>
            </div>
        </div>

        <div class="d-none" id="copied">
            Copied...
        </div>
    </div>
</div>