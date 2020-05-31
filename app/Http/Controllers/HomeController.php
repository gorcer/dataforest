<?php

namespace App\Http\Controllers;

use App\Collector;
use App\Helpers\DatabaseConnection;
use App\Stat;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $c = Collector::whereId(14)->first();

        $group = [
            "_id" => [ '$dateToString' => [ "format" => "%Y-%m-%d", "date" => '$dt' ],
            ],
            "dt"    => ['$min' => '$dt'],
        ];
        foreach($c->getFields() as $field) {
            $group[$field] = ['$sum' => '$'.$field];
        }

        $cursor = Stat::where('collector_id', 14)->raw()->aggregate([
            ['$group' =>
                $group,
            ],
        ]);

        $result=[];
        foreach ($cursor as $document) {
            $result[]=$document->getArrayCopy();
        }

        return view('home');
    }
}
