if (!window.jQuery) {
	var script = document.createElement('script');
	script.type = "text/javascript";
	script.src = "https://code.jquery.com/jquery-1.10.2.js";
	document.getElementsByTagName('head')[0].appendChild(script);
}

jQuery(document).ready(function () {
	jQuery(".current_location").on("click", function () {
		console.log("click");
		initAutocomplete1();
	});
	jQuery(".woogeolocation_shortcode").insertAfter(".elementor-element-2079275");
});

google.maps.event.addDomListener(window, 'load', initAutocomplete);

useGmaps = false; /* set to not use by default */
googlemap_status = "";
var placeSearch, autocomplete, autocomplete_vendor;


function initAutocomplete1() {
	var service = new google.maps.places.AutocompleteService();
	service.getPlacePredictions({
		input: 'Brisbane,Australia',
		types: ['(cities)']
	},

		function (predictions, status) {
			if (status == google.maps.places.PlacesServiceStatus.OK) {
				useGmaps = true; /* status is ok so set flag to use Google Maps */
				if (useGmaps === true && typeof (google) !== 'undefined') {
					jQuery(".customer_api_error").hide();
					var startPos;
					var geoSuccess = function (position) {
						startPos = position;
						jQuery('#_latitude').val(startPos.coords.latitude);
						jQuery('#_longitude').val(startPos.coords.longitude);
						var geocoder = new google.maps.Geocoder();
						var latLng = new google.maps.LatLng(startPos.coords.latitude, startPos.coords.longitude);
						if (geocoder) {
							geocoder.geocode({ 'latLng': latLng }, function (results, status) {
								if (status == google.maps.GeocoderStatus.OK) {
									jQuery('#_address').val(results[0].formatted_address);
									var add = results[0].formatted_address;
									var value = add.split(",");
									var count = value.length;
									var city = value[count - 3];
									const country = results[0].address_components.find(item => item.types.includes('country'));
									jQuery('#_country').val(country.long_name);
									jQuery('#_city').val(city);
									for (var i = 0; i < results[0].address_components.length; i++) {
										for (var b = 0; b < results[0].address_components[i].types.length; b++) {
											if (results[0].address_components[i].types[b] == "administrative_area_level_1") {
												jQuery('#_state').val(results[0].address_components[i].long_name);
											}
											if (results[0].address_components[i].types[b] == "postal_code") {
												jQuery('#_postalcode').val(results[0].address_components[i].long_name);
											}
										}
									}

								}
							}); //geocoder.geocode()

						}
						setTimeout(function () {
							jQuery(".filter_trigger").trigger('click');
						}, 2000);
					};
					var error = function (positionError) {
						switch (positionError.code) {
							case positionError.TIMEOUT:
								ipinfo_location(2);
								break;
							case positionError.POSITION_UNAVAILABLE:
								ipinfo_location(2);
								break;
							case positionError.PERMISSION_DENIED:
								ipinfo_location(2);
								break;
							default:
						}
					};
					var result = navigator.geolocation.getCurrentPosition(geoSuccess, error, { enableHighAccuracy: true });


				} else {
					jQuery(".customer_api_error").text("Could not load the map correctly due to API issue.");
					jQuery("#_address").val("");
				}
			} else {
				jQuery("#_address").val("");
			}
		});


}


function initAutocomplete() {
	if (jQuery("#_address").val() == "") {
		var service = new google.maps.places.AutocompleteService();
		service.getPlacePredictions({
			input: 'Brisbane,Australia',
			types: ['(cities)']
		},

			function (predictions, status) {
				if (status == google.maps.places.PlacesServiceStatus.OK) {
					useGmaps = true; /* status is ok so set flag to use Google Maps */
					if (useGmaps === true && typeof (google) !== 'undefined') {
						jQuery(".customer_api_error").hide();
						jQuery(".woo-geo-location-products .map_loader_marker").show();
						jQuery(".wgm-vendor-map-mark .map_loader_marker").show();
						if (!(window.location.href.indexOf("_latitude") > -1) && (window.location.href.indexOf("showall_shop_result") == -1)) {

							//var get_loc_storage = localStorage.getItem('test');

							//if (get_loc_storage == "" || get_loc_storage == undefined || get_loc_storage == null) {
							//localStorage.setItem('test', "yes");
							var startPos;
							var geoSuccess = function (position) {
								startPos = position;
								jQuery('#_latitude').val(startPos.coords.latitude);
								jQuery('#_longitude').val(startPos.coords.longitude);
								var geocoder = new google.maps.Geocoder();
								var latLng = new google.maps.LatLng(startPos.coords.latitude, startPos.coords.longitude);
								if (geocoder) {
									geocoder.geocode({ 'latLng': latLng }, function (results, status) {
										if (status == google.maps.GeocoderStatus.OK) {
											jQuery('#_address').val(results[0].formatted_address);
											var add = results[0].formatted_address;
											var value = add.split(",");
											var count = value.length;
											var city = value[count - 3];
											const country = results[0].address_components.find(item => item.types.includes('country'));
											jQuery('#_country').val(country.long_name);
											jQuery('#_city').val(city);
											for (var i = 0; i < results[0].address_components.length; i++) {
												for (var b = 0; b < results[0].address_components[i].types.length; b++) {
													if (results[0].address_components[i].types[b] == "administrative_area_level_1") {
														jQuery('#_state').val(results[0].address_components[i].long_name);
													}
													if (results[0].address_components[i].types[b] == "postal_code") {
														jQuery('#_postalcode').val(results[0].address_components[i].long_name);
													}
												}
											}

										}
									}); //geocoder.geocode()

								}
								setTimeout(function () {
									jQuery(".filter_trigger").trigger('click');
								}, 2000);
							};
							var error = function (positionError) {
								switch (positionError.code) {
									case positionError.TIMEOUT:
										ipinfo_location(2);
										break;
									case positionError.POSITION_UNAVAILABLE:
										ipinfo_location(2);
										break;
									case positionError.PERMISSION_DENIED:
										ipinfo_location(2);
										break;
									default:
								}
							};
							var result = navigator.geolocation.getCurrentPosition(geoSuccess, error, { enableHighAccuracy: true });


							//	}


						}
					} else {
						jQuery(".customer_api_error").text("Could not load the map correctly due to API issue.");
						jQuery(".woo-geo-location-products .map_loader_marker").hide();
						jQuery(".wgm-vendor-map-mark .map_loader_marker").hide();
						jQuery("#_address").val("");
					}
				} else {
					jQuery(".customer_api_error").text("Could not load the map correctly due to API issue.");
					jQuery(".woo-geo-location-products .map_loader_marker").hide();
					jQuery(".wgm-vendor-map-mark .map_loader_marker").hide();
					jQuery("#_address").val("");
				}
			});


	} else {
		jQuery(".customer_api_error").css("display", "none");
	}

	// FOR SHOP WEBSITE
	if (document.getElementById('_address')) {
		autocomplete = new google.maps.places.Autocomplete(
/** @type {!HTMLInputElement} */(document.getElementById('_address')),
			{ types: ['geocode'] });
		autocomplete.addListener('place_changed', fillInAddress);
	}

	function fillInAddress() {
		var place = autocomplete.getPlace();
		var address = place.address_components;
		var city, state;
		address.forEach(function (component) {
			var types = component.types;
			if (types.indexOf('locality') > -1) {
				city = component.long_name;
			}
			if (types.indexOf('administrative_area_level_1') > -1) {
				state = component.long_name;
			}
		});
		const country = place.address_components.find(item => item.types.includes('country'));
		jQuery('#_city').val(city);
		jQuery('#_country').val(country.long_name);
		jQuery('#_state').val(state);
		jQuery('#_latitude').val(place.geometry.location.lat());
		jQuery('#_longitude').val(place.geometry.location.lng());
		for (var i = 0; i < place.address_components.length; i++) {
			for (var j = 0; j < place.address_components[i].types.length; j++) {
				if (place.address_components[i].types[j] == "postal_code") {
					jQuery('#_postalcode').val(place.address_components[i].long_name);
				}
			}
		}
	}
}




//GET USER CURRENT LOCATION BY USER IP ADDRESS ---- THIS FUN WILL BE CALLED ONLY ON IF USER BLOCK THEIR CURRENT LOCATION ACCESS
function ipinfo_location(page) {
	var myip = "";
	//GET USER IP ADDRESS
	jQuery.getJSON("https://api.ipify.org/?format=json", function (e) {
		myip = e.ip;
	});
	//GET USER LOCATION INFO BASED ON THEIR IP ADDRESS
	var ipapi = 'https://ipapi.co/' + myip + '/json';
	jQuery.getJSON(ipapi, function (ipinfo) {
		var latitude = ipinfo.latitude;
		var longitude = ipinfo.longitude;
		if (page == 1) {
			jQuery('#_wgm_vendorp_city').val(ipinfo.city);
			jQuery('#_wgm_vendorp_country').val(ipinfo.country);
			jQuery('#_wgm_vendorp_state').val(ipinfo.region);
			jQuery('#_wgm_vendorp_postalcode').val(ipinfo.postal);
			jQuery('#_wgm_vendorp_longitude').val(longitude);
			jQuery('#_wgm_vendorp_latitude').val(latitude);
			if (ipinfo.city != "" && ipinfo.region != "" && ipinfo.country != "") {
				jQuery('#_wgm_vendorp_address').val(ipinfo.city + ',' + ipinfo.region + ',' + ipinfo.country_name);
			} else if (ipinfo.city == "" && ipinfo.region != "" && ipinfo.country_name != "") {
				jQuery('#_wgm_vendorp_address').val(ipinfo.region + ',' + ipinfo.country_name);
			} else {
				jQuery('#_wgm_vendorp_address').val(ipinfo.country_name);
			}
		} else {
			jQuery('#_city').val(ipinfo.city);
			jQuery('#_country').val(ipinfo.country);
			jQuery('#_state').val(ipinfo.region);
			jQuery('#_postalcode').val(ipinfo.postal);
			jQuery('#_longitude').val(longitude);
			jQuery('#_latitude').val(latitude);
			if (ipinfo.city != "" && ipinfo.region != "" && ipinfo.country != "") {
				jQuery('#_address').val(ipinfo.city + ',' + ipinfo.region + ',' + ipinfo.country_name);
			} else if (ipinfo.city == "" && ipinfo.region != "" && ipinfo.country_name != "") {
				jQuery('#_address').val(ipinfo.region + ',' + ipinfo.country_name);
			} else {
				jQuery('#_address').val(ipinfo.country_name);
			}
		}

	});

	setTimeout(function () {
		jQuery(".filter_trigger").trigger('click');
	}, 2000);
}

// for range slider

const allRanges = document.querySelectorAll(".range-wrap");
allRanges.forEach(wrap => {
	const range = wrap.querySelector(".range");
	const bubble = wrap.querySelector(".bubble");

	range.addEventListener("input", () => {
		setBubble(range, bubble);
	});
	setBubble(range, bubble);
});

function setBubble(range, bubble) {
	const val = range.value;
	const min = range.min ? range.min : 0;
	const max = range.max ? range.max : $('#rangeslide').attr('max');
	const newVal = Number(((val - min) * 100) / (max - min));
	bubble.innerHTML = val;

	// Sorta magic numbers based on size of the native UI thumb
	bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
}

