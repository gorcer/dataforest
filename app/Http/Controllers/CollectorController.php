<?php

namespace App\Http\Controllers;

use App\Collector;
use App\Helpers\DatabaseConnection;
use App\Stat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \MongoDB\BSON\UTCDateTime;

class CollectorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('collector.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $params = $request->all();

        $params = array_filter($params, function ($value) {
            return $value != '';
        });

        unset($params['_token']);
        $params['hash'] = md5(print_r($params, true));
        $params['user_id']=Auth::id();
        $collector = Collector::create($params);

        $collector->process();

        return redirect()->route('collector.show', ['collector'=>$collector]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $collector = Collector::find($id);
        $allCollectors = Collector::where('user_id', Auth::id())->get();

        return view('collector.show', ['mainCollector' => $collector, 'allCollectors' => $allCollectors]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $collector = Collector::find($id);
        
        return view('collector.edit', ['collector' => $collector]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $collector = Collector::find($id);

        $params = $request->all();

        $params = array_filter($params, function ($value) {
            return $value != '';
        });

        unset($params['_token']);

        $collector->update($params);

        return redirect()->route('collector.show', ['collector'=>$collector]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        Collector::destroy($id);
        return redirect('');
    }

    public function test(Request $request) {

        $params = $request->all();

        $data = Collector::getData($params);

        if (!is_array($data)) {
            return
                [   'status' => 'error',
                    'data' => '<div class="text-danger">' . $data .'</div>'
                ];
        }

        // Если несколько записей, то нужно поле с датой
        if (sizeof($data) > 1) {

            $first = reset($data);
            if (!isset($first['dt'])) {
                return
                    [   'status' => 'problem',
                        'data' => view('table', ['data' => $data])->render(),
                        'problem' => 'You dataset need a dt field or only one row!'
                    ];
            }

        }



        return [
            'status' => 'ok',
            'data' => view('table', ['data' => $data])->render()
        ];
    }

    public function process($id) {
        $collector = Collector::findOrFail($id);
        $collector->process();

        return redirect()->route('collector.show',['collector' => $collector]);
    }

    public function frame(Request $request, $id, $type, $group) {

        $collector = Collector::findOrFail($id);
        $stat = $collector->getStat($group);

        switch($type) {
            case 'diagram':
                            return view('stat.chart', ['collector' => $collector, 'stat' => $stat]);
                            break;
        }


    }


    public static function processAll() {

        $collectors = Collector::whereRaw([
            '$or' => [
                ['period' => 'hourly', 'last_check' => ['$lt' =>  new UTCDateTime(strtotime('-1 hour') * 1000)] ],
                ['period' => 'daily', 'last_check' => ['$lt' => new UTCDateTime(strtotime('-1 day')*1000)] ],
                ['period' => 'weekly', 'last_check' => ['$lt' => new UTCDateTime(strtotime('-1 week')*1000)] ],
                ['period' => 'monthly', 'last_check' => ['$lt' => new UTCDateTime(strtotime('-1 month')*1000)] ],
            ]
            ,
        ])->get();

        foreach($collectors as $collector) {
            $collector->process();
            echo date('Y-m-d H:i:s') . ' - ProcessAll ' . $collector->name . PHP_EOL;
        }

    }
}
