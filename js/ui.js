/*jslint
  indent: 4
*/

/*global
  $, document, gapi, setTimeout,
  CDDA, Coordinates, Freifunk, Hillshading, NPA, Okapi, Sidebar, Storage,
  DownloadGPX, Geolocation,
  showMulticoordinatesDialog, Markers
*/


/* coordinate format */
function setCoordinatesFormat(t) {
    'use strict';

    Storage.set('coordinatesFormat', t);

    if ($('#coordinatesFormat').val() !== t) {
        $('#coordinatesFormat').val(t);
    }

    Coordinates.setFormat(t);
    Markers.update();
}


function restoreCoordinatesFormat(defaultValue) {
    'use strict';

    var t = Storage.getString("coordinatesFormat", "DM");

    if (t === "DM" || t === "DMS" || t === "D") {
        setCoordinatesFormat(t);
    } else {
        setCoordinatesFormat(defaultValue);
    }
}


/* info dialog */
function showInfoDialog() {
    'use strict';

    $('#dlgInfoAjax').modal({show: true, backdrop: "static", keyboard: true});
}


/* alert dialog */
function showAlert(title, msg) {
    'use strict';

    $("#dlgAlertHeader").html(title);
    $("#dlgAlertMessage").html(msg);
    $("#dlgAlert").modal({show: true, backdrop: "static", keyboard: true});
}


/* projection dialog */
function showProjectionDialog(callback) {
    'use strict';

    $('#projectionDialogOk').off('click');
    $('#projectionDialogOk').click(function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('#projectionDialog').modal('hide');
        if (callback) {
            setTimeout(function () {
                callback($("#projectionBearing").val(), $("#projectionDistance").val());
            }, 10);
        }
    });
    $("#projectionDialog").modal({show: true, backdrop: "static", keyboard: true});
}


/* permalink dialog */
function showLinkDialog(linkUrl) {
    'use strict';

    $('#linkDialogLink').val(linkUrl);
    $('#linkDialog').modal({show: true, backdrop: "static", keyboard: true});
    $('#linkDialogLink').select();
}


function linkDialogShortenLink() {
    'use strict';

    var longUrl = $('#linkDialogLink').val();
    gapi.client.setApiKey('AIzaSyC_KjqwiB6tKCcrq2aa8B3z-c7wNN8CTA0');
    gapi.client.load('urlshortener', 'v1', function () {
        var request = gapi.client.urlshortener.url.insert({resource: {longUrl: longUrl}});
        request.execute(function (resp) {
            if (resp.error) {
                $('#linkDialogError').html('Error: ' + resp.error.message);
            } else {
                $('#linkDialogLink').val(resp.id);
                $('#linkDialogLink').select();
            }
        });
    });
}


/* setup button events */
$(document).ready(function () {
    'use strict';

    $("#sidebartoggle").click(function () {
        if ($('#sidebar').is(':visible')) {
            Sidebar.hide();
        } else {
            Sidebar.show();
        }
    });
    $('#buttonWhereAmI').click(function () {
        Geolocation.whereAmI();
    });
    $("#hillshading").click(function () {
        Hillshading.toggle($('#hillshading').is(':checked'));
    });
    $("#npa").click(function () {
        NPA.toggle($('#npa').is(':checked'));
    });
    $("#cdda").click(function () {
        CDDA.toggle($('#cdda').is(':checked'));
    });
    $("#geocaches").click(function () {
        Okapi.toggle($('#geocaches').is(':checked'));
    });
    $('#coordinatesFormat').change(function () {
        setCoordinatesFormat($('#coordinatesFormat').val());
    });
    $("#freifunk").click(function () {
        Freifunk.toggle($('#freifunk').is(':checked'));
    });
    $("#buttonUploadGPX").click(function (e) {
        $("#buttonUploadGPXinput").click();
        e.preventDefault();
    });
    $("#buttonExportGPX").click(function () {
        DownloadGPX.initiateDownload();
    });
    $("#buttonMulticoordinates").click(function () {
        showMulticoordinatesDialog();
    });
});
