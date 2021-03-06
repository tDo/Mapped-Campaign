<!DOCTYPE html>
<html>
<head>
    <title>Testmap</title>
    <meta charset="utf-8" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet/less" type="text/css" href="css/main.less">

    <script src="js/less-1.3.0.min.js"></script>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=false&libraries=geometry,drawing"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/Campaign/Messages.js"></script>
    <script src="js/Campaign/Districts.js"></script>
    <script src="js/Campaign/Locations.js"></script>
    <script src="js/Campaign/Map.js"></script>
    <script src="js/Campaign/Editor.js"></script>
    <script>
    // Initialization function when the document is ready
    $(document).ready(function () {
        // Create map instance and load the map
        var map    = new Campaign.Map(1, 'map_canvas', <?= $this->data['isLoggedIn'] ? 'true' : 'false'; ?>);

        <?php
          // If there are messages from the application, show them here
          if (isset($flash['error'])) {
        ?>
          Campaign.Messages.addError("Fehler! ", "<?= htmlspecialchars($flash['error']); ?>")

        <?php } else if (isset($flash['success'])) { ?>

          Campaign.Messages.addSuccess("Yay! ", "<?= htmlspecialchars($flash['success']); ?>")

        <?php } ?>
    });
    </script>
</head>
<body>
    
    <div id="alerts">
    </div>
    

    <div class="container-fluid">
        <div class="row-fluid">

            <div class="navbar span12">
                <div class="navbar-inner">
                    <a class="brand" href="/">Kampagnenspiel</a>
                    <ul class="nav">
                        <?php
                          // Only show editor menu if the user is even logged in
                          if ($this->data['isLoggedIn']) {
                        ?>
                        <li class="dropdown active">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="editorcaption">Default</span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu dropdown-inverse">
                                <li data-editormode="None"><a tabindex="-1" href="#"><i class="icon-eye-open"></i> Betrachten</a></li>
                                <li data-editormode="Edit"><a tabindex="-1" href="#"><i class="icon-edit"></i> Bearbeiten</a></li>
                                <li data-editormode="AddDistrict"><a tabindex="-1" href="#"><i class="icon-globe"></i> Lehen eintragen</a></li>
                                <li data-editormode="AddLocation"><a tabindex="-1" href="#"><i class="icon-map-marker"></i> Ort eintragen</a></li>
                            </ul>
                        </li>

                        <li><a href="<?= BASE_URI; ?>logout">Logout <small>(<?= htmlspecialchars($this->data['username']); ?>)</small></a></li>

                        <?php 
                          // If the user is not logged in, show a login form here
                          } else {
                        ?>

                          <form class="navbar-form pull-left" 
                                action="<?= BASE_URI; ?>login"
                                method="post">

                              <input type="text" class="span4" name="username" placeholder="Name"/>
                              <input type="password" class="span4" name="password" placeholder="Passwort"/>

                              <button type="submit" class="btn">Login</button>
                          </form>


                        <?php } ?>

                    </ul>
                </div>
            </div>

        </div>

        <div class="row-fluid">
        
            <div class="span9">
                <div id="map_canvas"></div>
            </div>

            <div id    = "sidebar"
                 class = "span3">

                <!-- Content container for actually displaying stored data -->
                <div id="content">
                  <h1 id="content_name"></h1>
                  <p id="content_description"></p>
                </div>

                <!-- Form for adding/editing districts -->
                <form id     = "district_form"
                      class  = "edit_form"
                      method = "post"
                      action = "#">

                    <button type="reset" class="close">&times;</button>
                    <legend>Lehen bearbeiten</legend>
                    <div class="control-group">
                        <label for="district_name">Name</label>
                        <input type        = "text"
                               name        = "name"
                               id          = "district_name"
                               value       = ""
                               class       = "span10"
                               placeholder = "Name des Lehens" />

                        <label for="district_description">Beschreibung</label>
                        <textarea name        = "description"
                                  id          = "district_description"
                                  class       = "span10"
                                  placeholder = "Beschreibung des Lehens"
                                  rows        = "10"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Speichern</button>
                    <button type="button" class="btn btn-danger btn-mini" name="delete"><i class="icon-trash icon-white"></i> Löschen</button>
                </form>

                <!-- Form for adding/editing locations -->
                <form id     = "location_form"
                      class  = "edit_form"
                      method = "post"
                      action = "#">

                    <button type="reset" class="close">&times;</button>
                    <legend>Ort bearbeiten</legend>

                    <div class="control-group">
                        <label for="location_name">Name</label>
                        <input type        = "text"
                               name        = "name"
                               id          = "location_name"
                               value       = ""
                               class       = "span10"
                               placeholder = "Name des Ortes" />

                        <label for="location_description">Beschreibung</label>
                        <textarea name        = "description"
                                  id          = "location_description"
                                  class       = "span10"
                                  placeholder = "Beschreibung des Ortes"
                                  rows        = "10"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Speichern</button>
                    <button type="button" class="btn btn-danger btn-mini" name="delete"><i class="icon-trash icon-white"></i> Löschen</button>
                </form>

            </div>

        </div>

    </div>
</body>
</html>
