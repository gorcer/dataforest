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

        $user_id = Auth::id();
        $collectors = Collector::where('user_id',$user_id)->get();


        return view('home', ['collectors' => $collectors]);
    }


    public function test() {
        dd( Stat::where('collector_id', '5ed462ed2f5d0a10f909d0a4')->count() );
    }
}
