=== CFS Options Screens ===
Contributors: jchristopher
Donate link: http://mondaybynoon.com/donate/
Tags: CFS, Custom Field Suite, Options, Settings, Screen
Requires at least: 3.9
Tested up to: 3.9.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create options screens that utilize Custom Field Suite

== Description ==

Build any number of options screens based on [http://wordpress.org/plugins/custom-field-suite/](Custom Field Suite).

= For Example =

Many times you may use something like the following to build your main site navigation:

`function my_cfs_options_screens( $screens ) {
	$screens[] = array(
		'name'            => 'options',
		'menu_title'      => __( 'Site Options' ),
		'page_title'      => __( 'Customize Site Options' ),
		'menu_position'   => 100,
		'icon'            => 'dashicons-admin-generic', // optional, dashicons-admin-generic is the default
		'field_groups'    => array( 75 ), // post ID(s) of CFS Field Group to use on this page
	);
	return $screens;
}
add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );`

== Installation ==


1. Upload `cfs-options-screens` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Register your options screen(s) using the code snippets from this readme

== Frequently Asked Questions ==

= How do I add a Field Group to my options screen? =

You must specify the Field Group ID(s) in the `field_groups` parameter when using the `cfs_options_screens` hook

== Changelog ==

= 1.0 =
* Initial release
