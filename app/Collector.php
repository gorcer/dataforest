<?php

namespace App;

use App\Helpers\DatabaseConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $hash
 * @property string $name
 * @property string $data
 * @property string $created_at
 * @property string $last_check
 */
class Collector extends Model
{
    public $_stat=null;
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'email', 'hash', 'name', 'data', 'created_at', 'last_check', 'type', 'period'];
    const UPDATED_AT = null;

    public static function getData($params) {

        switch($params['type']) {
            case 'sql':


                    $params['connection'] = [
                        'driver' => 'mysql',
                        'host' => '127.0.0.1',
                        'port' => '3306',
                        'database' => $params['sql_dbname'],
                        'username' => 'admin',
                        'password' => 'admin',
                    ];


                        try {
                            $connection = DatabaseConnection::setConnection($params['connection']);
                            $stats = $connection->select($params['sql_query']);
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
                                $stats = [['value' => $node->nodeValue]];
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

    public function process() {

        $data = unserialize($this->data);

        // Получаем данные из вне
        $stats = self::getData($data);

        if (is_string($stats))
            return $stats;

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
            if (isset($item['dt']))
                $dt = $item['dt'];
            else {
                $dt=date('Y-m-d H:i:s');
                $item['dt'] = $dt;
            }
            $item['dt'] = new \MongoDB\BSON\UTCDateTime(strtotime($item['dt'])*1000);


            $item['collector_id']= $this->id;

            Stat::where(['collector_id'=>$this->id, 'dt'=>$dt])
                ->update($item, ['upsert' => true]);

         }


        $this->last_check = date('Y-m-d H:i:s');
        $this->save();

    }


    public function getStat($group=false) {

        if (!$group)
            $group = request()->group;

        $format = "%Y-%m-%d";
        switch($group) {
            case 'by_day': $format = "%Y-%m-%d"; break;
            case 'by_weeks': $format = "%Y / %V"; break;
            case 'by_month': $format = "%Y-%m"; break;
            case 'by_year': $format = "%Y"; break;
        }

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
                $group[$field] = ['$avg' => '$'.$field];
            }

            $cursor = Stat::raw()->aggregate([
                [
                    '$match' => [
                        'collector_id' => $this->id
                    ]
                ],
                ['$group' =>
                    $group,
                ]

            ]);

            $result=[];

            foreach ($cursor as $document) {
                $item=$document->getArrayCopy();
               // $item['dt'] = $item['_id'];
                $item['dt'] = $item['dt']->toDateTime()->format('Y-m-d');
                unset($item['_id']);
                $result[]=$item;
            }
        }

           // $this->_stat = Stat::where('collector_id', $this->id)->orderBy('dt', 'desc')->get();

        return $result;
    }

}
