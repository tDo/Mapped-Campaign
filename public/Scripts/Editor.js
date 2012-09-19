
var EditorModes = { None:                0,
                    AddDistrict:         1,
                    EditDistrict:        2,
                    AddLocation:         3,
                    EditLocation:        4,
                    AddPointOfInterest:  5,
                    EditPointOfInterest: 6,
                    AddBuilding:         7,
                    EditBuilding:        8
                  };

function Editor(map) {
    var self = this;
    this.map = map;

    // Holds options for the polygon creation
    var polyOptions = {
        strokeWeight: 0,
        fillOpacity:  0.45,
        editable:     true
    };

    // Create the drawing manager for shape drawing options
    var drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode:    null,//google.maps.drawing.OverlayType.POLYGON,
        drawingControl: false,
        markerOptions: {
            draggable: true
        },
        polylineOptions: {
            editable: true
        },
        rectangleOptions: polyOptions,
        circleOptions:    polyOptions,
        polygonOptions:   polyOptions,
        map:              map.map
    });

    // Function will check for all bound handlers if any of them is currently in editing mode
    this.isEditing = function() {
        return self.map.districts.isEditing;
    };

    // Holds a reference to all editormode controls (Which can be used to change the mode)
    var controls = $("*[data-editormode]");

    // Holds the currently used creation mode
    var mode     = EditorModes.None;
    // Get the currently used editing mode
    this.getMode = function() { return mode; };

    // Set the currently used creation mode
    this.setMode = function(newMode) {
        // May not change the mode while editing
        if (self.isEditing()) return false;

        var drawingMode = null;
        // Check what new mode is required
        switch (newMode) {
            // Reset mode to not editing
            case EditorModes.None:
                drawingMode = null;
                break;
            // District handling
            case EditorModes.AddDistrict:
                drawingMode = google.maps.drawing.OverlayType.POLYGON;
                break;
            case EditorModes.EditDistrict:
                drawingMode = null;
                break;
            // Locations, POIs, Buildings
            case EditorModes.AddLocation:
            case EditorModes.AddPointOfInterest:
            case EditorModes.AddBuilding:
                drawingMode = google.maps.drawing.OverlayType.MARKERS;
                break;
            case EditorModes.EditLocation:
            case EditorModes.EditPointOfInterest:
            case EditorModes.EditBuilding:
                drawingMode = null;
                break;
            // Anything else is not allowed and we stop here
            default:
                return false;
                break;
        }

        // Assign the new mode
        mode = newMode;
        // And also to the drawing manager
        drawingManager.set('drawingMode', drawingMode);

        // And update the active status of controls
        controls.each(function(idx, element) {
            $(element).removeClass('active');
            var key = $(element).attr("data-editormode");
            if (EditorModes[key] == mode)
                $(element).addClass('active');
        });
    };

    // Bind the editor mode controls
    controls.each(function(idx, element) {
        var key = $(element).attr("data-editormode");
        if (EditorModes[key] !== undefined) {
            $(element).on('click', function() {
                self.setMode(EditorModes[key]);
            });
        }
    });

    // Finally set to default (None) mode for the beginning
    self.setMode(EditorModes.None);

    // Bind event which is fired when a polygon has been compledted
    google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
        // Start editing the district
        self.setMode(EditorModes.EditDistrict);
        self.map.districts.edit(polygon);
    });
}