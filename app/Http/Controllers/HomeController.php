<?php

namespace App\Http\Controllers;

use App\Collector;
use App\Helpers\DatabaseConnection;
use App\Jobs\ProcessTask;
use App\Stat;
use App\User;
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
       // $collector = Collector::find('5ed462ed2f5d0a10f909d0a7');
       // dispatch(new ProcessTask($collector));

        //

        return view('home');
    }
}
