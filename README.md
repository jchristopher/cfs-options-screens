This is a WordPress plugin, [Official download available on WordPress.org](http://wordpress.org/plugins/cfs-options-screens/)

# Custom Field Suite Options Screens

Create any number of options screens powered by [Custom Field Suite](http://customfieldsuite.com)

## Documentation

Begin by creating your Field Group(s) as you normally would. Be sure to set **NO Placement Rules**, this is handled automagically.

Register an Options Screen (with all options)

```php
function my_cfs_options_screens( $screens ) {
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
add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );
```

or register multiple Options Screens

```php
function my_cfs_options_screens( $screens ) {
	// Parent
	$screens[] = array(
		'name' => 'options',
		'field_groups' => array( 15 ),
	);
	// Child
	$screens[] = array(
		'name' => 'options-nav',
		'parent' => 'options',
		'field_groups' => array( 17 ),
	);
	return $screens;
}
add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );
```

Once your options screen(s) have been registered you can attach CFS Field Groups to them. Done!

## Retrieve options

Option retrieval requires the screen and field names, it's as easy as:

```php
$value = cfs_get_option( 'options_screen_name', 'field_name_from_field_group' );
```
