<?php
/*************************************************************************
 File Name: map.php
 Author: chliny
 mail: chliny11@gmail.com
 Created Time: 2013年03月27日 星期三 14时47分55秒
 ************************************************************************/
class MyMap{
   public function createMap($x,$y){
        echo '<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map_canvas { height: 100% }
    </style>
    <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxUjhEDD8KZSORjds-V1l21lBsP4r3Kt0&sensor=true">
    </script>
    <script type="text/javascript">
      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng('.$x .',' . $y.'),
          zoom: 18,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
      }
    </script>
  </head>
  <body onload="initialize()">
    <div id="map_canvas" style="width:100%; height:100%"></div>
  </body>
</html>';
    }
}

$locaX = $_GET['x'];
$locaY = $_GET['y'];
$newMap = new MyMap;
$newMap->createMap($locaX,$locaY);
?>
