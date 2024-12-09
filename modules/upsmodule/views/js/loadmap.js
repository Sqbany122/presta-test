/**
  * @author    United Parcel Service of America, Inc.*
  * @copyright (c) 2019 United Parcel Service of America, Inc., all rights reserved*
  * @license   This work is Licensed under the Academic Free License version 3.0
  *            http://opensource.org/licenses/afl-3.0.php *
  * @link      https://www.ups.com/pl/en/services/technology-integration/ecommerce-plugins.page *
 */

// bing map
var map, searchManager;
var indexMap = [];

function GetMap() {

    var locations = {
        'BE' : [50.844391, 4.35609],
        'NL' : [52.373055, 4.892222],
        'FR' : [48.85717, 2.3414],
        'ES' : [40.42028, -3.70577],
        'PL' : [52.2356, 21.01037],
        'IT' : [41.903221, 12.49565],
        'DE' : [52.516041, 13.37691],
        'GB' : [51.50632, -0.12714],
        'US' : [47.411297, -120.556267]
    };

    if (countryCode in locations) {
        var geo = locations[countryCode];
        map = new Microsoft.Maps.Map('#myMap', {
            center: new Microsoft.Maps.Location(geo[0], geo[1]),
            zoom: 8
        });
    } else {
        map = new Microsoft.Maps.Map('#myMap', {});
    }

    Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
        searchManager = new Microsoft.Maps.Search.SearchManager(map);
        var requestOptions = {
            bounds: map.getBounds(),
            where: countryCode,
            includeCountryIso2: true,
            callback: function (answer, userData) {

                answer.results.forEach(function(e){
                    if (e.address.countryRegionISO2 == countryCode) {
                        map.setView({ center: e.bestView.center });
                        map.entities.push(new Microsoft.Maps.Pushpin(e.location));
                    }
                });
            }
        };
        searchManager.geocode(requestOptions);
    });
}

function getBoundary(geocodeResult) {
    var labelMap = '';
    var str = geocodeResult.results[0].address.formattedAddress;
    var streetaddress= str.substr(0, str.indexOf(','))*1;
    streetaddress = streetaddress.toFixed(6);
    indexMap.forEach(function (value, index) {
        if (value.value == streetaddress)
        {
            var num = value.index*1 + 1
            labelMap = num.toString();
        }
    });
    //Add the first result to the map and zoom into it.
    if (geocodeResult && geocodeResult.results && geocodeResult.results.length > 0) {
        //Zoom into the location.
        map.setView({ bounds: geocodeResult.results[0].bestView });

        //Create the request options for the GeoData API.
        var geoDataRequestOptions = {
            lod: 1,
            getAllPolygons: true
        };

        var path = '';

        if ($("#view_dir").length === 1) {
            path = $("#view_dir").val();
        } else {
            var currentUrl = document.URL;
            var arrUrl = currentUrl.split("index");
            path = arrUrl[0] + 'modules/upsmodule/views';
        }

        //Verify that the geocoded location has a supported entity type.
        switch (geocodeResult.results[0].entityType) {
            case "CountryRegion":
            case "AdminDivision1":
            case "AdminDivision2":
            case "Postcode1":
            case "Postcode2":
            case "Postcode3":
            case "Postcode4":
            case "Neighborhood":
            case "PopulatedPlace":
                geoDataRequestOptions.entityType = geocodeResult.results[0].entityType;
                break;
            default:

                //Display a pushpin if GeoData API does not support EntityType.
                var pin = new Microsoft.Maps.Pushpin(geocodeResult.results[0].location, {
                        text: labelMap,
                        icon: path + '/img/red_pin.png',
                    });
                map.entities.push(pin);
                return;
        }

        //Use the GeoData API manager to get the boundaries of the zip codes.
        Microsoft.Maps.SpatialDataService.GeoDataAPIManager.getBoundary(
            geocodeResult.results[0].location,
            geoDataRequestOptions,
            map,
            function (data) {
                //Add the polygons to the map.
                if (data.results && data.results.length > 0) {
                    map.entities.push(data.results[0].Polygons);
                } else {
                    //Display a pushpin if a boundary isn't found.
                    var center = map.getCenter();

                    //Create custom Pushpin
                    var pin = new Microsoft.Maps.Pushpin(center, {
                        text: labelMap,
                        icon: path + '/img/red_pin.png',
                    });

                    map.entities.push(pin);
                }
            });
    }
}
