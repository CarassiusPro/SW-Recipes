<?php
/**
 * Plugin Name: SW Recipes
 * Plugin URI: https://beaverlodgehq.com
 * Description: Recipes Custom Post Type.
 * Version: 1.0
 * Author: Beaverlodge HQ
 * Author URI: https://beaverlodgehq.com
 */
 
if ( ! function_exists('sw_recipe_post_type') ) {

// Register Recipe Post Type
function sw_recipe_post_type() {

	$labels = array(
		'name'                  => _x( 'Recipe', 'Post Type General Name', 'fl-builder' ),
		'singular_name'         => _x( 'Recipe', 'Post Type Singular Name', 'fl-builder' ),
		'menu_name'             => __( 'Recipes', 'fl-builder' ),
		'name_admin_bar'        => __( 'Recipes', 'fl-builder' ),
		'archives'              => __( 'Recipe Archives', 'fl-builder' ),
		'parent_item_colon'     => __( 'Recipe Item:', 'fl-builder' ),
		'all_items'             => __( 'All Recipes', 'fl-builder' ),
		'add_new_item'          => __( 'Add New Recipe', 'fl-builder' ),
		'add_new'               => __( 'Add Recipe', 'fl-builder' ),
		'new_item'              => __( 'New Recipes', 'fl-builder' ),
		'edit_item'             => __( 'Edit Recipe', 'fl-builder' ),
		'update_item'           => __( 'Update Recipe', 'fl-builder' ),
		'view_item'             => __( 'View Recipe', 'fl-builder' ),
		'search_items'          => __( 'Search Recipes', 'fl-builder' ),
		'not_found'             => __( 'Not found', 'fl-builder' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'fl-builder' ),
		'featured_image'        => __( 'Featured Image', 'fl-builder' ),
		'set_featured_image'    => __( 'Set featured image', 'fl-builder' ),
		'remove_featured_image' => __( 'Remove featured image', 'fl-builder' ),
		'use_featured_image'    => __( 'Use as featured image', 'fl-builder' ),
		'insert_into_item'      => __( 'Insert into recipe', 'fl-builder' ),
		'uploaded_to_this_item' => __( 'Uploaded to this recipe', 'fl-builder' ),
		'items_list'            => __( 'Recipes list', 'fl-builder' ),
		'items_list_navigation' => __( 'Recipes list navigation', 'fl-builder' ),
		'filter_items_list'     => __( 'Filter recipes list', 'fl-builder' ),
	);
	$args = array(
		'label'                 => __( 'Recipe', 'fl-builder' ),
		'description'           => __( 'Recipes', 'fl-builder' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-carrot',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'recipes', $args );

}
add_action( 'init', 'sw_recipe_post_type', 0 );

}

if ( ! function_exists( 'sw_recip_taxonomy' ) ) {

// Register Season Taxonomy
function sw_recip_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Seasons', 'Taxonomy General Name', 'fl-builder' ),
		'singular_name'              => _x( 'Season', 'Taxonomy Singular Name', 'fl-builder' ),
		'menu_name'                  => __( 'Season', 'fl-builder' ),
		'all_items'                  => __( 'All Seasons', 'fl-builder' ),
		'parent_item'                => __( 'Parent Season', 'fl-builder' ),
		'parent_item_colon'          => __( 'Parent Season:', 'fl-builder' ),
		'new_item_name'              => __( 'New Season', 'fl-builder' ),
		'add_new_item'               => __( 'Add New Season', 'fl-builder' ),
		'edit_item'                  => __( 'Edit Season', 'fl-builder' ),
		'update_item'                => __( 'Update Season', 'fl-builder' ),
		'view_item'                  => __( 'View Season', 'fl-builder' ),
		'separate_items_with_commas' => __( 'Separate seasons with commas', 'fl-builder' ),
		'add_or_remove_items'        => __( 'Add or remove seasons', 'fl-builder' ),
		'choose_from_most_used'      => __( 'Choose season', 'fl-builder' ),
		'popular_items'              => __( 'Popular Seasons', 'fl-builder' ),
		'search_items'               => __( 'Search Seasons', 'fl-builder' ),
		'not_found'                  => __( 'Not Found', 'fl-builder' ),
		'no_terms'                   => __( 'No seasons', 'fl-builder' ),
		'items_list'                 => __( 'Seasons list', 'fl-builder' ),
		'items_list_navigation'      => __( 'Seasons list navigation', 'fl-builder' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'season', array( 'recipes' ), $args );

}
add_action( 'init', 'sw_recip_taxonomy', 0 );

}

// Adds recipe metaboxes
class Recipe_Meta_Box {

	public function __construct() {

		if ( is_admin() ) {
			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}

	}

	public function init_metabox() {

		add_action( 'add_meta_boxes',        array( $this, 'add_metabox' )         );
		add_action( 'save_post',             array( $this, 'save_metabox' ), 10, 2 );

	}

	public function add_metabox() {

		add_meta_box(
			'recipe_steps',
			__( 'Recipe Steps', 'fl-builder' ),
			array( $this, 'render_metabox' ),
			'recipes',
			'advanced',
			'high'
		);

	}

	public function render_metabox( $post ) {

		// Retrieve an existing value from the database.
		$sw_ingredients = get_post_meta( $post->ID, 'sw_ingredients', true );
		$sw_instructions = get_post_meta( $post->ID, 'sw_instructions', true );
		$sw_resources = get_post_meta( $post->ID, 'sw_resources', true );

		// Set default values.
		if( empty( $sw_ingredients ) ) $sw_ingredients = '';
		if( empty( $sw_instructions ) ) $sw_instructions = '';
		if( empty( $sw_resources ) ) $sw_resources = '';

		// Form fields.
		echo '<table class="form-table">';

		echo '	<tr>';
		echo '		<th><label for="sw_ingredients" class="sw_ingredients_label">' . __( 'Ingredients', 'fl-builder' ) . '</label></th>';
		echo '		<td>';
		wp_editor( $sw_ingredients, 'sw_ingredients', array( 'media_buttons' => true ) );
		echo '		</td>';
		echo '	</tr>';

		echo '	<tr>';
		echo '		<th><label for="sw_instructions" class="sw_instructions_label">' . __( 'Instructions', 'fl-builder' ) . '</label></th>';
		echo '		<td>';
		wp_editor( $sw_instructions, 'sw_instructions', array( 'media_buttons' => true ) );
		echo '		</td>';
		echo '	</tr>';

		echo '	<tr>';
		echo '		<th><label for="sw_resources" class="sw_resources_label">' . __( 'Resources/Tips', 'fl-builder' ) . '</label></th>';
		echo '		<td>';
		wp_editor( $sw_resources, 'sw_resources', array( 'media_buttons' => true ) );
		echo '		</td>';
		echo '	</tr>';

		echo '</table>';

	}

	public function save_metabox( $post_id, $post ) {

		// Sanitize user input.
		$sw_new_ingredients = isset( $_POST[ 'sw_ingredients' ] ) ? wp_kses_post( $_POST[ 'sw_ingredients' ] ) : '';
		$sw_new_instructions = isset( $_POST[ 'sw_instructions' ] ) ? wp_kses_post( $_POST[ 'sw_instructions' ] ) : '';
		$sw_new_resources = isset( $_POST[ 'sw_resources' ] ) ? wp_kses_post( $_POST[ 'sw_resources' ] ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, 'sw_ingredients', $sw_new_ingredients );
		update_post_meta( $post_id, 'sw_instructions', $sw_new_instructions );
		update_post_meta( $post_id, 'sw_resources', $sw_new_resources );

	}

}

new Recipe_Meta_Box;

// Add Ingredients Shortcode
function sw_ingredients_shortcode() {

	echo get_post_meta( $post->ID, 'sw_ingredients', true );

}
add_shortcode( 'ingredients', 'sw_ingredients_shortcode' );

// Add Instructions Shortcode
function sw_instructions_shortcode() {

	echo get_post_meta( $post->ID, 'sw_instructions', true );

}
add_shortcode( 'instructions', 'sw_instructions_shortcode' );

// Add Resources Shortcode
function sw_resources_shortcode() {

	echo get_post_meta( $post->ID, 'sw_resources', true );

}
add_shortcode( 'resources', 'sw_resources_shortcode' );