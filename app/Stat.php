<?php

namespace App;


use FormulaParser\FormulaParser;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Stat extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'stat';
    protected $guarded = [];

    protected $hidden = array('_id', 'collector_id', 'updated_at', 'collection_id');
    protected $dates = ['dt', 'last_check'];


    public static function prepareCalcData($data, $fields) {

        if (sizeof($fields) == 0 || sizeof($data) == 0) {
            return $data;
        }
        
        foreach($data as &$row) {

            foreach($fields as $field => $command) {

                $command = str_replace('"',"'", $command);

                foreach($row as $comField => $value) {
                    $command = str_replace(["'" . $comField . "'", $comField], $value, $command);
                }


                try {
                    $parser = new FormulaParser($command, 2);
                    $parser->setVariables($data);
                    $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]
                } catch (\Exception $e) {
                    $result = false;
                }

                if ($result[0] == 'done') {
                    $row[$field] = $result[1];
                } else {
                    return 'Error in field ' . $field .': ' . $result[1];
                }

            }

        }


        return $data;
    }
}
