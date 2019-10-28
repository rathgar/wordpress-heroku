<?php
/**
 * WooCommerce MailChimp Admin notices
 *
 * @package   WooCommerce MailChimp
 * @author    Saint Systems, LLC
 * @link      http://www.saintsystems.com
 * @copyright Copyright 2019, Saint Systems, LLC
 *
 * @since 2.0.13
 */

/**
 * Handle displaying and storing of admin notices for WooCommerce MailChimp
 * @since 2.0.13
 */
class SS_WC_MailChimp_Admin_Notices {

	static private $admin_notices = array();

	static private $dismissed_notices = array();

	function __construct() {

		$this->add_hooks();
	}

	function add_hooks() {
		add_action( 'network_admin_notices', array( $this, 'dismiss_notice' ), 50 );
		add_action( 'admin_notices', array( $this, 'dismiss_notice' ), 50 );
		add_action( 'admin_notices', array( $this, 'admin_notice' ), 100 );
		add_action( 'network_admin_notices', array( $this, 'admin_notice' ), 100 );
	}

	/**
	 * Dismiss a WooCommerce notice - stores the dismissed notices for 30 days
	 * @return void
	 */
	public function dismiss_notice() {

		// No dismiss sent
		if ( empty( $_GET['sswcmc-dismiss'] ) ) {
			return;
		}

		// Invalid nonce
		if ( !wp_verify_nonce( $_GET['sswcmc-dismiss'], 'dismiss' ) ) {
			return;
		}

		$notice_id = esc_attr( $_GET['notice'] );

		//don't display a message if use has dismissed the message for this version
		$dismissed_notices = (array)get_transient( 'ss_wc_mailchimp_dismissed_notices' );

		$dismissed_notices[] = $notice_id;

		$dismissed_notices = array_unique( $dismissed_notices );

		// Remind users every 30 days
		set_transient( 'ss_wc_mailchimp_dismissed_notices', $dismissed_notices, DAY_IN_SECONDS * 30 );

	}

	/**
	 * Should the notice be shown in the admin (Has it been dismissed already)?
	 *
	 * If the passed notice array has a `dismiss` key, the notice is dismissable. If it's dismissable,
	 * we check against other notices that have already been dismissed.
	 *
	 * @see GravityView_Admin::dismiss_notice()
	 * @see GravityView_Admin::add_notice()
	 * @param  string $notice            Notice array, set using `add_notice()`.
	 * @return boolean                   True: show notice; False: hide notice
	 */
	function _maybe_show_notice( $notice ) {

		// There are no dismissed notices.
		if( empty( self::$dismissed_notices ) ) {
			return true;
		}

		// Has the
		$is_dismissed = !empty( $notice['dismiss'] ) && in_array( $notice['dismiss'], self::$dismissed_notices );

		return $is_dismissed ? false : true;
	}

	/**
	 * Get admin notices
	 * @since 1.12
	 * @return array
	 */
	public static function get_notices() {
		return self::$admin_notices;
	}

	/**
	 * Handle whether to display notices in Multisite based on plugin activation status
	 *
	 * @since 2.0.13
	 *
	 * @return bool True: show the notices; false: don't show
	 */
	private function check_show_multisite_notices() {

		if ( ! is_multisite() ) {
			return true;
		}

		// It's network activated but the user can't manage network plugins; they can't do anything about it.
		if ( SS_WC_MailChimp_Plugin::is_network_activated() && ! is_main_site() ) {
			return false;
		}

		// or they don't have admin capabilities
		if ( ! is_super_admin() ) {
			return false;
		}

		return true;
	}

	/**
	 * Outputs the admin notices generated by the plugin
	 *
	 * @since 2.0.13
	 *
	 * @return void
	 */
	public function admin_notice() {

		/**
		 * Modify the notices displayed
		 * @since 2.0.13
		 */
		$notices = apply_filters( 'ss_wc_mailchimp/admin/notices', self::$admin_notices );

		if( empty( $notices ) || ! $this->check_show_multisite_notices() ) {
			return;
		}

		//don't display a message if use has dismissed the message for this version
		self::$dismissed_notices = isset( $_GET['show-dismissed-notices'] ) ? array() : (array)get_transient( 'ss_wc_mailchimp_dismissed_notices' );

		foreach( $notices as $notice ) {

			if( false === $this->_maybe_show_notice( $notice ) ) {
				continue;
			}

			echo '<div id="message" class="notice '. sswcmc_sanitize_html_class( $notice['class'] ).'">';

			if( !empty( $notice['title'] ) ) {
				echo '<h3>'.esc_html( $notice['title'] ) .'</h3>';
			}

			echo wpautop( $notice['message'] );

			if( !empty( $notice['dismiss'] ) ) {

				$dismiss = esc_attr($notice['dismiss']);

				$url = esc_url( add_query_arg( array( 'sswcmc-dismiss' => wp_create_nonce( 'dismiss' ), 'notice' => $dismiss ) ) );

				echo wpautop( '<a href="'.$url.'" data-notice="'.$dismiss.'" class="button-small button button-secondary">'.esc_html__( 'Dismiss', 'woocommerce-mailchimp' ).'</a>' );
			}

			echo '<div class="clear"></div>';
			echo '</div>';

		}

		//reset the notices handler
		self::$admin_notices = array();
	}

	/**
	 * Add a notice to be displayed in the admin.
	 * @param array $notice Array with `class` and `message` keys. The message is not escaped.
	 */
	public static function add_notice( $notice = array() ) {

		if( !isset( $notice['message'] ) ) {
			do_action( 'ss_wc_mailchimp_log_error', 'SSWCMC_Admin[add_notice] Notice not set', $notice );
			return;
		}

		$notice['class'] = empty( $notice['class'] ) ? 'error' : $notice['class'];

		self::$admin_notices[] = $notice;
	}
}