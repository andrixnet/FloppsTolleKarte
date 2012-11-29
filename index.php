<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="description" content="Vollbildkarte mit Koordinatenbestimmung, Wegpunktprojektion und Abstandsberechnung">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flopps Tolle Karte: Wegpunktprojektion, Koordinaten, Abstand</title>
    <link rel="author" href="https://plus.google.com/100782631618812527586" />
    <link rel="icon" href="img/favicon.png" type="image/png" />
    <link rel="shortcut icon" href="img/favicon.png" type="image/png" />
    <link rel="image_src" href="img/screenshot.png" />
    
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyC_KjqwiB6tKCcrq2aa8B3z-c7wNN8CTA0&sensor=false"></script>
    <script type="text/javascript" src="js/cookies.js"></script>
    <script type="text/javascript" src="js/geographiclib.js"></script>
    <script type="text/javascript" src="js/coordinates.js"></script>
    <script type="text/javascript" src="js/map.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <script type="text/javascript" src="ext/bootstrap/js/bootstrap.min.js"></script>
    
    <link href="ext/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-27729857-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<style type="text/css">
    html, body { height: 100%; overflow: hidden}
    .map-wrapper { position: absolute; width: 100%; margin: 0; padding: 0; top: 40px; bottom: 0px; left: 0;}
    #themap { width: 100%; height: 100%;}
    #themap img { max-width: none; }
</style>

</head>
  
<?php
$lat1_valid = false;
$lon1_valid = false;
$lat2_valid = false;
$lon2_valid = false;
$clat_valid = false;
$clon_valid = false;
$zoom_valid = false;

$lat1 = 0;
$lon1 = 0;
$lat2 = 0;
$lon2 = 0;
$clat = 0;
$clon = 0;
$zoom = 0;
$maptype = 'OSM';

function my_parse_float( $s, &$f, &$ok, $min, $max )
{
    if( is_numeric( $s ) )
    {
        $f = floatval($s);
        $ok = ( $min <= $f && $f <= $max );
    }
    else
    {
        $ok = false;
    }
    
    if( !$ok ) $f = NULL;
}

function my_parse_int( $s, &$f, &$ok, $min, $max )
{
    $f = intval( $s );
    if (strval($f) == $s) 
    { 
        $ok = ( $min <= $f && $f <= $max );
    }
    else
    {
        $ok = false;
    }
    
    if( !$ok ) $f = NULL;
}


if(!empty($_GET)) 
{
    if (isset($_GET['lat1']))
    {
        my_parse_float( $_GET['lat1'], $lat1, $lat1_valid, -90, +90 );
    }
    if (isset($_GET['lon1']))
    {
        my_parse_float( $_GET['lon1'], $lon1, $lon1_valid, -180, +180 );
    }
    
    if (isset($_GET['lat2']))
    {
        my_parse_float( $_GET['lat2'], $lat2, $lat2_valid, -90, +90 );
    }
    if (isset($_GET['lon2']))
    {
        my_parse_float( $_GET['lon2'], $lon2, $lon2_valid, -180, +180 );
    }
    
    if (isset($_GET['clat']))
    {
        my_parse_float( $_GET['clat'], $clat, $clat_valid, -90, +90 );
    }
    if (isset($_GET['clon']))
    {
        my_parse_float( $_GET['clon'], $clon, $clon_valid, -180, +180 );
    }
    
    if (isset($_GET['zoom']))
    {
        my_parse_int( $_GET['zoom'], $zoom, $zoom_valid, 1, 18 );
    }
    
    if (isset($_GET['map']))
    {
        $maptype = $_GET['map'];
        if( $maptype != 'OSM' && $maptype != 'OSM/DE' && $maptype != 'roadmap' && $maptype != 'satellite' && $maptype != 'terrain' && $maptype != 'hybrid' )
        {
            $maptype = 'OSM';
        }
    }  
}

$ok = false;
if( $lat1_valid && $lon1_valid )
{
    $ok = true;
    if( $lat2_valid && $lon2_valid )
    {
        if( $clat_valid && $clon_valid )
        {
            // alles ok
        }
        else
        {
            $clat = ( $lat1 + $lat2 )/2;
            $clon = ( $lon1 + $lon2 )/2;
        }
    }
    else if( $clat_valid && $clon_valid )
    {
        $lat2 = $clat + ( $clat - $lat1 );
        $lon2 = $clon + ( $clon - $lon1 );
    }
    else
    {
        $clat = $lat1;
        $clon = $lon1;
        
        $lat2 = $lat1 + 0.01666666;
        $lon2 = $lon1 + 0.01666666;
    }
}
else if( $lat2_valid && $lon2_valid )
{
    $ok = true;
    if( $clat_valid && $clon_valid )
    {
        $lat1 = $clat + ( $clat - $lat2 );
        $lon1 = $clon + ( $clon - $lon2 );
    }
    else
    {
        $clat = $lat2;
        $clon = $lon2;
        
        $lat1 = $lat2 + 0.01666666;
        $lon1 = $lon2 + 0.01666666;
    }
}
else if( $clat_valid && $clon_valid )
{
    $ok = true;
    
    $lat1 = $clat;
    $lon1 = $clon;
    
    $lat2 = $lat1 + 0.01666666;
    $lon2 = $lon1 + 0.01666666;
}
else
{
    $ok = false;
}

if( !$zoom_valid ) $zoom = 12;

if( $ok )
{
    echo "<body onload=\"initialize( true, $lat1, $lon1, $lat2, $lon2, $clat, $clon, $zoom, '$maptype' )\" onunload=\"GUnload()\">";
}
else
{
    echo "<body onload=\"initialize( false, $lat1, $lon1, $lat2, $lon2, $clat, $clon, $zoom, '$maptype' )\" onunload=\"GUnload()\">";
}
?>


<!-- the menu -->
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <ul class="nav">
                <li><a role="button" class="brand" href="javascript:">Flopps Tolle Karte</a></li>
                <li><a role="button" href="#hilfeDialog" data-toggle="modal">Wie funktioniert die Karte?</a></li>
                <li><a role="button" href="#kontaktDialog" data-toggle="modal">Info/Impressum</a></li>
            </ul>
            <div class="pull-right">
                <ul class="nav">
                    <li><a role="button" href="javascript:" id='showPermalink'>Permalink</a></li>
                    <li><a role="button" href="javascript:" id='sidebar-toggle'>Verstecke Sidebar</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $("#sidebar-toggle").click(
        function() {
            if( $('#sidebar').is(':visible') )
            {
                $('#sidebar').hide();
                $('#sidebar-toggle').html( "Zeige Sidebar" );
            }
            else
            {
                $('#sidebar').show();
                $('#sidebar-toggle').html( "Verstecke Sidebar" );
            }
        });
        
        $("#showPermalink").click(
        function() {
            showPermalinkDialog();
        });
        
        $("#showCoordinatesADialog").click(
        function() {
            $('#coordinatesADialogEdit').val($("#inputCoordinatesA").val());
            $('#coordinatesADialog').modal();
        });
        $("#coordinatesADialogOk").click(
        function() {
            s = $('#coordinatesADialogEdit').val();
            c = string2coords( s );
            if( c != null )
            {
                $('#coordinatesADialog').modal('hide');
                setCoordinatesA( c );
            }
            else
            {
                alert( "Falsches Koordinatenformat:\n"+ s );
            }
        });
        
        $("#showCoordinatesBDialog").click(
        function() {
            $('#coordinatesBDialogEdit').val($("#inputCoordinatesB").val());
            $('#coordinatesBDialog').modal();
        });
        $("#coordinatesBDialogOk").click(
        function() {
            s = $('#coordinatesBDialogEdit').val();
            c = string2coords( s );
            if( c != null )
            {
                $('#coordinatesBDialog').modal('hide');
                setCoordinatesB( c );
            }
            else
            {
                alert( "Falsches Koordinatenformat:\n"+ s );
            }
        });
        
        $("#showNSG").click(
        function() {
            if( $('#showNSG').is(':checked') )
            {
                showNSGLayer( true );
            }
            else
            {
                showNSGLayer( false );
            }
        });
});
</script>


<!-- the map -->
<div class="map-wrapper">
    <div id="themap"></div>
</div>
  

<!-- the control widget -->
<div id="sidebar" style="position:absolute; bottom:32px; right:0px; margin: 4px; padding: 4px; border:1px solid #000; background:#fff; opacity: .9; z-index:2;">

<p>Suche</p>
<div class="input-append">
<input id="txtSearch" style="width: 210px" type="text" placeholder="Koordinaten oder Ort">
<button class="btn" style="width: 44px" type="button" onClick="searchLocation()"><i class="icon-search"></i></button>
</div>

<form>
<p>Marker <span class="label label-success">A</span></p>
<div class="input-append">
<input id="inputCoordinatesA" style="width: 166px" type="text" readonly>
<button id="showCoordinatesADialog" class="btn" style="width: 44px" type="button"><i class="icon-pencil"></i></button>
<button class="btn" style="width: 44px" type="button" onClick="centerX()"><i class="icon-screenshot"></i></button>
</div>  
</form>

<form>
<p>Marker <span class="label label-important">B</span></p>
<div class="input-append">
<input id="inputCoordinatesB" style="width: 166px" type="text" readonly>
<button id="showCoordinatesBDialog" class="btn" style="width: 44px" type="button"><i class="icon-pencil"></i></button>
<button class="btn" style="width: 44px" type="button" onClick="centerP()"><i class="icon-screenshot"></i></button>
</div>
</form>

<form>
<p>Entfernung und Winkel von <span class="label label-success">A</span> nach <span class="label label-important">B</span></p>

<div class="input-prepend input-append" style="width: 200px">
<span class="add-on" style="width: 48px">Distanz</span>
<input id="txtDistance" style="width: 160px" type="text" readonly>
<span class="add-on" style="width: 16px">m</span>
</div>

<div class="input-prepend input-append" style="width: 200px">
<span class="add-on" style="width: 48px">Winkel</span>
<input id="txtBearing" style="width: 160px" type="text" readonly>
<span class="add-on" style="width: 16px">°</span>
</div>
</form>

<form onsubmit="projectionXP()">
<p>Wegpunktprojektion</p>

<div class="input-prepend input-append" style="width: 200px">
<span class="add-on" style="width: 48px">Distanz</span>
<input id="txtProjectionDistance" style="width: 160px" type="text" placeholder="Distanz (m)">
<span class="add-on" style="width: 16px">m</span>
</div>

<div class="input-prepend input-append">
<span class="add-on" style="width: 48px">Winkel</span>
<input id="txtProjectionBearing" style="width: 160px" type="text" placeholder="Winkel (°)">
<span class="add-on" style="width: 16px">°</span>
</div>

<input class="btn" type="button" value="Los geht's!" onClick="projectionXP()" />
</form>
<form>
<p>Naturschutzgebiete</p>
<label class="checkbox">
    <input id="showNSG" type="checkbox"> Zeige Naturschutzgebiete
</label>
</form>
</div>


<!-- the hilfe dialog -->
<div class="modal hide fade" id="hilfeDialog" tabindex="-1" role="dialog" aria-labelledby="hilfeDialogLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="hilfeDialogLabel">Wie funktioniert die Karte?</h3>
  </div>
  <div class="modal-body">
      <div class="page-header">  
      <h4>Die Karte</h4>
      </div>
      <p>Die Karte basiert auf Google Maps und enthält daher auch die von Google Maps gewohnten Steuerelemente zur Navigation. Über die Knöpfe "OSM", "OSM/DE", "Karte", "Satellit" in der oberen, rechten Ecke der Karte kann zwischen verschiedenen Kartendarstellungen umgeschaltet werden.
      <img src="img/screenshot.png" alt="Screenshot von 'Flopps Tolle Karte'" width="400px" height="426px" class="img-polaroid">
      </p>
      <div class="page-header">  
      <h4>Die Sidebar</h4>
      </div>
      <p>Auf der rechten Seite des Kartenfensters befindet sich die Sidebar, die Informationen über die Marker, sowie Elemente zur Kontrolle und Manipulation der Marker enthält.</p>
      <p>Die Sidebar kann über den Knopf <a class="btn btn-small" href="javascript:">Verstecke Sidebar</a> versteckt und über den Knopf <a class="btn btn-small" href="javascript:">Zeige Sidebar</a> wieder eingeblendet werden.</p>
      
      <div class="page-header">  
      <h4>Die Marker</h4>
      </div>
      <p>Auf der Karte gibt es zwei Marker: <img src="img/green.png" alt="A"> 
      und <img src="img/red.png" alt="B"></p>
      <p>
      <img alt="Die Marker auf der Karte" src="img/screenshot-markers.png" class="img-polaroid">
      </p>
      <p>Die Marker können mit der Maus an neue Orte auf der Karte gezogen werden.<img src="img/screenshot-move.gif" alt="Ziehen der Marker" class="img-polaroid"></p>
      
      <p>Die aktuellen Koordinaten der beiden Marker werden in den Feldern 
      "Marker <span class="label label-success">A</span>" bzw. "Marker <span class="label label-important">B</span>" angezeigt.</p>
      <p>Der Knopf <span class="btn btn-small"><i class="icon-pencil"></i></span> öffnet ein Fenster, in dem die Koordinaten des Markers direkt geändert werden können.</p>
      <p>Durch den Knopf <span class="btn btn-small"><i class="icon-screenshot"></i></span> 
      kann der jeweilige Marker im Zentrum der Karte platziert werden.</p>
      <p>Bei "Entfernung und Winkel von <span class="label label-success">A</span> nach <span class="label label-important">B</span>" werden sowohl der aktuelle 
      Abstand von Marker <span class="label label-success">A</span> zu Marker <span class="label label-important">B</span> in Metern, als auch der Richtungswinkel 
      von <span class="label label-success">A</span> zu <span class="label label-important">B</span> angezeigt.</p>
      
      
      <div class="page-header">  
      <h4>Die Suche</h4>
      </div>
      <p>Über das Feld "Suche" kann man nach Koordinaten und Orten suchen. 
      Gibt man z.B. im Suchfeld "Berlin" ein und drückt auf <span class="btn btn-small"><i class="icon-search"></i></span>, 
      so wird die Karte auf Berlin zentriert. Bei Eingabe von 
      "N 47 59.734 E 007 51.172" landet man auf dem Freiburger Münster.</p>
      
      <div class="page-header">  
      <h4>Die Wegpunktprojektion</h4>
      </div>
      <p>
      <img src="img/projection.png"  style="float: right" alt="Wegpunktprojektion" width="100px" height="123px" class="img-polaroid">
      Beim Klick auf <span class="btn btn-small">Los geht's!</span> wird 
      Marker <span class="label label-important">B</span> vom Marker <span class="label label-success">A</span> aus in Richtung "Winkel" um die Entfernung "Distanz" 
      verschoben; Marker <span class="label label-success">A</span> bleibt an seiner Position. Nach erfolgter Projektion
      sollten die Werte in "Entfernung und Winkel von <span class="label label-success">A</span> nach B" mit denen in 
      "Wegpunktprojektion" übereinstimmen.
      </p>
      
      <div class="page-header">  
      <h4>Naturschutzgebiete</h4>
      </div>
      <p>
      Durch ankreuzen des Feldes "Zeige Naturschutzgebiete", wird die Anzeige von deutschen Naturschutzgebieten in der Karte als farbige Flächen aktiviert. Die Informationen über die Naturschutzgebiete werden vom <a href="http://www.nsg-atlas/" target="_blank">NSG-Atlas</a> bezogen.
      <img src="img/screenshot-nsg.png" alt="Naturschutzgebiete" class="img-polaroid">
      </p>
      
      <div class="page-header">  
      <h4>Permalinks</h4>
      </div>
      <p>
          Der Eintrag "Permalink" in der Naviagtionsleiste öffnet einen Dialog in 
          dem ein Permalink auf die aktuelle Kartenansicht angezeigt wird (inklusive 
          der Positionen der Marker, der Zoomstufe, des gewählten Kartentyps). 
          Diesen Link kann man kopieren und z.B. an Freunde schicken um die 
          aktuelle Kartenansicht mit ihnen zu teilen.
      </p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Ok</button>
  </div>
</div>

<!-- the kontakt dialog -->
<div class="modal hide fade" id="kontaktDialog" tabindex="-1" role="dialog" aria-labelledby="kontaktDialogLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="kontaktDialogLabel">Kontakt/Info</h3>
  </div>
  <div class="modal-body">
    <img class="img-polaroid" width="100ps" height="100px" style="float: right" src="avatar.jpg" alt="Flopp">
    <h4>Kontakt</h4>
    <p>Fragen und Anregungen nehme ich gerne per <a href="mailto:mail@flopp-caching.de" target="_blank">Mail</a> oder <a href="https://twitter.com/floppgc" target="_blank">Twitter</a> entgegen. Außerdem gibt es Seiten bei <a href="https://plus.google.com/u/0/116067328889875491676" target="_blank">Google+</a> und bei <a href="https://www.facebook.com/FloppsTolleKarte" target="_blank">Facebook</a>.</p>
    <p>Bugs können auch via <a href="https://github.com/flopp/FloppsTolleKarte" target="_blank">github</a> gemeldet werden.</p>
    <p>Flopps Tolle Karte ist unter den URLs <a href="http://flopp-caching.de/">flopp-caching.de</a> und <a href="http://foomap.de/">foomap.de</a> erreichbar.</p>
    <p>
        <a href="mailto:mail@flopp-caching.de" target="_blank"><img src="img/email.png"></a>
        <a href="https://twitter.com/floppgc" target="_blank"><img src="img/twitter.png"></a>
        <a href="https://plus.google.com/u/0/116067328889875491676" target="_blank"><img src="img/googleplus.png"></a>
        <a href="https://www.facebook.com/FloppsTolleKarte" target="_blank"><img src="img/facebook.png"></a>
    </p>
    
    <h4>Impressum</h4>
    <p>Dies ist ein rein privates, werbe- und kostenfreies Informationsangebot
zum Hobby Geocaching. Als nicht geschäftsmäßiges Angebot unterliegt diese
Seite gemäß Telemediengesetz nicht der Impressumspflicht.</p>

    <h4>Hinter den Kulissen...</h4>
    <p>
        <b>Flopps Tolle Karte</b> benutzt <a href="https://developers.google.com/maps/" target="_blank">Google Maps</a> 
        und <a href="http://openstreetmap.org/" target="_blank">OpenStreetMap</a> als Kartelieferanten, 
        <a href="http://twitter.github.com/bootstrap/index.html" target="_blank">Bootstrap</a> 
        und <a href="http://jquery.com/" target="_blank">jQuery</a> für die Elemente des Userinterfaces,
        die Icons von <a href="http://glyphicons.com/" target="_blank">Glyphicons</a> und <a href="http://www.awicons.com/" target="_blank">Lokas Software</a>,
        sowie die Javascript-Version von <a href="http://geographiclib.sf.net/html/other.html#javascript" target="_blank">GeographicLib</a>
        für die Distanz-/Winkelberechnungen und die Wegpunktprojektion.
        Informationen über deutsche Naturschutzgebiete werden vom <a href="http://www.nsg-atlas/" target="_blank">NSG-Atlas</a> freundlicherweise zur Verfügung gestellt. Danke dafür!
        
        Der aktuelle Quellcode von <b>Flopps Toller Karte</b> ist auf <a href="https://github.com/flopp/FloppsTolleKarte" target="_blank">github</a> zu finden.
    </p>

<h4>Datenschutz</h4>
<p>
Diese Seite erhebt keine personenbezogenen Daten.
</p>
<h4>Cookies</h4>
<p>
Diese Seite verwendet Cookies, um die aktuelle Ansicht der Karte (Zentrum, Position der Marker, gewählter Kartentyp) abzuspeichern und beim erneuten Besuch der Seite wieder zu laden. Selbstverständlich ist das Löschen dieser Cookies bzw. das Unterbinden dieser Cookies in den Browsereinstellungen möglich.
</p>
<h4>Google Analytics</h4>
<p>
Diese Website benutzt Google Analytics, einen Webanalysedienst der Google Inc. ("Google"). Google Analytics verwendet sog. "Cookies", Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website durch Sie ermöglichen. Die durch den Cookie erzeugten Informationen über Ihre Benutzung dieser Website werden in der Regel an einen Server von Google in den USA übertragen und dort gespeichert. Im Falle der Aktivierung der IP-Anonymisierung auf dieser Webseite wird Ihre IP-Adresse von Google jedoch innerhalb von Mitgliedstaaten der Europäischen Union oder in anderen Vertragsstaaten des Abkommens über den Europäischen Wirtschaftsraum zuvor gekürzt.

Nur in Ausnahmefällen wird die volle IP-Adresse an einen Server von Google in den USA übertragen und dort gekürzt. Im Auftrag des Betreibers dieser Website wird Google diese Informationen benutzen, um Ihre Nutzung der Website auszuwerten, um Reports über die Websiteaktivitäten zusammenzustellen und um weitere mit der Websitenutzung und der Internetnutzung verbundene Dienstleistungen gegenüber dem Websitebetreiber zu erbringen. Die im Rahmen von Google Analytics von Ihrem Browser übermittelte IP-Adresse wird nicht mit anderen Daten von Google zusammengeführt.

Sie können die Speicherung der Cookies durch eine entsprechende Einstellung Ihrer Browser-Software verhindern; wir weisen Sie jedoch darauf hin, dass Sie in diesem Fall gegebenenfalls nicht sämtliche Funktionen dieser Website vollumfänglich werden nutzen können. Sie können darüber hinaus die Erfassung der durch das Cookie erzeugten und auf Ihre Nutzung der Website bezogenen Daten (inkl. Ihrer IP-Adresse) an Google sowie die Verarbeitung dieser Daten durch Google verhindern, indem sie das unter dem folgenden Link verfügbare Browser-Plugin herunterladen und installieren: <a href="http://tools.google.com/dlpage/gaoptout?hl=de">http://tools.google.com/dlpage/gaoptout?hl=de</a>.
</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Ok</button>
  </div>
</div>

<div id="coordinatesADialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="coordinatesADialogLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="coordinatesADialogLabel">Koordinaten für <img src="img/green.png"></h3>
  </div>
  <div class="modal-body">
    <input id="coordinatesADialogEdit" style="width: 210px" type="text">
    <p>Zulässiges Koordinatenformat: "N 47 58.123 E 007 54.567"</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Abbrechen</button>
    <button class="btn btn-primary" id="coordinatesADialogOk">Ok</button>
  </div>
</div>

<div id="coordinatesBDialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="coordinatesBDialogLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="coordinatesBDialogLabel">Koordinaten für <img src="img/red.png"></h3>
  </div>
  <div class="modal-body">
    <input id="coordinatesBDialogEdit" style="width: 210px" type="text">
    <p>Zulässiges Koordinatenformat: "N 47 58.123 E 007 54.567"</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Abbrechen</button>
    <button class="btn btn-primary" id="coordinatesBDialogOk">Ok</button>
  </div>
</div>

<div id="permalinkDialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="permalinkDialogLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="permalinkDialogLabel">Permalink für die aktuelle Kartenansicht</h3>
  </div>
  <div class="modal-body">
    <input id="permalinkDialogEdit" style="width: 500px" type="text">
    <p>
    Der obige Permalink ist Link zur aktuellen Kartenansicht, inklusive beider Marker, der Zoomstufe und des gewählten Kartentyps. So kann eine spezielle Kartenansicht mit anderen geteilt werden.
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Ok</button>
  </div>
</div>

<!-- the welcome dialog -->
<div id="welcomeDialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="welcomeDialogLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="welcomeDialogLabel">Willkommen!</h3>
  </div>
  <div class="modal-body">
<p>
    <h4>Willkommen bei <i>Flopps Toller Karte</i>!</h4>
</p>
<p>
    Wie man diese Online-Karte benutzt und welche Möglichkeiten man hat, erfährst du mit einem Klick auf <i>Wie funktioniert diese Karte?</i> in der Titelleiste. Viel Spaß!
</p>
<p id="news">
    <h4>Neuigkeiten</h4>
    <ul>
        <li><b>2012/11/29</b> Kooperation mit dem <a href="http://www.nsg-atlas.de/" target="_blank">NSG-Atlas</a>: Naturschutzgebiete können eingeblendet werden.</li>        
        <li><b>2012/11/20</b> Es können Permalinks für die aktuelle Kartenansicht erzeugt werden.</li>
        <li><b>2012/11/16</b> Die Karte ist jetzt auch unter <a href="http://foomap.de/">foomap.de</a> erreichbar.</li>
        <li><b>2012/11/09</b> Anzeige des Kartenmaßstabs hinzugefügt.</li>
        <li><b>2012/11/05</b> Rundungsfehler bei der internen Berechnung von Koordinaten behoben.</li>
        <li><b>2012/11/05</b> Die Koordinaten der Marker können jetzt über den Button <span class="btn btn-small"><i class="icon-pencil"></i></span> direkt geändert werden.</li>
    </ul>
</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Ok</button>
  </div>
</div>


  </body>

</html>
