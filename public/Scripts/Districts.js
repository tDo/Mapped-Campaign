/**
 * This file holds methods and classes used for district handling
 * like managing polygons and districts themselves
 */
var Campaign = Campaign || {};

/**
 * Converts a polygon to a json representation
 * @return string jsonrepresentation of a polygon path
 * (Extending polygons)
 */
google.maps.Polygon.prototype.toJson = function() {
    return JSON.stringify(this.getPath().b);
}

/**
 * Extensions checks if this is a new entry or if the entry was already
 * assigned to a district
 * @return {Boolean} Wether the polygon has been saved for a district or not
 */
google.maps.Polygon.prototype.isNew = function() {
    var id = this.get('id');
    return id == undefined;
}

/**
 * Converts a json ecnoded polygon path back to a polygon (Extending strings)
 * @return {google.maps.Polygon} Converts a json string to a polygon
 */
String.prototype.toPolygon = function() {
    var coords = new Array();

    try {
        // Try to parse the representation
        var parsed = JSON.parse(this);
        // And loop through the values and create points for the polygon
        $.each(parsed, function(i, curr) {
            coords.push(new google.maps.LatLng(curr.Xa, curr.Ya));
        });
    } catch (err) {
        // On failure reset it
        coords = new Array();
    }

    // Finally return the polygon
    return new google.maps.Polygon({
        paths:        coords
      });
}

/**
 * District handler. Holds options to add, edit, delete and show districts
 * on the map. Will also make sure that the correct editors are called
 * and the correct actions on the api.
 * @param {Map} map The map handling instance
 */
Campaign.Districts = function(map) {
    var self = this;
    this.map = map;

    // Function handles "Adding polygons to the map"
    // This might be done with existing or new polygons likewise
    // exisiting polygons just retrieve event resets
    // Also the styles of the polygons will be applied here
    this.addPolygon = function(polygon) {
        // Clear all listeners beforehand
        google.maps.event.clearInstanceListeners(polygon);

        // Bind event handlers
        google.maps.event.addListener(polygon, 'mouseover', function() {
            if (!self.map.editor.isEditing())
                polygon.set('fillOpacity', 0.25);
        });

        google.maps.event.addListener(polygon, 'mouseout', function() {
            if (!self.map.editor.isEditing())
                polygon.set('fillOpacity', 0.05);
        });

        // On click select for editing (If currently no other one is being editied)
        google.maps.event.addListener(polygon, 'click', function() {
            if (!self.map.editor.isEditing())
                // Only edit if we are in district editing mode
                if (self.map.editor.getMode() == EditorModes.EditDistrict)
                    // We are in editing mode for districts, show editor
                    self.edit(polygon);
                else if (self.map.editor.getMode() == EditorModes.None) {
                    // Default mode, show infos
                    console.log("Name: "+ polygon.get("name"));
                    console.log("Id: "+ polygon.get("id"));
                }
        });

        // Apply polygon styles
        polygon.set('strokeColor',   '#fff');
        polygon.set('strokeWeight',  2);
        polygon.set('strokeOpacity', 0.25);
        polygon.set('fillColor',     '#000');
        polygon.set('fillOpacity',   0.05);

        polygon.setMap(self.map.map);
    };

    // Adds a ditrict from storage to the map and already assigns all required data
    this.add = function(district) {
        // verify the data structure a bit
        if (district == undefined) return false;
        if (district.polygon == undefined || district.id == undefined || district.name == undefined) return false;

        // Create the polygon and add it to the map
        var polygon = district.polygon.toPolygon();
        polygon.set('id',   district.id);
        polygon.set('name', district.name);

        self.addPolygon(polygon);
    };

    this.editing        = false;
    this.editingPolygon = null;
    this.originalPath   = null;

    // Will start editing a polygon
    // Editing may be "editing of a new district" or "editing of an existant district"
    // this is based on the data stored for the given polygon
    this.edit = function(polygon) {
        // If we are already editing something or the provided polygon is invalid, stop right here
        if (polygon == undefined) return false;
        if (self.map.editor.isEditing()) return false;

        self.isEditing      = true;
        self.editingPolygon = polygon;
        polygon.setEditable(true);

        // Is this a new one or an existing polygon...
        if (!polygon.isNew()) {
            // Exisitng entry
            // Store current path of the polygon (In case we wish to reset it later on)
            // Yes this is done by a conversion in between since else the points would be handled
            // by reference which means that they hold all translations made. This way we get a clean
            // copy.
            self.originalPath = polygon.toJson().toPolygon().getPath();

            // Load the data to show
            $.get('district/'+ polygon.get('id'), function(data) {
                $('#district_form input[name=name]').val(data.name);
                $('#district_form textarea[name=description]').val(data.description);
                $('#forms').show();
            }, 'json')
        } else {
            // A New one, just show the forms
            $('#district_form input[name=name]').val("");
            $('#district_form textarea[name=description]').val("");
            $('#forms').show();
        }
    };

    // Cancels the editing process
    // In case a new district was currently being created the polygon will be removed from the map.
    // Also the forms will be hidden and any changes made just be reset
    this.cancel = function() {
        if (!self.isEditing) return false;

        if (self.editingPolygon) {
            // Reset editing mode
            self.editingPolygon.setEditable(false);
            // If this is a new polygon, also remove it from the map
            if (self.editingPolygon.isNew()) {
                self.editingPolygon.setMap(null);
            } else {
                // If not, restore the path
                if (self.originalPath)
                    self.editingPolygon.setPath(self.originalPath);
                // And readd the polygon
                self.addPolygon(self.editingPolygon);
            }
        }

        self.isEditing      = false;
        self.editingPolygon = null;
        self.originalPath   = null;
        $('#forms').hide();
    };

    // Delete the currently edited polygon/district
    this.delete = function() {
        if (!self.isEditing || !self.editingPolygon) return false;

        // Small closure handler to call when the process is done
        var done = function() {
            self.editingPolygon.setMap(null);
            self.isEditing      = false;
            self.editingPolygon = null;
            self.originalPath   = null;
            $('#forms').hide();
        }

        if (self.editingPolygon.isNew()) {
            // A new entry must just be removed from the map
            done();
        } else {
            // If the entry is not new, we must tell the server so
            $.ajax({
                type:     'DELETE',
                url:      'district/delete/'+ self.editingPolygon.get('id'),
                dataType: 'json'
            }).done(function(data) {
                if (data.ok == "ok") {
                    // Deleted and done
                    done();
                } else {
                    // Something went wrong...
                    alert(data.error.message);
                }
            })
        }
    }

    // Bind to save event of the editing form, so we can submit the data
    $('#district_form').on('submit', function() {
        // Just stop here is we are not editing anything
        if (!self.isEditing || !self.editingPolygon) return false;

        // Stop editing mode
        self.editingPolygon.setEditable(false);

        // Serialize the form data
        var data = {};
        jQuery.map($(this).serializeArray(), function(n, i) {
            data[n['name']] = n['value'];
        });

        // For now this is fix...
        data.region_id = 1;

        // And serialize the polygon
        data.polygon   = self.editingPolygon.toJson();

        // Based on wether this is an existant district or a new one
        // the url as well as the parameters will change
        var type = 'POST';
        var url  = 'district/add/';
        if (!self.editingPolygon.isNew()) {
            // Not a new one: Editing
            type             = "PUT";
            url              = 'district/edit/';
            data.district_id = self.editingPolygon.get('id');
        }

        $.ajax({
                type:     type,
                url:      url,
                data:     data,
                dataType: "json"
            }).done(function(data) {
                if (data.ok == "ok") {
                    // Was ok, was saved, we are done
                    self.isEditing      = false;

                    // Update the entity infos for the polygon
                    self.editingPolygon.set('id',   data.entity.id);
                    self.editingPolygon.set('name', data.entity.name);
                    self.addPolygon(self.editingPolygon);

                    self.editingPolygon = null;
                    self.originalPath   = null;
                    $('#forms').hide();
                } else {
                    // Errors occured
                    alert(data.error.message);
                    // TODO: Some field highlighting would be nice...
                }
            });

        return false;
    });
    
    // Bind to reset handler
    $('#district_form').on('reset', function() {
        // And just call the cancel procedure
        self.cancel();
    });

    // Bind to delete handler
    $('#district_form *[name=delete]').on('click', function() {
        self.delete();
    });
}