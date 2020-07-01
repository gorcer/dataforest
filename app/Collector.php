<?php

namespace App;

use App\Helpers\DatabaseConnection;
use Jenssegers\Mongodb\Eloquent\Model;
use \MongoDB\BSON\UTCDateTime;

/**
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $hash
 * @property string $name
 * @property string $created_at
 * @property string $last_check
 */
class Collector extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'collector';

    public $_stat=null;
    protected $guarded = [];


    const UPDATED_AT = null;

    public static function getData($params) {

        switch($params['type']) {
            case 'sql':

                   // ssh -N -L 3336:127.0.0.1:3306 doghouse-new
                   // ssh -N -L 27018:127.0.0.1:27017 doghouse-new


                $connection = parse_url($params['db_connection']);

                    $connection = [
                        'driver' => $connection['scheme'],
                        'host' => $connection['host'] . ':' . $connection['port'],
                        'database' => substr($connection['path'], 1),
                        'username' => $connection['user'],
                        'password' =>  $connection['pass'],
                    ];


                        try {
                            $connection = DatabaseConnection::setConnection($connection);
                            $stats = $connection->select($params['db_query']);
                        } catch(\Illuminate\Database\QueryException $e) {
                            return $e->getMessage();
                        }

                        $stats = array_map(function ($value) {
                            return (array)$value;
                        }, $stats);

                        break;

            case 'http':

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

                                $nodes = $xpath->query($params['http_xpath']);

                                if (sizeof($nodes) == 0) {
                                    return 'Your query result is empty, try to change selector.';
                                }
                                $node = $nodes[0];

                                //$stats = [['value' => (int)str_replace(' ','',$nodes[0]->nodeValue)]];
                                $stats = [
                                    [
                                        'value' => $node->nodeValue
                                    ]
                                ];
                            }
                            catch(\ErrorException $e) {
                                    return $e->getMessage();
                                }

                         break;
        }


        return $stats;

    }
    public function getFields() {
        $stat = Stat::where('collector_id', $this->id)->orderBy('dt','desc')->limit(1)->first();
        if ($stat == null)
            return [];
        $result = $stat->toArray();
        unset($result['dt']);
        $result=array_keys($result);

        foreach($result as $k=>$item) {
            $result[$item]=$item;
            unset($result[$k]);
        }

        return $result;
    }

    // Обновляем стуктуру
    public function migrate() {
        if (is_string($this->data)) {
            $data = unserialize($this->data);
            $this->data = $data;
        }

        if (isset($this->data) && $this->data != null) {
            foreach($this->data as $k=>$v) {
                $this->$k=$v;
            }
            $this->data=null;
        }



        if (isset($this->attributes['connection']))
            $this->attributes['connection']=null;

        if (isset($this->attributes['sql_dbname']))
            $this->attributes['sql_dbname']=null;

        if (isset($this->attributes['sql_query'])) {
            $this->db_query = $this->sql_query;
            $this->attributes['sql_query']=null;
        }

        $this->save();


    }

    public function process() {

        $this->migrate();

        // Получаем данные из вне
        $stats = self::getData($this);

        if (!is_array($stats))
            echo $stats;

        foreach($stats as $item) {

                foreach($item as $key => &$value) {
                    // все кроме даты делаем числом
                    if ($key !== 'dt')
                        $value= (float)str_replace(" ", "", $value);

                    // Убираем точку из названия столбца
                    if (strpos($key, '.') !== false) {

                        unset($item[$key]);

                        $key = str_replace('.',' ',$key);
                        $item[$key] = $value;
                    }

                    // Если 2 значения, то второе называем value
                    if (sizeof($item)<=2 && $key != 'dt' && $key != 'value') {
                        unset($item[$key]);
                        $item['value'] = $value;
                    }
                }


            // Если нет даты, то добавляем
            if (!isset($item['dt'])) {
                $dt=date('Y-m-d H:i:s');
                $item['dt'] = $dt;
            }


            //Приводим дату к формату
            switch($this->period) {
                case 'hourly':
                    $item['dt'] = date('Y-m-d H:00:00', strtotime($item['dt']));
                    break;
                case 'daily':
                    $item['dt'] = date('Y-m-d 00:00:00', strtotime($item['dt']));
                    break;
                case 'weekly':
                        $item['dt'] = date('Y-m-d 00:00:00', strtotime('last monday', strtotime($item['dt'])));
                    break;
                case 'monthly':
                    $item['dt'] = date('Y-m-01 00:00:00', strtotime($item['dt']));
                    break;
            }


            // Обрезаем таймзону
            $item['dt'] = strtotime($item['dt'] . ' ' . 'GMT+00:00');

            $item['dt'] = new UTCDateTime($item['dt']*1000);


            $item['collector_id']= $this->id;

            Stat::where(['collector_id'=>$this->id, 'dt'=>$item['dt']])
                ->update($item, ['upsert' => true]);
         }

        $this->last_check = new UTCDateTime();
        $this->save();

    }


    public function getStat($group=false) {

        if (!$group)
            $group = request()->group;

        $format = "%Y-%m-%d";
        switch($group) {
            case 'by_hours': $format = "%Y-%m-%d %H"; break;
            case 'by_day': $format = "%Y-%m-%d"; break;
            case 'by_weeks': $format = "%Y / %V"; break;
            case 'by_month': $format = "%Y-%m"; break;
            case 'by_year': $format = "%Y"; break;
        }
        // Для вывода на график
        $outputFormat=str_replace('%', '', $format);
        if ($group == 'by_weeks')
            $outputFormat='Y-m-d';



        switch(request()->get('period', '7_days')) {
            case 'today':
                         $start = date('Y-m-d 00:00:00');
                         $end = date('Y-m-d 00:00:00', strtotime("+1 day"));
                         break;
            case 'yesteraday':
                        $start = date('Y-m-d 00:00:00', strtotime("-1 day"));
                        $end = date('Y-m-d 00:00:00');
                        break;
            case '7_days':
                        $start = date('Y-m-d 00:00:00', strtotime("-7 day"));
                        $end = date('Y-m-d 00:00:00', strtotime("+1 day"));
                        break;
            case '30_days':
                        $start = date('Y-m-d 00:00:00', strtotime("-30 day"));
                        $end = date('Y-m-d 00:00:00', strtotime("+1 day"));
                        break;
            case 'this_month':
                        $start = date('Y-m-01 00:00:00', strtotime("-30 day"));
                        $end = date('Y-m-d 00:00:00', strtotime("+1 day"));
                        break;
            case 'last_month':
                        $start = date('Y-m-01 00:00:00', strtotime("-1 month"));
                        $end = date('Y-m-1 00:00:00');
                        break;
            case 'this_year':
                        $start = date('Y-01-01 00:00:00');
                        $end = date('Y-m-d 00:00:00', strtotime("+1 day"));
                        break;

        }

        // Обрезаем таймзону
        $start = strtotime($start . ' ' . 'GMT+00:00');
        $end = strtotime($end . ' ' . 'GMT+00:00');

        $start = new UTCDateTime($start*1000);
        $end = new UTCDateTime($end*1000);

        if ($this->_stat == null) {

            $fields = $this->getFields();
            if (sizeof($fields) == 0)
                return [];

            $group = [
                "_id" => [ '$dateToString' => [ "format" => $format, "date" => '$dt' ],
                ],
                "dt"    => ['$min' => '$dt'],
            ];
            foreach($fields as $field) {

                if (isset($this->aggregate) && isset($this->aggregate[$field])) {
                    $group[$field] = ['$' . $this->aggregate[$field] => '$'.$field];
                } else {
                    $group[$field] = ['$avg' => '$'.$field];
                }


            }

            $cursor = Stat::raw()->aggregate([
                [
                    '$match' => [
                        'collector_id' => $this->id,
                        'dt' => [
                            '$gt' => $start,
                            '$lt' => $end
                        ]
                    ]
                ],
                ['$group' =>
                    $group,
                ],
                [
                    '$sort' => [
                        'dt' => -1
                    ]
                ]

            ]);

            $result=[];


            foreach ($cursor as $document) {
                $item=$document->getArrayCopy();

               // $item['dt'] = $item['_id'];
                $item['dt'] = $item['dt']->toDateTime()->format($outputFormat);
/*
                $dateTime = new \DateTime();
                $dateTime->setTimestamp($item['dt']);
                $item['dt'] = $dateTime->format('Y-m-d H');
*/
                unset($item['_id']);
                $result[]=$item;

            }


        }

           // $this->_stat = Stat::where('collector_id', $this->id)->orderBy('dt', 'desc')->get();

        return $result;
    }

    public function statCount() {
        return Stat::where('collector_id', $this->id)->count();
    }



}
