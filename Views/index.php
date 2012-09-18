
<!DOCTYPE html>
<html>
<head>
    <title>Testmap</title>
    <meta charset="utf-8" />
    <link rel="stylesheet/less" type="text/css" href="Styles/main.less">

    <script type="text/javascript" src="Scripts/less-1.3.0.min.js"></script>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=drawing"></script>
    <script type="text/javascript" src="Scripts/Districts.js"></script>
    <script type="text/javascript" src="Scripts/Map.js"></script>
    <script type="text/javascript" src="Scripts/Editor.js"></script>
    <script type="text/javascript">
    // Initialization function when the document is ready
    $(document).ready(function () {
        // Create map instance and load the map
        var map    = new Map(1, 'map');
        /*var editor = new Editor(map);

        // Testwise bind to district creation form
        /*$('#district_create').bind('submit', function() {
            $.ajax({
                type: "post",
                url:  "district/add/",
                data: { region_id:   1,
                        name:        $('#district_name').val(),
                        description: $('#district_description').val(),
                        polygon:     editor.polygonToJson()
                }//,
                //dataType: "json"
            }).done(function() {
              alert( "Data Saved: ");
            });
            return false;
        });*/
    });
    </script>
</head>
<body>
    <div id="map"></div>

    <article id="content">
        <h1 id="headline"></h1>
        <div id="description"></div>
    </article>

    <div id="forms">

        <form id     = "district_form"
              method = "post"
              action = "#">

            <fieldset>
                <legend>Lehen</legend>

                <label>Name</label>
                <input type  = "text"
                       name  = "name"
                       value = "" />

                <label>Beschreibung</label>
                <textarea name = "description"></textarea>

                <button type="submit">Speichern</button>
            </fieldset>
        </form>

    </div>

</body>
</html>
