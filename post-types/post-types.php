<?php
/**
 * Post types registration
 *
 * Post types are registered in this file.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

add_action( 'init', 'tss_seasons_init' );

/**
 *
 * Add seasons posttype.
 *
 * @since 1.0
 */
function tss_seasons_init() {
	$labels = array(
		'name'               => __( 'Seasons', 'tss' ),
		'singular_name'      => __( 'Season', 'tss' ),
		'menu_name'          => __( 'Seasons', 'tss' ),
		'name_admin_bar'     => __( 'Season', 'tss' ),
		'add_new'            => __( 'Add New', 'tss' ),
		'add_new_item'       => __( 'Add New Season', 'tss' ),
		'new_item'           => __( 'New Season', 'tss' ),
		'edit_item'          => __( 'Edit Season', 'tss' ),
		'view_item'          => __( 'View Season', 'tss' ),
		'all_items'          => __( 'All Seasons', 'tss' ),
		'search_items'       => __( 'Search Seasons', 'tss' ),
		'parent_item_colon'  => __( 'Parent Seasons:', 'tss' ),
		'not_found'          => __( 'No Seasons found.', 'tss' ),
		'not_found_in_trash' => __( 'No Seasons found in Trash.', 'tss' ),
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'The Soccer Stats seasons.', 'tss' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => 'the-soccer-stats-admin',
		'query_var'          => true,
		'rewrite'            => array( 'slug' => __( 'season', 'tss' ) ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title' ),
	);

	register_post_type( 'tss-seasons', $args );
}

add_action( 'init', 'tss_opponents_init' );

/**
 *
 * Add opponents posttype.
 *
 * @since 1.0
 */
function tss_opponents_init() {
	$labels = array(
		'name'               => __( 'Opponents', 'tss' ),
		'singular_name'      => __( 'Opponent', 'tss' ),
		'menu_name'          => __( 'Opponents', 'tss' ),
		'name_admin_bar'     => __( 'Opponent', 'tss' ),
		'add_new'            => __( 'Add New', 'tss' ),
		'add_new_item'       => __( 'Add New Opponent', 'tss' ),
		'new_item'           => __( 'New Opponent', 'tss' ),
		'edit_item'          => __( 'Edit Opponent', 'tss' ),
		'view_item'          => __( 'View Opponent', 'tss' ),
		'all_items'          => __( 'All Opponents', 'tss' ),
		'search_items'       => __( 'Search Opponents', 'tss' ),
		'parent_item_colon'  => __( 'Parent Opponents:', 'tss' ),
		'not_found'          => __( 'No Opponents found.', 'tss' ),
		'not_found_in_trash' => __( 'No Opponents found in Trash.', 'tss' ),
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'The Soccer Stats opponents.', 'tss' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => 'the-soccer-stats-admin',
		'query_var'          => true,
		'rewrite'            => array( 'slug' => __( 'opponent', 'tss' ) ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'thumbnail' ),
	);

	register_post_type( 'tss-opponents', $args );
}

add_action( 'init', 'tss_players_init' );

/**
 *
 * Add players posttype.
 *
 * @since 1.0
 */
function tss_players_init() {
	$labels = array(
		'name'               => __( 'Players', 'tss' ),
		'singular_name'      => __( 'Player', 'tss' ),
		'menu_name'          => __( 'Players', 'tss' ),
		'name_admin_bar'     => __( 'Player', 'tss' ),
		'add_new'            => __( 'Add New', 'tss' ),
		'add_new_item'       => __( 'Add New Player', 'tss' ),
		'new_item'           => __( 'New Player', 'tss' ),
		'edit_item'          => __( 'Edit Player', 'tss' ),
		'view_item'          => __( 'View Player', 'tss' ),
		'all_items'          => __( 'All Players', 'tss' ),
		'search_items'       => __( 'Search Players', 'tss' ),
		'parent_item_colon'  => __( 'Parent Players:', 'tss' ),
		'not_found'          => __( 'No Players found.', 'tss' ),
		'not_found_in_trash' => __( 'No Players found in Trash.', 'tss' ),
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'The Soccer Stats players.', 'tss' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => 'the-soccer-stats-admin',
		'query_var'          => true,
		'rewrite'            => array( 'slug' => __( 'player', 'tss' ) ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail' ),
	);

	register_post_type( 'tss-players', $args );
}

add_action( 'init', 'tss_matches_init' );

/**
 * Add matches posttype.
 *
 * @since 1.0
 */
function tss_matches_init() {
	$labels = array(
		'name'               => __( 'Matches', 'tss' ),
		'singular_name'      => __( 'Match', 'tss' ),
		'menu_name'          => __( 'Matches', 'tss' ),
		'name_admin_bar'     => __( 'Match', 'tss' ),
		'add_new'            => __( 'Add New', 'tss' ),
		'add_new_item'       => __( 'Add New Match', 'tss' ),
		'new_item'           => __( 'New Match', 'tss' ),
		'edit_item'          => __( 'Edit Match', 'tss' ),
		'view_item'          => __( 'View Match', 'tss' ),
		'all_items'          => __( 'All Matches', 'tss' ),
		'search_items'       => __( 'Search Matches', 'tss' ),
		'parent_item_colon'  => __( 'Parent Matches:', 'tss' ),
		'not_found'          => __( 'No matches found.', 'tss' ),
		'not_found_in_trash' => __( 'No matches found in Trash.', 'tss' ),
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'The Soccer Stats Matches.', 'tss' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => 'the-soccer-stats-admin',
		'query_var'          => true,
		'rewrite'            => array( 'slug' => __( 'match', 'tss' ) ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor' ),
	);

	register_post_type( 'tss-matches', $args );
}

add_action( 'init', 'tss_matchtypes_init' );

/**
 * Add matchtypes posttype.
 *
 * @since 1.0
 */
function tss_matchtypes_init() {
	$labels = array(
		'name'               => __( 'Match types', 'tss' ),
		'singular_name'      => __( 'Match type', 'tss' ),
		'menu_name'          => __( 'Match types', 'tss' ),
		'name_admin_bar'     => __( 'Match type', 'tss' ),
		'add_new'            => __( 'Add New', 'tss' ),
		'add_new_item'       => __( 'Add New Match Type', 'tss' ),
		'new_item'           => __( 'New Match Type', 'tss' ),
		'edit_item'          => __( 'Edit Match Type', 'tss' ),
		'view_item'          => __( 'View Match Type', 'tss' ),
		'all_items'          => __( 'All Match Types', 'tss' ),
		'search_items'       => __( 'Search Match Types', 'tss' ),
		'parent_item_colon'  => __( 'Parent Match Types:', 'tss' ),
		'not_found'          => __( 'No Match Types found.', 'tss' ),
		'not_found_in_trash' => __( 'No Match Types found in Trash.', 'tss' ),
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'The Soccer Stats match types.', 'tss' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => 'the-soccer-stats-admin',
		'query_var'          => true,
		'rewrite'            => array( 'slug' => __( 'matchtype', 'tss' ) ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title' ),
	);

	register_post_type( 'tss-matchtypes', $args );
}
?>
