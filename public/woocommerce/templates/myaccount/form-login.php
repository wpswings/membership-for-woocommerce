<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// get captcha data.
$wps_mfw_enable_override_login_signup   = get_option( 'wps_mfw_enable_override_login_signup' );
$wps_mfw_enable_google_recaptcha        = get_option( 'wps_mfw_enable_google_recaptcha' );
$wps_mfw_site_captcha_key               = get_option( 'wps_mfw_site_captcha_key' );
$wps_mfw_captcha_secret_key             = get_option( 'wps_mfw_captcha_secret_key' );
$wps_mfw_user_welcome_msg               = get_option( 'wps_mfw_user_welcome_msg' );
$wps_mfw_login_form_color               = get_option( 'wps_mfw_login_form_color' );
do_action( 'woocommerce_before_customer_login_form' ); ?>

<!-- +++++++  Login Form +++++++++ -->
<div class="u-columns col2-set wps_mfw_membership_login_wrapper" id="customer_login">
	<div class="wps-mfw_apw-col wps_mfw_login_wrapper">

		<h2><?php esc_html_e( 'Log in', 'membership-for-woocommerce' ); ?></h2>
		<h5><?php echo esc_html( $wps_mfw_user_welcome_msg ); ?></h5>
		<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e( 'Username or email address', 'membership-for-woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'membership-for-woocommerce' ); ?></span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e( 'Password', 'membership-for-woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'membership-for-woocommerce' ); ?></span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<div class="form-row">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<div class="g-recaptcha" data-sitekey="<?php echo esc_html( $wps_mfw_site_captcha_key ); ?>"></div>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'membership-for-woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'membership-for-woocommerce' ); ?></button>
			</div>
			<p class="woocommerce-LostPassword lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'membership-for-woocommerce' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>
			<div class="wps_mfw_show_signup_option">
				<?php /* translators: %s: signup */ printf( esc_html__( "Don't have an account? %s", 'membership-for-woocommerce' ), '<a href="#">' . esc_html__( 'Sign Up', 'membership-for-woocommerce' ) . '</a>' ); ?>
			</div>
		</form>
	</div>

	<!-- +++++++  Registration Form +++++++++ -->
	<div class="wps-mfw_apw-col wps_mfw_registration_wrapper" style="display: none;">
		<h2><b><?php esc_html_e( 'Sign Up', 'membership-for-woocommerce' ); ?></b></h2>
		<h5><b><?php echo esc_html( $wps_mfw_user_welcome_msg ); ?></b></h5>
		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >
			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Username', 'membership-for-woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'membership-for-woocommerce' ); ?></span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'Email address', 'membership-for-woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'membership-for-woocommerce' ); ?></span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Password', 'membership-for-woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'membership-for-woocommerce' ); ?></span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
				</p>

			<?php else : ?>

				<p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'membership-for-woocommerce' ); ?></p>

			<?php endif; ?>
			<?php do_action( 'woocommerce_register_form' ); ?>
			<div class="woocommerce-form-row form-row">
				<div class="g-recaptcha" data-sitekey="<?php echo esc_html( $wps_mfw_site_captcha_key ); ?>"></div>
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="wps_mfw_terms_and_condition" type="checkbox" id="wps_mfw_terms_and_condition" value="forever" required /><span><?php esc_html_e( 'I agree to the ', 'membership-for-woocommerce' ); ?><a href="<?php echo esc_url( site_url() ) . '/privacy-policy' ?>" target="_blank"><?php esc_html_e( 'Terms & Conditions', 'membership-for-woocommerce' ); ?></a></span>
				</label>
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="woocommerce-Button wps_mfw_mem_register_btn woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'membership-for-woocommerce' ); ?>"><?php esc_html_e( 'Register', 'membership-for-woocommerce' ); ?></button>
			</div>
			<?php do_action( 'woocommerce_register_form_end' ); ?>
			<div class="wps_mfw_show_login_option">
				<?php /* translators: %s: login */ printf( esc_html__( 'Already have an account? %s', 'membership-for-woocommerce' ), '<a href="#">' . esc_html__( 'Login', 'membership-for-woocommerce' ) . '</a>' ); ?>
			</div>
		</form>
	</div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
