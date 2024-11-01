<?php
/**
 * Settings
 *
 * Settings registration and callbacks.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

/**
 * Register settings.
 *
 * @since 1.0
 */
function tss_register_settings() {
	register_setting( 'tss_options', 'tss_options' );
}
add_action( 'admin_init', 'tss_register_settings' );

/**
 * Wordpress related menu items settings explanation.
 *
 * @since 1.0
 */
function tss_echo_settings_info() {
	echo '<p>' . esc_html__( 'Settings for The Soccer Stats', 'tss' ) . '</p>';
}

/**
 * Creation the settings form.
 *
 * @since 1.0
 *
 */
function tss_build_settings_page() {

	echo '<form action="options.php" method="post">';
	settings_fields( 'tss_options' );
	add_settings_section( 'tss_main_settings', esc_html__( 'The Soccer Stats options', 'tss' ), 'tss_echo_settings_info', 'tss_settings_page' );
	add_settings_field( 'tss_my_team_name_field', esc_html__( 'My team?', 'tss' ), 'tss_settings_my_team_callback', 'tss_settings_page', 'tss_main_settings' );
	add_settings_field( 'tss_container_field', esc_html__( 'How to show the content in site?', 'tss' ), 'tss_settings_container_callback', 'tss_settings_page', 'tss_main_settings' );
	add_settings_field( 'tss_bootstrap_field', esc_html__( 'Do you have Bootstrap installed to your theme?', 'tss' ), 'tss_settings_bootstrap_callback', 'tss_settings_page', 'tss_main_settings' );
	add_settings_field( 'tss_date_field', esc_html__( 'Date settings?', 'tss' ), 'tss_settings_date_callback', 'tss_settings_page', 'tss_main_settings' );
	add_settings_field( 'tss_installation', esc_html__( 'The Soccer Stats installation', 'tss' ), 'tss_settings_installation_callback', 'tss_settings_page', 'tss_main_settings' );
	add_settings_field( 'tss_rebuild_matches_titles', esc_html__( 'Rebuild match titles', 'tss' ), 'tss_settings_rebuild_matches_callback', 'tss_settings_page', 'tss_main_settings' );

	do_settings_sections( 'tss_settings_page' );
	submit_button();

	echo '</form>';
}

/**
 * Creation the settings form, my team text input callback
 *
 * @since 1.0
 *
 */
function tss_settings_my_team_callback() {
	$options = get_option( 'tss_options' );
	$teams = get_posts( array( 'post_type' => 'tss-opponents', 'numberposts' => '-1' ) );
	?>
	<select id='tss_my_team_name_field' name='tss_options[my_team]'>
		<?php
		foreach ( $teams as $team ) {
			$selected = '';

			if ( $team->ID == $options['my_team'] ) {
				$selected = ' SELECTED';
			}
			echo "<option value=\"$team->ID\"$selected>$team->post_title</option>";
		}
		?>
	</select>
	<?php
}

/**
 * Creation the settings form, container
 *
 * @since 1.0
 *
 */
function tss_settings_container_callback() {
	$options = get_option( 'tss_options' );

	$selectvalues = array(
		array( 'value' => 'container', 'text' => __( 'Use container - content is fixed based on window width' , 'tss') ),
		array( 'value' => 'container-fluid', 'text' => __( 'Whole width', 'tss' ) )
	);
	?>
	<select id='tss_container_field' name='tss_options[container]'>
		<?php
		if( ! isset( $options['container'] ) ) {
			$options['container'] = 'container';
		}

		foreach ( $selectvalues as $selectvalue ) {
			$selected = '';

			if ( $selectvalue[ 'value' ] == $options['container'] ) {
				$selected = ' SELECTED';
			}
			echo '<option value="' . $selectvalue['value'] . '"' . $selected . '>' . $selectvalue['text'] . '</option>';
		}
		?>
	</select>
	<?php
}

/**
 * Creation the settings form, bootsrap installed? yes/no
 *
 * @since 1.0
 *
 */
function tss_settings_bootstrap_callback() {
	$options = get_option( 'tss_options' );

	$selectvalues = array(
		array( 'value' => 'yes', 'text' => __( 'Yes' , 'tss') ),
		array( 'value' => 'no', 'text' => __( 'No', 'tss' ) ),
		array( 'value' => 'dontknow', 'text' => __( 'No idea what is Bootstrap..', 'tss' ) )
	);
	?>
	<select id='tss_bootstrap_field' name='tss_options[bootstrap]'>
		<?php
		if( ! isset( $options['bootstrap'] ) ) {
			$options['bootstrap'] = 'no';
		}

		foreach ( $selectvalues as $selectvalue ) {
			$selected = '';

			if ( $selectvalue[ 'value' ] == $options['bootstrap'] ) {
				$selected = ' SELECTED';
			}
			echo '<option value="' . $selectvalue['value'] . '"' . $selected . '>' . $selectvalue['text'] . '</option>';
		}
		?>
	</select>
	<?php
}

/**
 * Creation the settings form, date callback
 *
 * @since 1.0
 *
 */
function tss_settings_date_callback() {
	echo __( 'The Soccer Stats uses the date settings defined under main Wordpress settings.', 'tss' );
}

/**
 * Creation the settings form, installation callback
 *
 * @since 1.0
 *
 */
function tss_settings_installation_callback() {
	if ( tss_check_custom_db_tables() == false ) {
		echo __( 'There is something wrong with the database. Reactivate the plugin.', 'tss' );
	} else {
		echo __( 'The Soccer Stats related database tables are found. Proceed with the plugin... :)', 'tss' );
	}
}

/**
 * Creation the settings form, rebuild matches titles
 *
 * @since 1.02
 *
 */
function tss_settings_rebuild_matches_callback() {
	?>
	<?php echo esc_html__( 'Modify all match titles like this: date vs. opponent name', 'tss' ) ?> <span class="btn left" id="tss-rebuild-match-titles"><?php echo  esc_html__( 'Rebuild', 'tss' ); ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span>
	<?php
}

?>
