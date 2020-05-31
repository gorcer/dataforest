<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Stat extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'stat';
    protected $guarded = [];

    protected $hidden = array('_id', 'collector_id', 'updated_at', 'collection_id');
    protected $dates = ['dt'];
}
