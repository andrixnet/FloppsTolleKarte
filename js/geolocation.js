/*jslint
  regexp: true
  indent: 4
*/

/*global
  Coordinates, $, google, trackSearch, showAlert, Lang, navigator
*/

var Geolocation = {};
Geolocation.m_map = null;

Geolocation.init = function (map) {
    'use strict';

    this.m_map = map;
};


Geolocation.search = function (address) {
    'use strict';

    address = String(address);
    trackSearch(address);

    var coords = Coordinates.fromString(address),
        url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" + address,
        the_map = this.m_map;

    if (!coords) {
        $.get(url)
            .done(function (data) {
                if (data.length > 0) {
                    the_map.setCenter(new google.maps.LatLng(data[0].lat, data[0].lon));
                } else {
                    showAlert(
                        Lang.t("dialog.search_error.title"),
                        Lang.t("dialog.search_error.content").replace(/%1/, address)
                    );
                }
            })
            .fail(function () {
                showAlert(
                    Lang.t("dialog.search_error.title"),
                    Lang.t("dialog.search_error.content").replace(/%1/, address)
                );
            });
    } else {
        this.m_map.setCenter(coords);
    }
};


Geolocation.whereAmI = function (showError) {
    'use strict';

    trackSearch("whereami");

    var the_map = this.m_map;

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                the_map.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
            },
            function () {
                if (showError) {
                    showAlert(
                        Lang.t("dialog.whereami_error.title"),
                        Lang.t("dialog.whereami_error.content")
                    );
                }
            }
        );
    } else if (showError) {
        showAlert(
            Lang.t("dialog.whereami_error.title"),
            Lang.t("dialog.whereami_error.content")
        );
    }
};
