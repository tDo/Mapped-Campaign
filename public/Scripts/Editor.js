

function Editor(map) {
    var self = this;
    this.map = map;

    var polyOptions = {
        strokeWeight: 0,
        fillOpacity: 0.45,
        editable: true
    };

    // Create the drawing manager for shape drawing options
    this.drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
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

    this.currentPolygon = null;
    this.polygonToJson = function() {
        return (self.currentPolygon) ? self.currentPolygon.toJson() : "";
    }

    // Bind event which is fired when a polygon has been compledted
    google.maps.event.addListener(this.drawingManager, 'polygoncomplete', function(polygon) {
        // Start editing the district
        self.map.districts.edit(polygon);
    });

    /*
    google.maps.event.addListener(this.drawingManager, 'overlaycomplete', function(e) {
        if (e.type != google.maps.drawing.OverlayType.MARKER) {
            var shape  = e.overlay;
            shape.type = e.type;

            google.maps.event.addListener(shape, 'mouseover', function() {
                shape.set('fillColor', '#ff0000');
                console.log(shape.getPath());
            });
        }

    });*/
}