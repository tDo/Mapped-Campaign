/**
 * This file holds methods and classes used for district handling
 * like managing polygons and districts themselves
 */

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
        paths: coords,
        fillOpacity: 0.05
      });
}

function Districts(map) {
    var self = this;
    this.map = map;

    // Adds a ditrict from storage to the map and already assigns all required data
    this.add = function(district) {
        // verify the data structure a bit
        if (district == undefined) return false;
        if (district.polygon == undefined || district.id == undefined || district.name == undefined) return false;

        // Create the polygon and add it to the map
        var poly = district.polygon.toPolygon();
        poly.set('id',   district.id);
        poly.set('name', district.name);

        // Bind event handlers
        google.maps.event.addListener(poly, 'mouseover', function() {
            console.log("Name: "+ poly.get("name"));
            console.log("Id: "+ poly.get("id"));
        });

        // On click select for editing (If currently no other one is being editied)
        google.maps.event.addListener(poly, 'click', function() {
              self.edit(poly);
        });

        poly.setMap(self.map.map);
    };

    this.editing        = false;
    this.editingPolygon = null;

    // Will start editing a polygon
    // Editing may be "editing of a new district" or "editing of an existant district"
    // this is based on the data stored for the given polygon
    this.edit = function(polygon) {
        // If we are already editing something or the provided polygon is invalid, stop right here
        if (polygon == undefined) return false;
        if (this.isEditing) return false;

        this.isEditing      = true;
        this.editingPolygon = polygon;
        polygon.setEditable(true);

        // Is this a new one or an existing polygon...
        if (!polygon.isNew()) {
            $.get('district/'+ polygon.get('id'), function(data) {
                $('#district_form input[name=name]').val(data.name);
                $('#district_form textarea[name=description]').val(data.description);
                $('#forms').show();
            }, 'json')
            // Existing one (TODO: Load data from that)
            //alert("So, not new?");
        } else {
            // A New one
            $('#forms').show();
        }
    };

    // Bind to save event of the editing form, so we can submit the data
    $('#district_form').bind('submit', function() {
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
                type: type,
                url:  url,
                data: data
                //dataType: "json"
            }).done(function() {
                alert( "Data Saved");
                self.isEditing      = false;
                self.editingPolygon = null;
            });

        return false;
    });

}