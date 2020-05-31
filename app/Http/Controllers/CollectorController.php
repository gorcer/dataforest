<?php

namespace App\Http\Controllers;

use App\Collector;
use App\Helpers\DatabaseConnection;
use App\Stat;
use Illuminate\Http\Request;

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
        //
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

        // Убираем лишнее
        $data = $params;
        unset($data['email']);
        unset($data['_token']);
        unset($data['name']);
        unset($data['period']);
        $data = serialize($data);

        $collector = Collector::create([
            'user_id' => 0,
            'email' => $params['email'],
            'hash' => md5($params['email'] . $data),
            'name' => $params['name'],
            'type' => $params['type'],
            'period' => $params['period'],
            'data' => $data
        ]);

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
        $collector = Collector::findOrFail($id);
        $allCollectors = Collector::get();

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
}
