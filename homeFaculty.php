<?php 
session_start();

//connecting to DB
include "connectToDB.php";
include "dbfunctions.php";
require_once "navV2.php";


if(isset($_SESSION['uID']) && isset($_SESSION['uName'])) {
	//	$connected = 0;
?>
    <body>
    <!DOCTYPE html>
    <html lang="en">
    <meta charset="UTF-8">
    <title>Home: Faculty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-colors-metro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="apple-touch-icon" sizes="180x180" href="favicon_package_v0/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_package_v0/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_package_v0/favicon-16x16.png">
    <link rel="manifest" href="favicon_package_v0/site.webmanifest">
    <link rel="mask-icon" href="favicon_package_v0/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <!-- <body> -->
    <body onload="showLoc()">
	<div class="w3-container w3-text-metro-dark-blue">
		<div class="w3-container w3-center w3-padding-16">    
			<div class="w3-card w3-margin"> 
				<div class="w3-container w3-metro-dark-blue">
				<h4>Welcome @<?php echo $_SESSION['uName']; ?>!</h4>
				</div>

				<div class="w3-container w3-center" id="mapBttnID"> 
				<!-- <img src="full-map.jpg" style="height: 200px; width: 400px; max-width: 100%; max-height: 100%;" class="w3-margin-top"></br> -->
				<!-- <button class="w3-button w3-circle w3-xlarge w3-metro-dark-blue w3-margin" onclick="showLoc()"> -->
					<!-- <i class="fa-solid fa-location-dot"></i></button> -->
				</div>

				<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
				<div id="map" style="height: 50%; width: 100%; margin: auto; display: none;"></div>
			</div>
		</div>
	</div>
    <div class="w3-container w3-center">
<?php
	// $query="SELECT fID, fName, lName, gender from Faculty WHERE isAvailable=1";
	// $result=mysqli_query($db, $query);
	// if (mysqli_num_rows($result)>0) {
		// while ($rows=mysqli_fetch_assoc($result)) {
			// echo "<option value=".$rows["fID"].">".$rows["fName"]." ".$rows["lName"]." (".$rows["gender"].") </option>";
		// }
	// }
?>
	<!-- </select></br> -->
	<!-- <button class="w3-button w3-ripple w3-round-large w3-metro-dark-blue w3-margin-bottom w3-hover-green" style="width: 90%" type="submit">Connect <i class="fa-solid fa-handshake"></i></button></a></br> -->
		<?php
			$uID = $_SESSION['uID'];
			$fID = $_SESSION['fID'];
			$sID = $_SESSION['sID'];
			$pickUp = $_SESSION['pickUp'];	
			$dropOff = $_SESSION['dropOff'];	
			
			$query2 = $db->prepare("SELECT fName, lName, gender FROM User WHERE uID=?");
			$query2->bind_param('i', $sID);
			if($query2->execute()) {
				mysqli_stmt_bind_result($query2, $res_first, $res_last, $res_gen);
				if($query2->fetch()) {
					$fname=$res_first;
					$lname=$res_last;
					$gender=$res_gen;
					// echo "Connected with: ".$fname." ".$lname." (".$gender.")"; ?>
					<p style="font-style: italic;"><?php echo "Connected to: ".$fname." (".$gender.")"; ?></p>
					<!--<p style="font-style: italic;"><?php //echo "Pick-Up at: ".$pickUp." "; ?></p>
					<p style="font-style: italic;"><?php //echo "Drop-Off at: ".$dropOff." "; ?>--></p><?php
				}
			} else { echo mysqli_error($db); }
		?>	
		<a href="inbox.php"<button class="w3-button w3-ripple w3-round-large w3-metro-dark-blue w3-hover-green w3-margin-bottom" style="width: 90%">Inbox 
	    	<i class="fa fa-envelope"></i></button></a></br>
        <a href="connectFaculty.php"<button class="w3-button w3-ripple w3-round-large w3-metro-yellow w3-hover-green" style="width: 90%">End Walk 
            <i class="fa-solid fa-person-walking"></i></button></a>
    </div>
<script>
	//Variables
	var lon, lat;
	var map, marker, myIcon;
	var popUp;

	//Variables for Map Bounds
	var northEast = L.latLng(35.354082, -119.092462),
		southWest = L.latLng(35.339888, -119.114156),
		csubBounds = L.latLngBounds(northEast, southWest);

	function showLoc() {
		var x = document.getElementById("mapBttnID");
		var y = document.getElementById("map");

		if (navigator.geolocation) {
			setInterval(() => {
			navigator.geolocation.getCurrentPosition(works, error);
		}, 1000);
		} else {
			loc.innerHTML = "Geolocation not supported.";
            //<img src="full-map.jpg" style="height: 200px; width: 400px; max-width: 100%; max-height: 100%;" class="w3-margin-top"></br>;
		}

		if (x.style.display === "none") {
			x.style.display = "block";
		} else {
			x.style.display = "none";
		}

		if (y.style.display === "none") {
			y.style.display = "block";
		} else {
			y.style.display = "none";
		}
	}

	function works(loc) {
		lat = loc.coords.latitude;
		lon = loc.coords.longitude;
		getMap(lat, lon);
	}

	function error() {
		alert("Cannot get location");
	}

	function onMapClick(e) {
		popUp = L.popup();
		var newMarker = new L.marker(e.latlng, {
		draggable: true,
			autoPan: true
		});
		newMarker.on('dragend', function(e) {
			newMarker = e.target;
			var position = newMarker.getLatLng();
			newMarker.setLatLng(position, {
			draggable: true
			});
		});
		newMarker.addTo(map)
			.bindPopup('You clicked the map at ' + e.latlng.toString()).openPopup();
	}

	function getMap(lat, lon) {
		// Sets the Map View along with the Initial Zoom Level
		map = L.map("map").setView([lat, lon], 17);

		// Adds the Map Tile Layer
		var baseLayers = {
		"OpenStreetMap": L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
		//attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
			minZoom: 16,
			maxZoom: 25,
			id: 'mapbox/streets-v11',
			tileSize: 512,
			zoomOffset: -1,
			fitBounds: csubBounds,
			accessToken: 'pk.eyJ1IjoiZHZpbnRpIiwiYSI6ImNrend1enYxdjg5c3oybm5rdnRsNzAyMHIifQ.wpv77ZIRfk3mI1gyqoOSAg'
		}),
				"Satellite": L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", {
				//attribution: "Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community",
					minZoom: 16,
					maxZoom: 25
		})
		};

		// This makes the default map view to be OpenStreetMap
		this.map.addLayer(baseLayers["OpenStreetMap"]);

		// Adds the base layers to the map
		L.control.layers(baseLayers, null, {
			collapsed: true
		}).addTo(map);

		// Fit Bounds

		// User Marker for Student Icon
		// Student Icon
		myIcon = L.icon({
		iconUrl: 'maps/CSUBMark.png',
			iconSize: [40, 50],
			iconAnchor: [22, 94],
			popupAnchor: [-3, -76]
		});

		marker = L.marker([lat, lon], {icon: myIcon}).addTo(map)
			.bindPopup('<b>User Location</b>').openPopup();

		//Start at Bakersfield after User Marker is set
		//map.panTo(new L.LatLng(35.350056, -119.103599));

		// On double click, add a new marker
		map.on('dblclick', onMapClick);

		// Disable Double Click Zoom
		map.doubleClickZoom.disable();
	}
</script>
</body>
</html>

<?php
} else {
	header("Location: indexV2.php?error=You must be logged in to view this page");
	exit();
}
?>