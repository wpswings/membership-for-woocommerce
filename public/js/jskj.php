<?php
global $WCFM, $WCFMmp, $post, $wp;
global $wp_session;

$max_radius_to_search = isset($WCFMmp->wcfmmp_marketplace_options['max_radius_to_search']) ? $WCFMmp->wcfmmp_marketplace_options['max_radius_to_search'] : '100';
$radius_range = isset($_GET['radious']) ? wc_clean($_GET['radious']) : (absint(apply_filters('wcfmmp_radius_filter_max_distance', $max_radius_to_search)) / apply_filters('wcfmmp_radius_filter_start_distance', 10));
$current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!-- SEARCH BOX HTML FORM STATED HERE !-->
<?php
$container_class = "";
if (is_front_page()) {
	$container_class = "container";
}
?>

<div class="map_loader_marker <?php echo $container_class; ?>">
	<!-- MAP WILL BE APPED HERE !-->
	<?php
	if (is_shop() || (strpos($current_url, 'product-category') != false) || is_front_page()) {
	} else {
	?>
		<div class="page_loading" style="display:block;margin: auto;text-align: center;">
			<img src="<?php echo plugins_url(); ?>/wc-frontend-manager/assets/images/dribbble_nearbyloader.gif" />
		</div>
		<div id="map" style="width: 100%;height: 350px;"></div>
	<?php
	}
	?>
	<!-- FORM MAIN CLASS -->
	<div class="inner_msg">
		<?php
		// URL FORM FORM ACTION
		global $wp;
		$cus_base_url = home_url($wp->request);

		// GET ALL THE SEARCHED PARAMS TO SET THE INPUT FIELDS

		if (is_front_page()) {
			setcookie("check_browser_cookie", "yes", time() + 3600, "/", "budgetershopy.com", 0);
			if (!isset($_COOKIE["check_browser_cookie"])) { ?>
				<!-- <p style="background: red;color: #fff;text-align: center;margin-top: 10px;margin-bottom: 0px;font-weight: 600;">We make use of cookies to improve location experience. Please allow the cookies in your browser settings.</p> -->
		<?php
			}

			if (isset($_GET['_latitude'])) {
				setcookie("wgm_user_lat", $_GET['_latitude'], time() + 3600, "/");
			} else {
				setcookie("wgm_user_lat", $_COOKIE["wgm_user_lat"], time() + 3600, "/");
			}

			if (isset($_GET['_longitude'])) {
				setcookie("wgm_user_lon", $_GET['_longitude'], time() + 3600, "/");
			} else {
				setcookie("wgm_user_lon", $_COOKIE["wgm_user_lon"], time() + 3600, "/");
			}

			if (isset($_GET['_city'])) {
				setcookie("wgm_user_city", $_GET['_city'], time() + 3600, "/"); // 86400 = 1 day
			} else {
				setcookie("wgm_user_city", $_COOKIE["wgm_user_city"], time() + 3600, "/"); // 86400 = 1 day
			}

			if (isset($_GET['_state'])) {
				setcookie("wgm_user_state", $_GET['_state'], time() + 3600, "/"); // 86400 = 1 day
			} else {
				setcookie("wgm_user_state", $_COOKIE["wgm_user_state"], time() + 3600, "/"); // 86400 = 1 day
			}

			if (isset($_GET['_country'])) {
				setcookie("wgm_user_country", $_GET['_country'], time() + 3600, "/"); // 86400 = 1 day
			} else {
				setcookie("wgm_user_country", $_COOKIE["wgm_user_country"], time() + 3600, "/"); // 86400 = 1 day
			}

			if (isset($_GET['_address'])) {
				setcookie("wgm_user_loc", $_GET['_address'], time() + 3600, "/"); // 86400 = 1 day
			} else {
				setcookie("wgm_user_loc", $_COOKIE["wgm_user_loc"], time() + 3600, "/", "", 1); // 86400 = 1 day
			}
		}
		if (!empty($_SESSION['address_searched_address'])) {
			$session_address = end($_SESSION["address_searched_address"]);
			$session_address_searched = end($_SESSION["address_searched"]);
			$session_address_state = end($_SESSION["address_searched_state"]);
			$session_address_country = end($_SESSION["address_searched_country"]);
			$session_address_postelcode = end($_SESSION["address_searched_postalcode"]);
		} else {
			$session_address = !empty($_SESSION['wgm_user_loc']) ? $_SESSION['wgm_user_loc'] : "";
			$session_address_searched =  !empty($_SESSION['wgm_user_city']) ? $_SESSION['wgm_user_city'] : "";
			$session_address_state = !empty($_SESSION['wgm_user_state']) ? $_SESSION['wgm_user_state'] : "";
			$session_address_country =  !empty($_SESSION['wgm_user_country']) ? $_SESSION['wgm_user_country'] : "";
			$session_address_postelcode = "";
		}

		$session_address = !empty($_COOKIE['wgm_user_loc']) ? $_COOKIE['wgm_user_loc'] : "";
		$session_address_searched =  !empty($_COOKIE['wgm_user_city']) ? $_COOKIE['wgm_user_city'] : "";
		$session_address_state = !empty($_COOKIE['wgm_user_state']) ? $_COOKIE['wgm_user_state'] : "";
		$session_address_country =  !empty($_COOKIE['wgm_user_country']) ? $_COOKIE['wgm_user_country'] : "";
		$session_address_postelcode = "";

		$sess_lat = !empty($_COOKIE["wgm_user_lat"]) ? $_COOKIE["wgm_user_lat"] : "";
		$sess_long = !empty($_COOKIE["wgm_user_lon"]) ? $_COOKIE["wgm_user_lon"] : "";
		$address = !empty($_GET['_address']) ? $_GET['_address'] : $session_address;
		$latitude = !empty($_GET['_latitude']) ? $_GET['_latitude'] : $sess_lat;
		$longitude = !empty($_GET['_longitude']) ? $_GET['_longitude'] : $sess_long;
		$city = !empty($_GET['_city']) ? $_GET['_city'] : $session_address_searched;
		$state = !empty($_GET['_state']) ? $_GET['_state'] : $session_address_state;
		$country = !empty($_GET['_country']) ? $_GET['_country'] : $session_address_country;
		$postelcode = !empty($_GET['_postalcode']) ? $_GET['_postalcode'] : $session_address_postelcode;

		?>

		<form method="get" action="<?php echo $cus_base_url; ?>" class="search_click" novalidate id="post">
			<input type="hidden" value="<?php echo ($latitude); ?>" name="_latitude" id="_latitude">
			<input type="hidden" value="<?php echo ($longitude); ?>" name="_longitude" id="_longitude">
			<input type="hidden" value="<?php echo (urldecode($city)); ?>" name="_city" id="_city">
			<input type="hidden" value="<?php echo (urldecode($state)); ?>" name="_state" id="_state">
			<input type="hidden" value="<?php echo (urldecode($country)); ?>" name="_country" id="_country">
			<input type="hidden" value="<?php echo ($postelcode); ?>" name="_postalcode" id="_postalcode">
			<?php
			$uaddress = rawurldecode($address);
			$udaddress = urldecode($uaddress);
			?>
			<table style="width: 100%;">
				<tr>
					<td>

						<?php if (is_shop() || (strpos($current_url, 'product-category') != false)) {
							// if (!empty($sess_lat)) {
							// 	$ids = "";
							// } else {
							// 	$ids = "_address";
							// }
							$ids = "_address";
						?>
							<input type="hidden" name="_address" id="<?php echo $ids; ?>" value="<?php echo rawurldecode($udaddress); ?>" />
							<?php if (!empty($udaddress)) { ?>
								<label> Location: <?php echo rawurldecode($udaddress); ?></label>
							<?php } ?>
						<?php } else if (is_front_page()) {  ?>
							<label> Current location: </label>
							<input type="text" name="_address" style="float: left;" value="<?php echo rawurldecode($udaddress); ?>" id="_address" class="_address" placeholder="Place Name">
							<span><button type="button" class="current_location"><img src="https://budgetershopy.com/wp-content/plugins/woo-geo-location/assets/img/current_location.jpg" /></button></span>
							<button type="submit" value="Search" name="filtername" class="fron_page_submit filter_trigger">
							<?php } else { ?>
								<label> Current location: </label>
								<input type="text" name="_address" style="float: left;" value="<?php echo rawurldecode($udaddress); ?>" id="_address" class="_address" placeholder="Place Name">
							<?php } ?>
					</td>
					<?php

					if (is_shop() || (strpos($current_url, 'product-category') != false)) {
					?>
				</tr>
				<tr>
				<?php
					}
				?>
				<?php
				if ((strpos($current_url, 'product-category') != false) || (is_shop())) {
				?>

					<td>
						<label> Product Search: </label>
						<input type="text" style="width:100%;" name="_psearch" value="<?php echo !empty($_GET['_psearch']) ? $_GET['_psearch'] : ""; ?>" id="searchbar" class="searchbar" placeholder="Search Products">
						<input type="hidden" name="radious" value="<?php echo !empty($_GET['radious']) ? $_GET['radious'] : 3 ?>" />
						<input type="hidden" name="searchwith" value="km" />
					</td>

					<?php if (is_front_page() == false) { ?>
				</tr>
			<?php } ?>
		<?php } ?>


		<?php
		if (is_shop()) {
		?>
			<tr>
				<input type="hidden" id="selected_category" name="product_cat" value="<?php echo !empty($_GET['product_cat']) ? $_GET['product_cat'] : ""; ?>">
			</tr>
		<?php
		}
		if (strpos($current_url, 'product-category') != false) {
			global $wp;
			$get_cat = add_query_arg($wp->query_vars, home_url($wp->request));
			$_GET['product_cat'] = get_query_var('product_cat');
		?><tr>
				<input type="hidden" id="selected_category" value="<?php echo !empty($_GET['product_cat']) ? $_GET['product_cat'] : ""; ?>">
			</tr>
		<?php
		}
		?>
		<?php if (is_front_page()) { ?>

		<?php } else { ?>
			<tr>
				<td colspan="2" style="text-align: center;padding-top: 20px;">
					<input type="submit" value="Show All" name="showall" id="showall" style="display: none;" />&nbsp;&nbsp;&nbsp;
					<input type="submit" value="Search" class="filter_trigger btn btn-success" name="filtername">
					<?php if (is_shop() || strpos($current_url, 'product-category') != false) { ?>
						<a class="btn btn-warning" href="<?php echo home_url(); ?>">Back</a>
					<?php } ?>
				</td>
			</tr>
		<?php }	?>
			</table>
		</form>
		<input type="hidden" id="woo_shop_page_url" value="<?php echo home_url(add_query_arg(array(), $wp->request)); ?>" />

	</div>
	<!-- innser msg end -->
</div>

<?php if (is_shop() || (strpos($current_url, 'product-category') != false) || is_front_page()) {
} else { ?>
	<script>
		// MAP FUNCTION START HERE  
		function initMap() {
			//GET ALL THE SEARCHED PARAMS FROM URL
			jQuery(".page_loading").hide();
			var lat = "";
			var lon = "";
			let searchParams = new URLSearchParams(window.location.search);
			if (searchParams.has("_latitude") === true) {
				lat = searchParams.get('_latitude');
			} else {
				lat = jQuery("#_latitude").val();
			}

			if (searchParams.has("_longitude") === true) {
				lon = searchParams.get('_longitude');
			} else {
				lon = jQuery("#_longitude").val();
			}

			if (searchParams.has("_city") === true) {
				var city = decodeURIComponent(searchParams.get('_city'));
			} else {
				var city = jQuery("#_city").val();
			}

			if (searchParams.has("_state") === true) {
				var state = decodeURIComponent(searchParams.get('_state'));
			} else {
				var state = jQuery("#_state").val();
			}

			if (searchParams.has("radious") === true) {
				var radious = searchParams.get('radious');
			} else {
				var radious = jQuery(".select_kms").val();
			}

			if (searchParams.has("searchwith") === true) {
				var searchwith = searchParams.get('searchwith');
			} else {
				var searchwith = jQuery(".radioBtnClass").val();
			}

			if (searchParams.has("_postalcode") === true) {
				var postalcode = searchParams.get('_postalcode');
			} else {
				var postalcode = jQuery("#_postalcode").val();
			}

			if (searchParams.has("_address") === true) {
				if (jQuery("#_address").val() != "") {
					var address = jQuery("#_address").val()
				} else {
					var address = decodeURIComponent(searchParams.get('_address'));
				}
			} else {
				var address = jQuery("#_address").val();
			}

			if (searchParams.has("_country") === true) {
				var country = decodeURIComponent(searchParams.get('_country'));
			} else {
				var country = jQuery("#_country").val();
			}

			if (searchParams.has("product_cat") === true) {
				var pcat = searchParams.get('product_cat');
			} else if (jQuery("#selected_category").val() != "") {
				var pcat = jQuery("#selected_category").val();
			} else {
				var pcat = ""
			}
			if (searchParams.has("showall_value") === true) {
				var showall_value = searchParams.get('showall_value');
			}
			if (searchParams.has("_psearch") === true) {
				var psearch = searchParams.get('_psearch');
			} else {
				var psearch = ""
			}
			if (searchParams.has("min_price") === true) {
				var min_price = searchParams.get('min_price');
			} else {
				var min_price = "";
			}
			if (searchParams.has("max_price") === true) {
				var max_price = searchParams.get('max_price');
			} else {
				var max_price = "";
			}

			var center = new google.maps.LatLng(parseFloat(lat), parseFloat(lon));

			//THE MAP TO DISPLAY.
			var map = new google.maps.Map(document.getElementById("map"), {
				center: new google.maps.LatLng(lat, lon),
				mapTypeId: google.maps.MapTypeId.TERRAIN,
				disableDefaultUI: true
			});



			//PASS THE ALL THE VALUES TO THE GETTING THE PRODUCT IDS BASED ON LOCATION HOOK USING AJAX.
			var post_url = "<?php echo admin_url('admin-ajax.php'); ?>";
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: post_url,
				cache: true,
				data: {
					action: "wgm_append_data_to_map",
					psearch: psearch,
					lat: lat,
					lon: lon,
					city: city,
					state: state,
					country: country,
					radious: radious,
					searchwith: searchwith,
					postalcode: postalcode,
					address: address,
					product_cat: pcat,
					showall_value: showall_value,
					max_price: max_price,
					min_price: min_price
				},
				success: function(data) {
					// IF SUCCESS MEANS PRODUCTS ARE THERE IN THE LOCATION & IF RESPONSE IS ERROR MEANS THERE IS AN NO PRODUCT IN THE LOCATION
					if (data.result == "success") {
						jQuery(".customer_api_error").hide();
						if (data.data != "") {
							var center = new google.maps.LatLng(parseFloat(data.data[0].lat), parseFloat(data.data[0].lon));
						} else {
							var center = new google.maps.LatLng(parseFloat(lat), parseFloat(lon));
						}
						// MAP ATTRIBUTES.
						var mapAttr = {
							center: center,
							mapTypeId: google.maps.MapTypeId.TERRAIN,
							disableDefaultUI: true
						};

						//THE MAP TO DISPLAY.
						var map = new google.maps.Map(document.getElementById("map"), mapAttr);
						//CIRCLE BEGIN
						var circle = new google.maps.Circle({
							center: center,
							map: map,
							radius: parseFloat(1) * 1000, //convert 1000 meter = 1 km
							fillColor: '#e5e5ff', //fillOpacity: 0.1,
							strokeColor: "green", //circle color
							strokeWeight: 1 //circle stroke
						});
						// GET THE ZOOM LEVEL VALUE ADDED BY ADMIN IN THE SETTINGS
						<?php
						$get_option_val = "12";
						if ($get_option_val >= 0) {
							$zoom_level = $get_option_val;
						} else {
							$zoom_level = 10;
						}
						?>
						map.setZoom(<?php echo $zoom_level; ?>);
						// CIRCLE END
						var markers = [];
						var marker, i;
						var infowindow = new google.maps.InfoWindow();
						var arrayData = data.data;

						for (var i = 0; i < arrayData.length; i++) {
							var description = arrayData[i].description;
							var lat = parseFloat(arrayData[i].lat);
							var lon = parseFloat(arrayData[i].lon);
							var iconUrl = arrayData[i].iconUrl;
							var product_img = arrayData[i].product_img;
							var url = arrayData[i].url;

							//MARK THE PRODUCT LOCATION TO MAP
							marker = new google.maps.Marker({
								position: new google.maps.LatLng(lat, lon),
								icon: {
									url: iconUrl,
									scaledSize: new google.maps.Size(45, 45)
								},
								map: map,
								description: description,
								product_img: product_img,
								url: url
							});
							markers.push(marker);
							//ATTACH THE ICON TO THE MARKER
							google.maps.event.addListener(marker, 'click', (function(marker, i, data) {
								return function() {
									infowindow.setContent("<div class='wgm_info_window'><div class='map_p'>" + this.description + "</div><div>" + this.product_img + "</div><div><a class='map_link_custom' target='_blank' href=" + this.url + "><?php esc_html_e('Buy Now', 'Product view'); ?></a></div>");
									infowindow.open(map, marker);
								}
							})(marker, i, data));
							// MAP END
						}
						var markerCluster = new MarkerClusterer(map, markers, {
							imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
						});

					} else {
						var center = new google.maps.LatLng(parseFloat(data.data.lat), parseFloat(data.data.lon));

						// MAP ATTRIBUTES.
						var mapAttr = {
							center: center,
							mapTypeId: google.maps.MapTypeId.TERRAIN,
							disableDefaultUI: true
						};

						//THE MAP TO DISPLAY.
						var map = new google.maps.Map(document.getElementById("map"), mapAttr);
						//CIRCLE BEGIN
						var circle = new google.maps.Circle({
							center: center,
							map: map,
							radius: parseFloat(1) * 1000, //convert 1000 meter = 1 km
							fillColor: '#e5e5ff', //fillOpacity: 0.1,
							strokeColor: "green", //circle color
							strokeWeight: 1 //circle stroke
						});
						// CIRCLE END

						// GET THE ZOOM LEVEL VALUE ADDED BY ADMIN IN THE SETTINGS
						<?php
						$get_option_val = 12;
						if ($get_option_val >= 0) {
							$zoom_level = $get_option_val;
						} else {
							$zoom_level = 10;
						}
						?>
						map.setZoom(<?php echo $zoom_level; ?>);
					}
				}
			});

		}
	</script>
<?php } ?>

<script>
	// LOAD THE INIT MAP FUNCITON AFTER PAGE LOADDED FEW SECONDS
	<?php if (is_front_page()) { ?>
		jQuery(document).ready(function() {
			if (jQuery("#_latitude").val() != "") {
				jQuery(".elementor-element-50ec977 a").attr("href", "https://budgetershopy.com/product-category/rice-bags/organic-rice/");
				//jQuery('.search_click').attr('action', 'https://budgetershopy.com/shop');
			}
		});
	<?php } ?>

	jQuery(document).ready(function() {
		let searchParams = new URLSearchParams(window.location.search);
		if ((searchParams.has("_latitude") === false) && (jQuery("#_latitude").val() != "")) {
			<?php if (is_shop() || (strpos($current_url, 'product-category') != false)) { ?>
			<?php } else { ?>
				initMap();
			<?php } ?>
			jQuery(".filter_trigger").trigger('click');
			jQuery('.woocommerce-result-count').html('');
			jQuery('.woocommerce-result-count').append("<p>Showing Product Is From <b>" + jQuery("#_address").val() + "</b></p>");
		} else {
			<?php if (is_shop() || (strpos($current_url, 'product-category') != false) || is_front_page()) { ?>
			<?php } else { ?>
				setTimeout(function() {
					initMap();
					if ((searchParams.has("_latitude") === false) && (jQuery("#_latitude").val() == "")) {
						initAutocomplete();
					}
					jQuery('.woocommerce-result-count').html('');
					jQuery('.woocommerce-result-count').append("<p>Showing Product Is From <b>" + jQuery("#_address").val() + "</b></p>");
				}, 2000);
			<?php } ?>

		}
	});
</script>

<?php
if (is_front_page()) {
?>
	<script>
		jQuery('#_address').on('change', function() {
			setTimeout(function() {
				jQuery(".filter_trigger").trigger('click');
			}, 2000);
		});
	</script>
<?php
}
?>

<script>
	// GET ALL THE SELECTED CATEGORIES AND APPED TO HIDDEN FIELDS
	jQuery('#product_cat').on('change', function() {
		var items = [];
		jQuery('#product_cat option:selected').each(function() {
			items.push(jQuery(this).val());
		});
		var result = items.join(',');
		jQuery("#selected_category").val(result);
	});
</script>

<style>
	.map_loader_marker label {
		color: #000;
		margin-top: 13px;
		font-weight: 600;
	}

	.map_loader_marker {
		box-shadow: 1px 1px 20px 0 #e9e9e9;
		margin-bottom: 25px;
		padding: 0px 20px;
		padding-top: 20px;
		padding-bottom: 2px;
	}

	.map_loader_marker input[type=text] {
		font-size: 15px;
		display: inline-block;
		box-shadow: 1px 1px 5px 0 #e9e9e9;
		background-color: #fff !important;
		border: 1px solid #ccc !important;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		border-radius: 3px;
		min-height: 38px;
		width: 98% !important;
	}

	.radious_select input {
		outline: none;
		width: 100%;
	}

	.range-wrap {
		position: relative;
		margin: 0 auto 3rem;
	}

	.range {
		width: 100%;
	}

	.bubble {
		background: red;
		color: white;
		padding: 4px 12px;
		position: absolute;
		border-radius: 4px;
		left: 50%;
		transform: translateX(-50%);
		top: 20px
	}

	.bubble::after {
		content: "";
		position: absolute;
		width: 2px;
		height: 2px;
		background: red;
		top: -1px;
		left: 50%;
	}

	span.range_left {
		position: absolute;
		top: 45%;
		left: 43px;
	}

	span.range_right {
		position: absolute;
		bottom: 34%;
		right: 43px;
	}

	button.current_location img {
		height: 36px;
		width: 100%;
		object-fit: cover;
	}

	button.current_location {
		padding: 0px;
		background: #fff;
		border: unset;
		float: right;
		position: relative;
		bottom: 38px;
		right: 23px;
		border-top: 1px solid #ccc;
		width: 50px;
	}

	.fron_page_submit {
		visibility: hidden;
	}

	.woogeolocation_shortcode .map_loader_marker.container table {
		margin-bottom: 0px !important;
	}

	.woogeolocation_shortcode .map_loader_marker.container {
		padding-top: 10px !important;
		padding-bottom: 10px !important;
	}

	.woogeolocation_shortcode .map_loader_marker.container {
		margin-top: 30px;
	}

	@media (max-width: 600px) {
		.map_loader_marker input[type=text] {
			width: 95% !important;
		}
	}
</style>

<?php


if ((strpos($current_url, 'product-category') != false) || (is_shop()) || (is_front_page())) {
	global $WCFM, $WCFMmp;
	$api_key = isset($WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api']) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
	if (!empty($api_key)) {
?>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key; ?>&libraries=places&ver=5.9.3" id="wcfm-store-google-maps-js"></script>
<?php
	}
}
?>
<script type="text/javascript" src="https://budgetershopy.com/wp-content/plugins/woo-geo-location/assets/js/wgmurrentlocation.js?v=<?php echo strtotime("now"); ?>"></script>
<script type="text/javascript" src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>