var Campaign = Campaign || {};

google.maps.Marker.prototype.isNew = function() {
    var id = this.get('id');
    return id == undefined;
}

google.maps.Marker.prototype.clonePosition = function() {
    var pos = this.getPosition();
    return new google.maps.LatLng(pos.lat(), pos.lng());
}

Campaign.Locations = function(map) {
    var self             = this;
    this.map             = map;
    var markers          = new google.maps.MVCArray();
    this.isEditing       = false;
    var editingMarker    = null;
    var originalPosition = null;

    var indexOfMarker = function(marker) {
        return markers.getArray().indexOf(marker);
    }

    var setDraggable = function(value, exceptFor) {
        var l = markers.getLength();
        for (var i = 0; i < l; i++) {
            var marker = markers.getAt(i);
            if (marker != exceptFor)
                marker.setDraggable(value);
        }
    }

    var addMarker = function(marker) {
        google.maps.event.clearInstanceListeners(marker);

        google.maps.event.addListener(self.map.editor, 'mode_changed', function() {
            if (!self.isEditing)
                if (self.map.editor.getMode() == EditorModes.Edit)
                    setDraggable(true);
                else
                    setDraggable(false);
        });

        google.maps.event.addListener(marker, 'mousedown', function(args) {
            if (!self.map.editor.isEditing()) {
                if (self.map.editor.getMode() == EditorModes.Edit) {
                    // We are in editing mode, show editor
                    self.edit(marker);                    
                    
                } else if (self.map.editor.getMode() == EditorModes.None) {
                    // Default mode, show infos
                    console.log("Name: "+ marker.get("name"));
                    console.log("Id: "+ marker.get("id"));
                }
            }
        });


        google.maps.event.addListener(marker, 'dragend', function(args) {
            var district = self.map.districts.getDistrictAt(marker.getPosition());
            console.log(self.isEditing);
            if (district == undefined || !self.isEditing) {
                console.log("Oh noe, not here! TODO: Real messge to user!");
                args.stop();
            }
        });

        

        removeMarker(marker);
        markers.push(marker);
        marker.setMap(self.map.map);
    }

    var removeMarker = function(marker) {
        marker.setMap(null);
        var index = indexOfMarker(marker);
        if (index > -1)
            markers.removeAt(index);
    };

    this.isValidDropPosition = function(latLng) {
        return self.map.districts.getDistrictAt(latLng) != undefined;
    };



    this.add = function(districtId, location) {
        // verify the data structure a bit
        if (districtId == undefined || location == undefined) return false;
        if (location.id == undefined || location.name == undefined ||
            location.x == undefined  || location.y == undefined) return false;

        var pos      = new google.maps.LatLng(location.y, location.x);
        // TODO: Check if we can find a way to wait for the google maps api to be completely loaded so we can do district
        // checks here as well
        //var district = self.map.districts.getDistrictAt(pos);
        //if (district == undefined) return false;
        //if (district.get('id') != districtId) return false;

        var marker = new google.maps.Marker({
            position:  pos,
            draggable: false
        });

        marker.set('id', location.id);
        marker.set('name', location.name);

        addMarker(marker);
    };

    this.edit = function(marker) {
        // Check some of the preconditions
        if (marker == undefined) return false;
        if (self.map.editor.getMode() != EditorModes.Edit) return false;
        if (self.map.editor.isEditing()) return false;

        self.isEditing   = true;
        editingMarker    = marker;
        originalPosition = marker.clonePosition();

        // And disable drag for all other markers
        setDraggable(false, marker);

        if (!marker.isNew()) {
            // Load data to show
            $.get('location/'+ marker.get('id'), function(data) {
                $('#location_form input[name=name]').val(data.name);
                $('#location_form textarea[name=description]').val(data.description);
                $('#location_form').show();
            }, 'json');
        } else {
            // A New one, just show the forms
            $('#location_form input[name=name]').val("");
            $('#location_form textarea[name=description]').val("");
            $('#location_form').show();
        }
    };

    this.cancel = function() {
        // make sure that we are editing
        if (!self.isEditing) return false;

        if (editingMarker) {
            // If it is a new marker, just remove it
            if (editingMarker.isNew()) {
                removeMarker(editingMarker);
            } else {
                // If it is not new, restore old position
                editingMarker.setPosition(originalPosition);
                // And readd the marker
                addMarker(editingMarker);
            }
        }

        // Finally reset the editing status
        self.isEditing   = false;
        editingMarker    = null;
        originalPosition = null;
        setDraggable(true);
        $('#location_form').hide();
    };

    this.delete = function() {
        // To be done...
    };

    // Bind to save event of the editing form, so we can submit the data
    $('#location_form').on('submit', function() {
        // Just stop here if we are not editing anything
        if (!self.isEditing || !editingMarker) return false;

        // Serialize the form data
        var data = {};
        jQuery.map($(this).serializeArray(), function(n, i) {
            data[n['name']] = n['value'];
        });

        // Type is fixed to location for now
        data.type = 0;

        var pos = editingMarker.getPosition();
        data.x = pos.lng();
        data.y = pos.lat();

        // Is there a valid district?
        var district = self.map.districts.getDistrictAt(pos);
        // No district at all
        if (district == undefined) {
            console.log("Oh noes, no district");
            return false;
        }
        if (district.isNew()) {
            console.log("Oh noes, no district id");
            return false;
        }

        // District seems fine
        data.district_id = district.get('id');

        // Based on wether this is an existing location or a new one
        // the url as well as the parameters will change
        var type = 'POST';
        var url  = 'location/add/';
        if (!editingMarker.isNew()) {
            // Not a new one: Editing
            type             = 'PUT';
            url              = 'location/edit/';
            data.location_id = editingMarker.get('id');
        }

        $.ajax({
            type:     type,
            url:      url,
            data:     data,
            dataType: 'json'
        }).done(function(data) {
            if (data.ok == "ok") {
                // Was ok, was saved, we are done
                self.isEditing = false;

                // Update entity infos for the marker
                editingMarker.set('id', data.entity.id);
                editingMarker.set('name', data.entity.name);
                addMarker(editingMarker);

                editingMarker    = null;
                originalPosition = null;
                $('#location_form').hide();

                // And enable all markers for drag&drop again
                setDraggable(true);
            } else {
                // Errors occured
                alert(data.error.message);
                // TODO: Some further handling would be nice
            }
        });

        return false;
    });

// Bind to reset handler
    $('#location_form').on('reset', function() {
        // And just call the cancel procedure
        self.cancel();
    });
}