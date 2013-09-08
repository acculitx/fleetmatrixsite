<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$id = JRequest::getInt('trip');
?>

<script>
    var geoXml = null;
    var map = null;
    var myLatLng = null;
    jQuery(document).ready(function(){
        var myOptions = {
            zoom: 8,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

        geoXml = new geoXML3.parser({
            map: map,
            singleInfoWindow: true,
            afterParse: useTheData
        });
        geoXml.parse('/cache/map<?php echo $id; ?>.kml');
    });

    function useTheData(doc){
        /*for (var i = 0; i < doc[0].markers.length; i++) {
            jQuery('#map_text').append(doc[0].markers[i].title + ', ');
        }*/
    };
</script>

