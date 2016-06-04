<?php
$locs = array();
$handle = fopen("locs.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) 
	{
		$vars = explode("/",$line);
		$vars[2] = str_replace(" ","",$vars[2]);
		$vars[2] = str_replace(",",", ",$vars[2]);
		$locs[$vars[0]] = array($vars[0],$vars[2],$vars[1]);
    }

    fclose($handle);
}

function sortbydateinfile($a,$b)
{
	//DC112_2015:04:01.jpg
	$f1 = substr($a[0],6,-4);
	$f2 = substr($b[0],6,-4);
	//echo $f1;
	return $f1 > $f2;
}
function sortbyfilename($a,$b)
{
	//DC112_2015:04:01.jpg
	$f1 = substr($a[0],0,-4);
	$f2 = substr($b[0],0,-4);
	//echo $f1;
	return $f1 > $f2;
}
//usort($locs,"sortbyfilename"); 
usort($locs,"sortbydateinfile"); 
//die();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Simple Polylines</title>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
    </style>
  </head>
  <body>
    <script src="https://maps.googleapis.com/maps/api/js"></script>
<div id="map"></div>
    <script>
		var locations = [
		  //Starting Point is "Home", and links to other locations"//
		  /*['Home', 37.774930, -122.419416],
		  ['a', 35.689487, 139.691706],
		  ['b', 48.856614, 2.352222],
		  ['c', -33.867487, 151.206990]*/
		  <?php
			foreach($locs as $key=>$loc)
			{
				echo "['{$loc[0]}', {$loc[1]}],\n";
			}
		  ?>
		]

		function initMap() {
		  var myLatLng = {
			lat: 12.363,
			lng: -131.044
		  };
		  var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 3,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: {
			  lat: 0,
			  lng: 0
			}
		  });
		var markers = [];
		var bounds = new google.maps.LatLngBounds();
		// Add the markers and infowindows to the map
	
		  for (var i = 0; i < locations.length; i++) 
		  {
			var marker = new google.maps.Marker({
			  position: new google.maps.LatLng(locations[i][1], locations[i][2]),
			  title: locations[i][0],
			  map: map
			})
			markers.push(marker); // push the marker onto the array
			bounds.extend(marker.getPosition());
			
			if (i > 0) { // move this inside the marker creation loop
			  var sitepath = new google.maps.Polyline({
				// use the markers in for the coordinates
				path: [markers[i-1].getPosition(), marker.getPosition()],
				geodesic: false,
				strokeColor: '#FF0000',
				strokeOpacity: 1.0,
				strokeWeight: 2,
				map: map
			  });
			 console.log("adding connection from",i-1,locations[i-1][0]," to ",i,locations[i][0])
			}
			
		  }
		  map.fitBounds(bounds);
		}
		google.maps.event.addDomListener(window, "load", initMap);
    </script>
  
  </body>
</html>
