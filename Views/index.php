
<!DOCTYPE html>
<html>
<head>
    <title>Testmap</title>
    <meta charset="utf-8" />
    <link rel="stylesheet/less" type="text/css" href="styles/main.less">

    <script type="text/javascript" src="scripts/less-1.3.0.min.js"></script>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=drawing"></script>
    <script type="text/javascript" src="scripts/Districts.js"></script>
    <script type="text/javascript" src="scripts/Map.js"></script>
    <script type="text/javascript" src="scripts/Editor.js"></script>
    <script type="text/javascript">
    // Initialization function when the document is ready
    $(document).ready(function () {
        // Create map instance and load the map
        var map    = new Map(1, 'map');
    });
    </script>
</head>
<body>
    <ul id="editor_controls">
        <li data-editormode="None">Default</li>
        <li data-editormode="AddDistrict">Add district</li>
        <li data-editormode="EditDistrict">Edit district</li>
    </ul>

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
                <button type="reset">Abbrechen</button>
                <button type="button" name="delete">Löschen</button>
            </fieldset>
        </form>

    </div>

</body>
</html>
