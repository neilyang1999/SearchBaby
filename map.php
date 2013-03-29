<?php
/*************************************************************************
 File Name: map.php
 Author: chliny
 mail: chliny11@gmail.com
 Created Time: 2013年03月27日 星期三 14时47分55秒
************************************************************************/
class MyMap{

    private $x;
    private $y;
    private $mapDb;
    private $neighborGoods;

    public function createMap(){
        $header = '<!DOCTYPE html>
            <html>
            <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
            <style type="text/css">
            html { height: 100% }
            body { height: 100%; margin: 0; padding: 0 }
            #map_canvas { height: 100% }
            </style>
            <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxUjhEDD8KZSORjds-V1l21lBsP4r3Kt0&sensor=true">
    </script>' . $this->createScript() . '</head>';
  $body='
     <body onload="initialize()">
    <div id="map_canvas" style="width:100%; height:100%"></div>
  </body>
</html>';
    echo $header . $body;
    }

    private function createScript(){
        $script = '<script type="text/javascript">' . $this->initialize();
        $script .= '</script>';
        return $script;
    }

    private function initialize(){
        $function =  'function initialize() {
        var mapOptions = {
          center: '. $this->getLating($this->x,$this->y) . ',
          zoom: 18,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
        ' . $this->makeLocationIcon() . $this->makeGoodsIcon() . ' };';

        return $function;
    }

    private function makeLocationIcon(){
        $function = '
        var marker = new google.maps.Marker({
            position: ' . $this->getLating($this->x,$this->y) . ',
            title:"你的位置: ' . $this->x . ','. $this->y .'",
            map: map
        });';

        return $function; 
    }

    private function makeGoodsIcon(){
        $this->getGoods();
        if(empty($this->neighborGoods)){
            return;
        }
        $function ='
        var CircleIcon = {
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: "yellow",
            fillOpacity: 1,
            scale: 4,
            strokeColor: "gold",
            strokeWeight: 14
        };';
        foreach($this->neighborGoods as $value){
            $maker = '
            var marker = new google.maps.Marker({
                position:' . $this->getLating($value['x'],$value['y']) . ',
                title: "' . $value['name'] . ': ' . $value['x'] . ',' . $value['y'] .'",
                map: map,
                icon: CircleIcon
            });';
            $function .= $maker;
        }
        return $function;
    }

    private function getLating($x,$y){
        $lating = 'new google.maps.LatLng(' . $x . ',' . $y .')';
        return $lating;
    }

    private function getGoods(){
        $query = "select goods.name,map.x,map.y from goods inner join map on goods.id=map.goodid where (map.x<$this->x+0.01 and map.x>$this->x-0.01) and (map.y >= ($this->y-0.01) and map.y <= ($this->y+0.01))";
        $result = $this->mapDb->query($query);
        $goods = array();
        while($good = $this->mapDb->fetch_array($result)){
            $goods[] = $good;
        }
        $this->neighborGoods = $goods;
    }

    function __construct($x,$y){
        $this->x = $x;
        $this->y = $y;
        include_once 'Mysql.php';
        include_once 'config.php';
        $this->mapDb = new MyDatabase;
        $this->mapDb->connect($hostname,$dbuser,$dbpassword,$dbname);
        $this->mapDb->query("set names utf8");
    }
}

$locaX = $_GET['x'];
$locaY = $_GET['y'];
$newMap = new MyMap($locaX,$locaY);
$newMap->createMap();
?>
