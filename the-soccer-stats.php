<?php
/**
 * Plugin Name: The Soccer Stats
 * Plugin URI: http://thesoccerstats.wordpress.com
 * Description: Ultimate soccer statistics tool for your team.
 * Version: 1.08
 * Author: Timo Leppänen
 * Author URI: https://wordpress.org/support/profile/lepileppanen
 * License: GPLv2
 */

/*
 * Security Note:
 * Consider blocking direct access to your plugin PHP files by adding the following line at the top of each of them,
 * or be sure to refrain from executing sensitive standalone PHP code before calling any WordPress functions.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Add menu item for plugin.
 *
 * @since 1.0
 */
function tss_add_to_settings_menu() {

	add_menu_page(
		__( 'The Soccer Stats', 'tss' ),
		__( 'The Soccer Stats', 'tss' ),
		'manage_options',
		'the-soccer-stats-admin',
		'tss_admin_main',
		'dashicons-groups', // Icon.
		6
	);

	add_submenu_page(
		'the-soccer-stats-admin', // Parent slug.
		__( 'Settings', 'tss' ), // Page title.
		__( 'Settings', 'tss' ), // Menu title.
		'remove_users', // Capability.
		'tss_settings_page', // Subpage slug.
		'tss_build_settings_page' // Callback.
	);
}
add_action( 'admin_menu', 'tss_add_to_settings_menu' );

require_once( 'classes/tssFilter.php' );
require_once( 'classes/tssPlayerStatsTable.php' );
require_once( 'classes/tssPlayerSeasonalStatsTable.php' );
require_once( 'classes/tssSeasonMatchList.php' );
require_once( 'classes/tssOpponentStats.php' );

require_once( 'settings/settings.php' );
require_once( 'post-types/post-types.php' );
require_once( 'meta-boxes/meta-boxes.php' );
require_once( 'functions/functions.php' );
require_once( 'ajax/ajax-admin.php' );

/**
 *
 * Add styles and css for admin section
 *
 * @since 1.0
 */
function tss_admin_styles() {
	wp_enqueue_style( 'bootstrap-css', plugins_url( 'css/tss-bootstrap.css', __FILE__ ) );
	wp_enqueue_script( 'bootstrap-js', plugins_url( 'plugins/bootstrap/bootstrap.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'admin-scripts', plugins_url( '/js/admin-main.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'tss-select2', plugins_url( '/plugins/select2/select2.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_style( 'admin-styles', plugins_url( '/css/tss-admin.css', __FILE__ ) );
	wp_enqueue_style( 'tss-select2-css', plugins_url( '/plugins/select2/select2.min.css', __FILE__ ) );
	wp_localize_script( 'admin-scripts', 'ajax_object',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'tssNonce' => wp_create_nonce( 'tss-player-seasons-nonce' ),
		)
	); // Setting ajaxurl.
}
add_action( 'admin_enqueue_scripts', 'tss_admin_styles' );

add_action('wp_head','tss_ajaxurl');
function tss_ajaxurl() {
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}


/**
 *
 * Add styles and css
 *
 * @since 1.0
 */
function tss_styles() {

	//Enqueue boostrap css, if not installed and not set to yes..
	$options = get_option( 'tss_options' );
	if( ! isset( $options[ 'bootstrap' ] ) || 'yes' !== $options[ 'bootstrap' ] ) {
		wp_enqueue_style( 'bootstrap-css', plugins_url( 'css/tss-bootstrap.css', __FILE__ ) );
	}

	wp_enqueue_style( 'tss-css', plugins_url( 'css/tss-default.css', __FILE__ ) );
	wp_enqueue_script( 'bootstrap-js', plugins_url( 'plugins/bootstrap/bootstrap.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_style( 'tss-select2-css', plugins_url( '/plugins/select2/select2.min.css', __FILE__ ) );
	wp_enqueue_style( 'tss-fa-css', plugins_url( '/plugins/font-awesome/css/font-awesome.min.css', __FILE__ ) );
	wp_enqueue_script( 'tss-select2', plugins_url( '/plugins/select2/select2.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'tss-tablesorter', plugins_url( '/plugins/tablesorter/jquery.tablesorter.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'tss-mainjs', plugins_url( '/js/main.js', __FILE__ ), array( 'jquery' ) );
	//wp_enqueue_script( 'tss-tablesorter-widgets', plugins_url( '/plugins/tablesorter/jquery.tablesorter.widgets.js', __FILE__ ), array( 'jquery' ) );

}
add_action( 'wp_enqueue_scripts', 'tss_styles' );

/**
 *
 * Form modification in admin
 *
 * @since 1.0
 * @param object $post Current post.
 */
function tss_modify_title_in_player_edit( $post ) {
	if ( 'tss-players' == $post->post_type ) {
		echo '<p>' . esc_html__( 'Add player description to editor.', 'tss' ) . '</p>';
	} elseif ( 'tss-seasons' == $post->post_type ) {
		echo '<p>' . esc_html__( 'Add season name to title.', 'tss' ) . '</p>';
	} elseif ( 'tss-opponents' == $post->post_type ) {
		echo '<p>' . esc_html__( 'Add opponent name to title.', 'tss' ) . '</p>';
	} elseif ( 'tss-matches' == $post->post_type ) {
		echo '<p>' . esc_html__( 'Add match report to editor below.', 'tss' ) . '</p>';
	}

}
add_action( 'edit_form_after_title', 'tss_modify_title_in_player_edit' );

/**
 *
 * Templates in use.
 *
 * @since 1.0
 * @param string $single_template Single template filename.
 */
function tss_get_custom_post_type_template( $single_template ) {
	 global $post;

	 if( 'tss-players' == $post->post_type || 'tss-matches' == $post->post_type || 'tss-seasons' == $post->post_type || 'tss-opponents' == $post->post_type )
		 $single_template = dirname( __FILE__ ) . '/templates/single-' . $post->post_type . '.php';

	 return $single_template;
}
add_filter( 'single_template', 'tss_get_custom_post_type_template' );

/**
 * Checks if custom db tables exists and installs if necessary
 * when plugin is activated
 *
 * @since 1.0
 */
function tss_install_custom_db_tables() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$tables = array( 'tss_goals', 'tss_starters', 'tss_substitutes', 'tss_substitutions', 'tss_redcards', 'tss_yellowcards', 'tss_player_seasons' );

	foreach ( $tables as $table ) {
		$q = "SHOW TABLES LIKE '$table'";
		$results = $wpdb->get_results( $q, OBJECT );

		if ( ! $results ) {
			$q = '';
			if ( 'tss_goals' == $table ) {
				$q = "CREATE TABLE tss_goals (
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          playerid int(10) unsigned NOT NULL DEFAULT '0',
          matchid int(10) unsigned NOT NULL DEFAULT '0',
          minute tinyint(3) unsigned NOT NULL DEFAULT '0',
          own tinyint(1) unsigned NOT NULL DEFAULT '0',
          penalty tinyint(1) unsigned NOT NULL DEFAULT '0',
          ownscorer varchar(64) NOT NULL DEFAULT '',
          PRIMARY KEY  (id),
          KEY playerid (playerid),
          KEY matchid (matchid)
        ) $charset_collate;";
			} elseif ( 'tss_starters' == $table ) {
				$q = "CREATE TABLE tss_starters (
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          playerid int(10) unsigned NOT NULL DEFAULT '0',
          matchid int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY  (id),
          KEY playerid (playerid),
          KEY matchid (matchid)
        ) $charset_collate;";
			} elseif ( 'tss_substitutes' == $table ) {
				$q = "CREATE TABLE tss_substitutes (
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          playerid int(10) unsigned NOT NULL DEFAULT '0',
          matchid int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY  (id),
          KEY playerid (playerid),
          KEY matchid (matchid)
        ) $charset_collate;";
			} elseif ( 'tss_substitutions' == $table ) {
				$q = "CREATE TABLE tss_substitutions (
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          playeridin int(10) unsigned NOT NULL DEFAULT '0',
          playeridout int(10) unsigned NOT NULL DEFAULT '0',
          matchid int(10) unsigned NOT NULL DEFAULT '0',
          minute tinyint(3) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY  (id),
          KEY playeridin (playeridin),
          KEY playeridout (playeridout),
          KEY matchid (matchid)
        ) $charset_collate;";
			} elseif ( 'tss_redcards' == $table ) {
				$q = "CREATE TABLE tss_redcards (
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          playerid int(10) unsigned NOT NULL DEFAULT '0',
          matchid int(10) unsigned NOT NULL DEFAULT '0',
          minute tinyint(3) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY  (id),
          KEY playerid (playerid),
          KEY matchid (matchid)
        ) $charset_collate;";
			} elseif ( 'tss_yellowcards' == $table ) {
				$q = "CREATE TABLE tss_yellowcards (
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          playerid int(10) unsigned NOT NULL DEFAULT '0',
          matchid int(10) unsigned NOT NULL DEFAULT '0',
          minute tinyint(3) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY  (id),
          KEY playerid (playerid),
          KEY matchid (matchid)
        ) $charset_collate;";
			} elseif ( 'tss_player_seasons' == $table ) {
				$q = "CREATE TABLE tss_player_seasons (
          seasonid int(10) unsigned NOT NULL DEFAULT '0',
          playerid int(10) unsigned NOT NULL DEFAULT '0',
          KEY seasonid (seasonid),
          KEY playerid (playerid)
        ) $charset_collate;";
			}

			dbDelta( $q );

		}
	}

}
register_activation_hook( __FILE__, 'tss_install_custom_db_tables' );


/**
 *
 * Localization.
 *
 * @since 1.0
 *
 */
function tss_load_textdomain() {
	load_plugin_textdomain( 'tss', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}
add_action('plugins_loaded', 'tss_load_textdomain');

/**
 *
 * Activation hooks.
 *
 * @since 1.03
 *
 */
function tss_flush_rewrites() {
	tss_seasons_init();
	tss_opponents_init();
	tss_players_init();
	tss_matches_init();
	tss_matchtypes_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tss_flush_rewrites' );

?>
