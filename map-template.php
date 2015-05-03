<?php
require_once('lib/lang.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title><?php TT('Flopp\'s Map', 'Flopps Tolle Karte');?></title>
    <meta name="description" content="<?php TT('Fullscreen map with coordinates, waypoint projection, distance/bearing calculation, display of geocaches', 'Vollbild-Karte mit Koordinaten, Wegpunktprojektion, Berechnung von Entfernungen und Winkeln, Anzeige von Geocaches');?>"</meta>
    
    <meta name="viewport" content="height = device-height,
    width = device-width,
    initial-scale = 1.0,
    minimum-scale = 1.0,
    maximum-scale = 1.0,
    user-scalable = no,
    target-densitydpi = device-dpi" />
  
    <link rel="author" href="https://plus.google.com/100782631618812527586" />
    <link rel="icon" href="img/favicon.png" type="image/png" />
    <link rel="shortcut icon" href="img/favicon.png" type="image/png" />
    <link rel="image_src" href="img/screenshot.png" />
    
    <!-- google maps -->
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyC_KjqwiB6tKCcrq2aa8B3z-c7wNN8CTA0&amp;sensor=true&amp;language=<?php TT('en', 'de');?>"></script>
    <script src="https://apis.google.com/js/client.js"></script>

    <!-- jquery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    
    <!-- jquery.cookie -->
    <script src="ext/jquery-cookie/jquery.cookie.js"></script>

    <!-- i18next -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/i18next/1.6.3/i18next-1.6.3.min.js"></script>
    
    <!-- bootstrap + font-awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
 
    <!-- fonts --> 
    <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Norican">
    
    <!-- my own stuff -->
    <script type="text/javascript" src="js/compressed.js?t=TSTAMP"></script>        
    <link type="text/css" rel="stylesheet" href="css/main.css?t=TSTAMP">
    
<!-- Piwik -->
<script type="text/javascript"> 
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://flopp-caching.de/piwik//";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 1]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
    g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Piwik Code -->

<script>
<?php
$cntr = "";
$zoom = "";
$maptype = "";
$markers = "";
$lines = "";

if(!empty($_GET)) 
{
  if(isset($_GET['c'])) { $cntr = $_GET['c']; }
  if(isset($_GET['z'])) { $zoom = $_GET['z']; }
  if(isset($_GET['t'])) { $maptype = $_GET['t']; }    
  if(isset($_GET['m'])) { $markers = $_GET['m']; }
  if(isset($_GET['d'])) { $lines = $_GET['d']; }
}

echo "\$(function() {";
echo "initialize('$lang', '$cntr', '$zoom', '$maptype', '$markers', '$lines');";
echo "})";
?>

$(document).ready( function() {
    var option = {resGetPath: 'lang/__lng__/__ns__.json', fallbackLng: 'en', debug: true};
 
    $.i18n.init(option, function(t) {
        $(document).i18n();
    });
});
</script>
</head>

<body>

<!-- the menu -->
<div class="navbar navbar-custom navbar-static-top">
  <div class="navbar-inner">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <img src="img/favicon.png" style="position: absolute; top: 9px; left:4px;">
      <a class="navbar-brand" href="#" style="margin-left:32px;"><div style="width: 32px"></div><span data-i18n="nav.title">FLOPP'S MAP</span></a>
    </div>
        
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li><a id="navbarBlog" role="button" href="http://blog.flopp-caching.de/" target="_blank"><span data-i18n="nav.blog">BLOG</span> <i class="fa fa-star"></i></a></li>
        <li><a id="navbarHelp" role="button" href="http://blog.flopp-caching.de/benutzung-der-karte/" target="_blank"><span data-i18n="nav.help">HELP</span> <i class="fa fa-question"></i></a></li>
        <li><a id="navbarInfo" role="button" href="javascript:showInfoDialog()"><span data-i18n="nav.impress">IMPRESS</span> <i class="fa fa-info"></i></a></li>
        <li></li>
      </ul>
      <form class="nav navbar-form navbar-right" style="margin:auto">
         <span class="btn btn-default btn-sm navbar-btn" onclick="langEN();" data-i18n="[html]nav.english">ENGLISH</span>
         <span class="btn btn-default btn-sm navbar-btn" onclick="langDE();" data-i18n="[html]nav.german">DEUTSCH</span>
      </form>
    </div>
  </div>
</div>

<!-- the map -->
<div id="map-wrapper">
  <div id="themap"></div>
</div>
  

<a id="sidebartoggle" href="javascript:">
  <span id="sidebartogglebutton"><i class="fa fa-chevron-right"></i></span>
</a>

<div id="sidebar">

<div class="my-section">
  <div class="my-section-header" data-i18n="sidebar.search.title">SEARCH</div>
  <button id="buttonWhereAmI" class="btn btn-info btn-sm my-section-buttons-top" type="button"><i class="fa fa-crosshairs"></i> <span data-i18n="sidebar.search.whereami">WHERE AM I?</span></button>
    
  <div>
    <form action="javascript:theGeolocation.search($('#txtSearch').val())">
      <div class="input-group" style="margin-bottom: 5px">
        <input class="form-control" id="txtSearch" type="text" data-i18n="[placeholder]sidebar.search.placeholder;">
        <span class="input-group-btn">
          <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
        </span>
      </div>
    </form>
  </div>
</div> <!-- section -->

<div class="my-section-with-footer my-section">
  <div class="my-section-header" data-i18n="sidebar.markers.title">MARKERS</div>
  <div id="btnmarkers1" class="btn-group btn-group-sm my-section-buttons-top">
    <button id="buttonMarkersNew1" class="btn btn-sm btn-success" type="button" onClick="newMarker(map.getCenter(), -1, -1, null)"><i class="fa fa-map-marker"></i> <span data-i18n="sidebar.markers.new">NEW</span></button>
    <button id="buttonMarkersDeleteAll1" class="btn btn-sm btn-danger" type="button" onClick="theMarkers.deleteAll()"><i class="fa fa-trash-o"></i> <span data-i18n="sidebar.markers.deleteall">DELETE ALL</span></button>
  </div>
  <div id="dynMarkerDiv"></div>
  <div id="btnmarkers2" class="btn-group btn-group-sm my-section-buttons-bottom" style="display: none">
    <button id="buttonMarkersNew2" class="btn btn-sm btn-success" type="button" onClick="newMarker( map.getCenter(), -1, -1, null )"><i class="fa fa-map-marker"></i> <span data-i18n="sidebar.markers.new">NEW</span></button>
    <button id="buttonMarkersDeleteAll2" class="btn btn-sm btn-danger" type="button" onClick="theMarkers.deleteAll()"><i class="fa fa-trash-o"></i> <span data-i18n="sidebar.markers.deleteall">DELETE ALL</span></button>
  </div>
</div> <!-- section -->
  
<div class="my-section">
  <div class="my-section-header" data-i18n="sidebar.lines.title">LINES</div>
  <div class="btn-group btn-group-sm my-section-buttons-top">
    <button id="buttonLinesNew" class="btn btn-sm btn-success" type="button" onClick="theLines.newLine(-1, -1)"><i class="fa fa-minus"></i> <span data-i18n="sidebar.lines.new">NEW</span></button>
    <button id="buttonLinesDeleteAll" class="btn btn-sm btn-danger" type="button" onClick="theLines.deleteAllLines()"><i class="fa fa-trash-o"></i> <span data-i18n="sidebar.lines.deleteall">DELETE ALL</span></button>
  </div>
  <div id="dynLineDiv"></div>
</div> <!-- section -->

<div class="my-section">
  <div class="my-section-header" data-i18n="sidebar.misc.title">MISC</div>
  <div style="margin-bottom: 10px">
    <a id="buttonExportGPX" class="btn btn-block btn-sm btn-info" role="button" href="download.php" data-i18n="sidebar.misc.gpx">EXPORT GPX</a>
  </div>
  <div style="margin-bottom: 10px">
    <button id="buttonPermalink" class="btn btn-block btn-sm btn-info" type="button" onClick="generatePermalink()" data-i18n="sidebar.misc.permalink">CREATE PERMALINK</button>
  </div>

  <b data-i18n="sidebar.misc.coordinates">FORMAT OF COORINATES</b>
  <div>
    <select class="form-control" id="coordinatesFormat">
      <option value="DM">DDD MM.MMM</option>
      <option value="DMS">DDD MM SS.SS</option>
      <option value="D">DDD.DDDDD</option>
    </select>
  </div>

  <b data-i18n="sidebar.misc.layers">ADDITIONAL LAYERS</b>
  <div style="margin-bottom: 10px">
    <label class="checkbox">
      <input id="hillshading" type="checkbox"> <span data-i18n="sidebar.misc.hillshading">HILL SHADING</span>
      <button class="btn btn-info btn-xs" onClick="showHillshadingDialog()"><i class="fa fa-info"></i></button>
    </label>
    <label class="checkbox">
      <input id="boundaries" type="checkbox"> <span data-i18n="sidebar.misc.boundaries">ADMINISTRATIVE BOUNDARIES</span>
      <button class="btn btn-info btn-xs" onClick="showBoundariesDialog()"><i class="fa fa-info"></i></button>
    </label>
    <label class="checkbox">
      <input id="naturschutzgebiete" type="checkbox"> <span data-i18n="sidebar.misc.npa">NATURE PROTECTION AREAS</span>
      <button class="btn btn-info btn-xs" onClick="showNaturschutzgebieteDialog()"><i class="fa fa-info"></i></button>
    </label>
    <div id="nsg_details" style="display: none;">
        <button class="btn btn-block btn-sm btn-info" style="margin-bottom: 10px;" onClick="startNsgInfoMode()" data-i18n="sidebar.misc.npainfo">SHOW NPA INFO ON NEXT CLICK</button>
    </div>
    <label class="checkbox">
      <input id="showCaches" type="checkbox"> <?php TT('Geocaches (<a href="http://www.opencaching.eu/" target="_blank">Opencaching</a>)', 'Geocaches (<a href="http://www.opencaching.eu/" target="_blank">Opencaching</a>)');?>
    </label>
  </div>
  
  <b><?php TT('External Services', 'Externe Dienste');?></b>
  <div>
    <div class="input-group">
      <select class="form-control" id="externallinks" title="<?php TT('Open external service', 'Öffne externen Dienst');?>"></select>
      <span class="input-group-btn">
        <button class="btn btn-info" type="button" onClick="gotoExternalLink()" title="<?php TT('Open external service', 'Öffne externen Dienst');?>"><i class="fa fa-play"></i></button>
      </span>
    </div>
  </div>
</div> <!-- section -->

<div style="text-align: center; color: white;">&copy; 2012-2015, <a href="http://www.florian-pigorsch.de/" target="_blank">Florian Pigorsch</a></div>

</div> <!-- sidebar -->


<!-- the info dialog -->
<div id="dlgInfoAjax" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3><?php TT('Info/Impress', 'Info/Impressum');?></h3>
      </div>
      <div class="modal-body">
<?php 
require('lang/info.' . $lang . '.html')
?>    
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

<!-- the alert dialog -->
<div id="dlgAlert" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="dlgAlertHeader">Modal header</h3>
      </div>
      <div id="dlgAlertMessage" class="modal-body">Modal body</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<div id="projectionDialog" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3><?php TT('Waypoint Projection', 'Wegpunktprokjektion');?></h3>
      </div>
      <div class="modal-body">
   
<img src="img/projection.png" style="float: right">
<div  style="margin-right: 150px">
<p><?php TT('Waypoint projection creates a new marker <b>d</b> meters away from the source marker with a bearing angle of <b>&beta;°</b>,', 'Wegpunktprojektion erzeugt einen neuen Marker, der <b>d</b> Meter vom Ursprungsmarker entfernt ist und unter einem Winkel von <b>&beta;°</b> erscheint.');?></p>
<form role="form">
  <div class="form-group">
    <label for="projectionBearing" class="control-label"><?php TT('Bearing &beta;', 'Winkel &beta;');?></label>
    <input type="text" class="form-control" id="projectionBearing" placeholder="<?php TT('Bearing angle in °; 0-360', 'Winkel &beta; in °; 0-360');?>">
  </div>
  <div class="form-group">
    <label for="projectionDistance" class="control-label"><?php TT('Distance d', 'Entfernung d');?></label>
    <input type="text" class="form-control" id="projectionDistance" placeholder="<?php TT('Projection distance in meters', 'Projektionsdistanz in Metern');?>">
  </div>
</form>
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" data-dismiss="modal"><?php TT('Cancel', 'Abbruch');?></button>
      <button id="projectionDialogOk" type="button" class="btn btn-primary">OK</button>
      </div>
    </div>
  </div>
</div>

<div id="linkDialog" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3><?php TT('Permalink', 'Permalink');?></h3>
      </div>
      <div class="modal-body">
        <div>
          <?php TT('The following URL links to the current view of Flopp\'s Map including all markers, lines and the selected map type. Copy (<tt>CTRL+C</tt>) the URL and share it with your friends!', 'Die folgende URL beschreibt die aktuelle Kartenansicht inklusive aller Marker, Linien und des ausgewählten Kartentyps. Kopiere (<tt>STRG+C</tt>) die URL und teile sie mit deinen Freunden!');?>
          <br />
          <?php TT('<b>Shorten</b> runs the long URL through an URL shortener (<a href="http://goo.gl/" target="_blank">goo.gl</a>) an produces a shortened URL.', '<b>Kürzen</b> schickt die lange URL durch einen URL-Shortener (<a href="http://goo.gl/" target="_blank">goo.gl</a>) und erzeugt so einen kürzere URL.');?>
        </div>
        <div class="input-group">
          <input class="form-control" id="linkDialogLink" type="text" title="<?php TT('Permalink to the current map view', 'Permalink für die aktuelle Kartenansicht');?>">
          <span class="input-group-btn">
            <button id="buttonPermalinkShorten" class="btn btn-info" type="button" title="<?php TT('Shorten the permalink', 'Verkürze den Permalink');?>" onclick="linkDialogShortenLink()"><?php TT('Shorten', 'Kürzen');?></button>
          </span>
        </div>
        <div id="linkDialogError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<div id="dialogHillshading" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3><?php TT('Hillshading', 'Hillshading');?></h3>
      </div>
      <div class="modal-body">
        <div>
          <?php TT('The hillshading map tiles are generously provided by the <a href="openmapsurfer.uni-hd.de" target="_blank">OpenMapSurfer project</a> of Heidelberg University unter the following copyright terms:<br>Map data &copy; <a href="http://srtm.csi.cgiar.org/" target="_blank">SRTM</a>; ASTER GDEM is a product <a href="http://www.meti.go.jp/english/press/data/20090626_03.html" target="_blank">METI</a> and <a href="https://lpdaac.usgs.gov/products/aster_policies" target="_blank">NASA</a>, Imagery <a href="http://giscience.uni-hd.de/" target="_blank">GIScience Research Group @ Heidelberg University</a>', 'Die Hillshading-Kartenkacheln werden uns freundlicherweise vom <a href="openmapsurfer.uni-hd.de" target="_blank">OpenMapSurfer-Projekt</a> der Univerität Heidelberg unter folgenden Copyright-Bedingungen zur Verfügung gestellt:<br>Map data &copy; <a href="http://srtm.csi.cgiar.org/" target="_blank">SRTM</a>; ASTER GDEM is a product <a href="http://www.meti.go.jp/english/press/data/20090626_03.html" target="_blank">METI</a> and <a href="https://lpdaac.usgs.gov/products/aster_policies" target="_blank">NASA</a>, Imagery <a href="http://giscience.uni-hd.de/" target="_blank">GIScience Research Group @ Heidelberg University</a>');?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<div id="dialogBoundaries" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3><?php TT('Administrative Boundaries', 'Verwaltungsgrenzen');?></h3>
      </div>
      <div class="modal-body">
        <div>
          <?php TT('The \'administrative boundaries\' map tiles are generously provided by the <a href="openmapsurfer.uni-hd.de" target="_blank">OpenMapSurfer project</a> of Heidelberg University unter the following copyright terms:<br>Map data &copy; <a href="http://osm.org/" target="_blank">Openstreetmap</a> contributors; Imagery <a href="http://giscience.uni-hd.de/" target="_blank">GIScience Research Group @ Heidelberg University</a>', 'Die Verwaltungsgrenzen-Kartenkacheln werden uns freundlicherweise vom <a href="openmapsurfer.uni-hd.de" target="_blank">OpenMapSurfer-Projekt</a> der Univerität Heidelberg unter folgenden Copyright-Bedingungen zur Verfügung gestellt:<br>Map data &copy; <a href="http://osm.org/" target="_blank">Openstreetmap</a> contributors; Imagery <a href="http://giscience.uni-hd.de/" target="_blank">GIScience Research Group @ Heidelberg University</a>');?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<div id="dialogNaturschutzgebiete" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3><?php TT('Nature Protection Areas', 'Naturschutzgebiete');?></h3>
      </div>
      <div class="modal-body">
        <div>
            <?php TT('German nature protection areas are shown as a green map overlay:<br /><img src="img/nsg.png" class="center-block img-thumbnail"><br /><br />The data of german nature protection areas are provided by the <a href="http://www.bfn.de/" target="_blank">Bundesamt für Naturschutz (Federal Agency for Nature Conservation)</a> under the <a href="http://www.gesetze-im-internet.de/geonutzv/" target="_blank">GeoNutzV</a> law. Additionally, we have an explicit written permission.<br />The displayed data sets are merged by the Bundesamt für Naturschutz from data sets of the individual federal states. The Bundesamt für Naturschutz does not provide any warranty  regarding the precision and correctness of the data.<br />On the website of the Bundesamt you can find an <a href="http://www.geodienste.bfn.de/schutzgebiete" target="_blank">official map showing the protection areas</a>.', 'Deutsche Naturschutzgebiete werden als grünes Kartenoverlay angezeigt:<br /><img src="img/nsg.png" class="center-block img-thumbnail"><br /><br />Die Daten für deutsche Naturschutzgebiete werden vom <a href="http://www.bfn.de/" target="_blank">Bundesamt für Naturschutz</a> unter den Bedingungen der <a href="http://www.gesetze-im-internet.de/geonutzv/" target="_blank">GeoNutzV</a> zur Verfügung gestellt. Zudem liegt eine explizite schriftliche Nutzungsgenehmigung vor.<br />Die angezeigten Datensätze der verschiedenen Schutzgebietskategorien werden vom Bundesamt für Naturschutz aus Datenbeständen der Bundesländer zusammengeführt und harmonisiert. Dies geschieht auf Basis einer Datenbereitstellung, die einmal jährlich erfolgt, so dass Stichtagsdatensätze erstellt werden und damit zwischenzeitlich erfolgte Änderungen nicht abgebildet werden. Das Bundesamt für Naturschutz übernimmt daher keine Gewähr hinsichtlich der Lagegenauigkeit und Aktualität der Daten.<br />Auf den Internetseiten des Bundesamts finden sich außerdem weitere Informationen zu den Schutzgebieten, sowie eine <a href="http://www.geodienste.bfn.de/schutzgebiete" target="_blank">offizielle Kartendarstellung der Schutzgebiete</a>.')?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

  </body>
</html>
