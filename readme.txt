=== CFS Options Screens ===
Contributors: jchristopher
Donate link: http://mondaybynoon.com/donate/
Tags: CFS, Custom Field Suite, Options, Settings, Screen
Requires at least: 3.9
Tested up to: 4.6.1
Stable tag: 1.2.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create options screens that utilize Custom Field Suite

== Description ==

Build any number of options screens based on [Custom Field Suite](http://wordpress.org/plugins/custom-field-suite/).

= For Example =

Begin by creating Field Group(s) you want to include on your options screen. **Be sure to set NO Placement Rules.** Once it's created, note the post ID it uses. You can then register any number of options screens like so:

`function my_cfs_options_screens( $screens ) {
    $screens[] = array(
        'name'            => 'options',
        'menu_title'      => __( 'Site Options' ),
        'page_title'      => __( 'Customize Site Options' ),
        'menu_position'   => 100,
        'icon'            => 'dashicons-admin-generic', // optional, dashicons-admin-generic is the default
        'field_groups'    => array( 'My Field Group' ), // Field Group name(s) of CFS Field Group to use on this page (can also be post IDs)
    );

    return $screens;
}

add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );`

= Retrieve your options like so: =

`$value = cfs_get_option( 'options_screen_name', 'cfs_field_name_from_field_group' );`

You can set up multiple top level and/or children options pages by adding a `parent` argument when registering your screen:

`function my_cfs_options_screens( $screens ) {

    // Parent
    $screens[] = array(
        'name'         => 'options',
        'field_groups' => array( 'My Parent Field Group Name' ),
    );

    // Child
    $screens[] = array(
        'name'         => 'options-nav',
        'parent'       => 'options', // name of the parent
        'field_groups' => array( 'My Child Field Group Name' ),
    );

    return $screens;
 }

 add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );`

 You can also use CFS Options Screens to set up Field Group 'defaults', allowing a Field Group to appear both on a CFS Options Screen and a post edit screen. The CFS Options Screen will act as the default/fallback and the post edit screen will override those defaults.

`function my_cfs_options_screens( $screens ) {
    $screens[] = array(
        'name'            => 'options',
        'menu_title'      => __( 'Site Options' ),
        'page_title'      => __( 'Customize Site Options' ),
        'menu_position'   => 100,
        'icon'            => 'dashicons-admin-generic', // optional, dashicons-admin-generic is the default
        'field_groups'    => array(
            array(
                'title'         => 'My CFS Field Group Name',
                'has_overrides' => true,
            ),
        ),
    );

    return $screens;
}

add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );`

Check out the `cfs_options_screens_override_note_default` and `cfs_options_screens_override_note_override` filters to customize the messaging for CFS Options Screens overrides.

== Installation ==

1. Upload `cfs-options-screens` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Register your options screen(s) using the code snippets from this readme

== Frequently Asked Questions ==

= How do I add a Field Group to my options screen? =

You must specify the Field Group Title(s) in the `field_groups` parameter when using the `cfs_options_screens` hook

= How do I retrieve saved options? =

`$value = cfs_get_option( 'options_screen_name', 'field_name_from_field_group' );`

== Changelog ==

= 1.2.7 =
* Add support for using CFS Field Group title instead of ID

= 1.2.5 =
* Better handling of overrides when not viewing a single post

= 1.2.4 =
* PHP Warning cleanup

= 1.2.3 =
* Fixed an issue that would output override note if any Field Group on an Options Screen had one
* Fixed an issue where multiple override notes would be output when there were multiple override Field Groups

= 1.2.1 =
* PHP Warning cleanup for `cfs_get_option`

= 1.2 =
* Added support for Field Group defaults/overrides where a Field Group can appear both on a CFS Options Screen and a post edit screen, and 'fall back' to the CFS Options Screen where applicable

= 1.1.2 =
* Refined the arguments for the underlying CPT to hide it from the Admin menu, filterable with `cfs_options_screens_post_type_args`

= 1.1.1 =
* Fixed an issue resulting in a change in WordPress 4.4 that prevented editing options screens

= 1.1 =
* Added new `cfs_get_options()` function to retrieve all CFS data for an options screen

= 1.0.3 =
* Fixed an issue in WordPress 4.3 where customized edit screen titles were not shown

= 1.0.2 =
* Only show 'Saved' update notice when editing an options screen

= 1.0.1 =
* Proper page title is now output when editing a screen

= 1.0 =
* Initial release
