<?php
/*
Plugin Name: CFS Options Screens
Plugin URI: http://wordpress.org/plugins/cfs-options-screens/
Description: Register options screens powered by Custom Field Suite
Version: 1.0
Author: Jonathan Christopher
Author URI: http://mondaybynoon.com/
Text Domain: cfsos

Copyright 2014 Jonathan Christopher

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CFS_Options_Screens {

	/**
	 * @var CFS_Options_Screens Singleton
	 */
	private static $instance;

	/**
	 * @var array Options screens to create and utilize
	 */
	public $screens = array();

	/**
	 * @var string Post Type that powers options screens
	 */
	public $post_type = 'options';

	/**
	 * @var string Meta key used to store options screen name
	 */
	public $meta_key = '_cfs_options_screen_name';

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'cfs_matching_groups', array( $this, 'cfs_rule_override' ), 10, 3 );
	}

	/**
	 * Singleton
	 *
	 * @return CFS_Options_Screens
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CFS_Options_Screens ) ) {
			self::$instance = new CFS_Options_Screens;
		}
		return self::$instance;
	}

	/**
	 * Initialize everything
	 */
	function init() {

		// hide the 'Edit Post' title, 'Add New' button, and 'updated' notification when editing all options screens
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

		// reinstate the 'updated' notification
		add_action( 'admin_notices', array( $this, 'maybe_updated_notice' ) );

		// let developers customize the post type used
		$this->post_type = apply_filters( 'cfs_options_screens_post_type', $this->post_type );

		// register our custom post type
		$this->register_cpt();

		// allow registration of options screens
		$this->screens = apply_filters( 'cfs_options_screens', $this->screens );

		// make sure our posts exist
		$this->init_screens();

		// add menus
		add_action( 'admin_menu', array( $this, 'maybe_add_menus' ) );
	}

	/**
	 * Maybe enqueue our stylesheet
	 * @param $hook
	 */
	function assets( $hook ) {
		global $post;

		if( 'post.php' == $hook && $this->post_type == $post->post_type ) {
			wp_enqueue_style( 'cfs-options-screen', plugin_dir_url( __FILE__ ) . 'style.css' );
		}

		return;
	}

	/**
	 * Since the 'updated' message references saving a post and adding a new one we hid it and this adds our own
	 */
	function maybe_updated_notice() {
		if ( isset( $_GET['message'] ) ) {
			?>
				<div class="updated"><p><?php _e( 'Saved', '' ); ?></p></div>
			<?php
		}
	}

	/**
	 * Retrieves post IDs for all screens, creates new posts in CPT if nonexistent
	 */
	function init_screens() {
		if ( ! empty( $this->screens ) ) {
			foreach( $this->screens as $screen_key => $screen_meta ) {
				$this->screens[ $screen_key ]['name']           = isset( $screen_meta['name'] )          ? $screen_meta['name'] : 'options';
				$this->screens[ $screen_key ]['page_title']     = isset( $screen_meta['page_title'] )    ? $screen_meta['page_title'] : ucfirst( (string) $this->screens[ $screen_key ]['name'] );
				$this->screens[ $screen_key ]['menu_title']     = isset( $screen_meta['menu_title'] )    ? $screen_meta['menu_title'] : ucfirst( (string) $this->screens[ $screen_key ]['name'] );
				$this->screens[ $screen_key ]['menu_icon']      = isset( $screen_meta['icon'] )          ? $screen_meta['icon'] : 'dashicons-admin-generic';
				$this->screens[ $screen_key ]['menu_position']  = isset( $screen_meta['menu_position'] ) ? $screen_meta['menu_position'] : 100;
				$this->screens[ $screen_key ]['field_groups']   = isset( $screen_meta['field_groups'] )  ? $screen_meta['field_groups'] : array();

				$this->screens[ $screen_key ]['capability']     = apply_filters( 'cfs_options_screens_capability', 'manage_options', $screen_meta );

				// check to see if the post for this screen exists
				$screen = get_page_by_title( $this->screens[ $screen_key ]['name'], 'OBJECT', $this->post_type );

				if ( empty( $screen ) ) {
					// post doesn't exist, create and flag it
					$this->screens[ $screen_key ]['id'] = wp_insert_post( array( 'post_title' => $this->screens[ $screen_key ]['name'], 'post_type' => $this->post_type ) );
				} else {
					$this->screens[ $screen_key ]['id'] = absint( $screen->ID );
				}
			}
		}
	}

	/**
	 * Registers the CPT that powers everything
	 */
	function register_cpt() {
		$args = array(
			'label'         => __( 'CFS Options Screen', '' ),
			'public'        => false,
			'show_ui'       => false,
			'query_var'     => false,
			'rewrite'       => false,
			'supports'      => false,
		);
		register_post_type( $this->post_type, $args );
	}

	/**
	 * Add applicable Admin menus
	 */
	function maybe_add_menus() {
		// screens were registered during init so the ID is already prepped and the post exists
		if ( ! empty( $this->screens ) ) {
			foreach( $this->screens as $screen ) {
				$edit_link = 'post.php?post=' . absint( $screen['id'] ) . '&action=edit';

				// if this screen doesn't have a parent, it IS a parent
				if ( empty( $screen['parent'] ) ) {
					add_menu_page( $screen['page_title'], $screen['menu_title'], $screen['capability'], $edit_link, '', $screen['menu_icon'], $screen['menu_position'] );
				} else {
					// it's a sub-menu, so add it to the parent
					$parent = (string) $screen['parent'];
					foreach( $this->screens as $maybe_parent_screen ) {
						if ( $parent == $maybe_parent_screen['name'] ) {
							$parent_slug = 'post.php?post=' . absint( $maybe_parent_screen['id'] ) . '&action=edit';
							add_submenu_page( $parent_slug, $screen['page_title'], $screen['menu_title'], $screen['capability'], $edit_link, '' );
							break;
						}
					}
				}
			}
		}
	}


	/**
	 * Custom Field Suite doesn't support single post IDs for placement rules, so we're going to inject our own.
	 *
	 * @param $matches
	 * @param $params
	 * @param $rule_types
	 *
	 * @return mixed
	 */
	function cfs_rule_override( $matches, $params, $rule_types ) {

		if ( is_array( $params ) || ! is_numeric ( $params ) ) {
			return $matches;
		}

		$options_screen = false;
		$post_id = absint( $params );

		// we need to validate that this post ID is actually a registered options screen
		if ( ! empty( $this->screens ) ) {
			foreach( $this->screens as $screen_key => $screen_meta ) {
				if ( isset( $screen_meta['id'] ) && $post_id == $screen_meta['id'] ) {
					$options_screen = $screen_meta;
					break;
				}
			}
		}

		if( $options_screen && is_array( $matches ) && ! empty( $matches ) ) {
			// we need to strip out the Field Groups that are not registered with this options screen
			foreach( $matches as $match_key => $match_title ) {
				if ( ! in_array( $match_key, $options_screen['field_groups'] ) ) {
					unset( $matches[ $match_key ] );
				}
			}
		} else {
			// we need to strip out all Field Groups related to Options Screens else they'll show up where we don't want them
			$options_screens_field_groups = array();
			foreach( $this->screens as $screen_meta ) {
				$options_screens_field_groups = array_merge( $options_screens_field_groups, $screen_meta['field_groups'] );
			}
			foreach( $matches as $match_key => $match_title ) {
				if ( in_array( $match_key, $options_screens_field_groups ) ) {
					unset( $matches[ $match_key ] );
				}
			}
		}

		return $matches;
	}

}

/**
 * Retrieve an option from a settings screen
 * Usage: $value = cfs_get_option( 'options', 'my_field' );
 *
 * @param string $screen The options screen name
 * @param string $field  The field name
 *
 * @return bool|mixed The field value as returned by the CFS API
 */
if ( ! function_exists( 'cfs_get_option' ) ) {
	function cfs_get_option( $screen = 'options', $field = '' ) {
		$value = false;

		if ( ! function_exists( 'CFS' ) ) {
			return false;
		}

		$cfs_options_screens = CFS_Options_Screens::instance();

		if ( ! empty( $cfs_options_screens->screens ) ) {
			foreach( $cfs_options_screens->screens as $screen_meta ) {
				if ( $screen == $screen_meta['name'] ) {
					$value = CFS()->get( $field, $screen_meta['id'] );
				}
			}
		}

		return $value;
	}
}

/**
 * Initializer
 *
 * @return CFS_Options_Screens
 */
if ( ! function_exists( 'cfs_options_screens_init' ) ) {
	function cfs_options_screens_init() {
		$cfs_options_screens = CFS_Options_Screens::instance();
		return $cfs_options_screens;
	}
}

// kickoff
cfs_options_screens_init();
