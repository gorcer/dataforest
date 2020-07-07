<?php

namespace App\Http\Controllers;

use App\Collector;
use App\Helpers\DatabaseConnection;
use App\Jobs\ProcessTask;
use App\Stat;
use App\User;
use FormulaParser\FormulaParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $collectors = Collector::where('user_id',Auth::id())->get();


        return view('home', ['collectors' => $collectors]);
    }


    public function test() {
        $parser = new FormulaParser('1/0', 2);
        $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]

        dd($result);
    }
}
