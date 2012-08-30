
<!DOCTYPE html>
<html>
<head>
	<title>Testmap</title>
	<meta charset="utf-8" />
    <link rel="stylesheet/less" type="text/css" href="Styles/main.less">

    <script type="text/javascript" src="Scripts/less-1.3.0.min.js"></script>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript" src="Scripts/Map.js"></script>
	<script type="text/javascript">
    // Initialization function when the document is ready
    $(document).ready(function () {
        // Create map instance and load the map
        var map = new Map(1, 'map');
    });
    </script>
</head>
<body>
    <div id="container">
	   <div id="map" style="width: 75%; float: left;"></div>
       <div id="content" style="width: 15%; float: left;">
            Some content here
       </div>
    </div>
</body>
</html>
