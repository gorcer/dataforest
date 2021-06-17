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

        if ($collector->type != 'api' )
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

        $collector->getStat();

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
        $collector->migrate();
        
        return view('collector.edit', ['collector' => $collector]);
    }

    /**
     * Приводим поля к значению Key => value
     * @param $params
     * @return mixed
     */
    public function prepareCalcFields($params) {
        if (isset($params['calcFieldName']) && isset($params['calcFieldVal'])) {
            $params['calculated']=[];
            foreach($params['calcFieldName'] as $k=>$name) {
                if ($name == '')
                    continue;
                $params['calculated'][$name] = $params['calcFieldVal'][$k];
            }
        }
        unset($params['calcFieldName']);
        unset($params['calcFieldVal']);

        return $params;
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

        $params = $this->prepareCalcFields($params);

        unset($params['_token']);

        $collector->update($params);
        $collector->process();


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

    public function findXPath(Request $request) {
        $params = $request->all();

        try {
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
                "http" => [
                    "user_agent" => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:75.0) Gecko/20100101 Firefox/75.0'
                ]
            );
            $response = file_get_contents($params['http_url'], false, stream_context_create($arrContextOptions));

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($response);
            $xpath = new \DOMXPath($dom);

            $nodes = $xpath->query("//*[text()[contains(., '".$params['http_value']."')]]");

            if (sizeof($nodes) == 0 ) {
                return [
                    'status' => 'error',
                    'value' => 'Element not found'
                ];
            }

            $path = $nodes[0]->getNodePath();

            return [
                'status' => 'ok',
                'value' => $path
            ];
        }
        catch(\ErrorException $e) {
            return [
                'status' => 'error',
                'value' => $e->getMessage()
            ];

        }


    }

    public function test(Request $request) {

        $params = $request->all();

        $params = $this->prepareCalcFields($params);

        $data = Collector::getData($params);

        if (is_array($data) && isset($params['calculated']))
            $data = Stat::prepareCalcData($data, $params['calculated']);

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

        // Если нет записей
        if (sizeof($data) == 0) {
                return
                    [   'status' => 'problem',
                        'data' => '',
                        'problem' => 'Result is empty'
                    ];
        }

        return [
            'status' => 'ok',
            'data' => view('table', ['data' => $data])->render()
        ];
    }

    public function process($id) {


        $collector = Collector::findOrFail($id);

        if ($collector->type == 'api')
            return redirect()->route('collector.show', ['collector'=>$collector]);


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
            case 'table':
                            return view('stat.table', ['collector' => $collector, 'stat' => $stat]);
                            break;
            case 'json':
                            if (request()->withTools)
                                return view('stat.json', ['collector' => $collector, 'stat' => $stat]);
                            else
                                echo json_encode($stat, JSON_PRETTY_PRINT);

                            break;
            case 'lastValue':
                            $lastValue = reset($stat);
                            if ($lastValue)
                                unset($lastValue['dt']);

                            if (request()->withTools || ($lastValue && sizeof($lastValue)>1))
                                return view('stat.lastValue', ['collector' => $collector, 'lastValue' => $lastValue]);
                            elseif($lastValue)
                                echo $lastValue['value'];

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

            if ($collector->type == 'api')
                continue;

            echo date('Y-m-d H:i:s') . ' - ProcessAll ' . $collector->name;
            try {
                $collector->process();
            } catch (\Exception $e) {
             echo ' error ' . $e->getMessage() . PHP_EOL;
             continue;
            }

            echo 'ok' . PHP_EOL;
        }
    }

    public function putData(Collector $collector) {

        if ($collector->type == 'api') {
            $collector->process();
        }
    }
}
