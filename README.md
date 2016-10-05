This is a WordPress plugin, [Official download available on WordPress.org](http://wordpress.org/plugins/cfs-options-screens/)

# Custom Field Suite Options Screens

Create any number of options screens powered by [Custom Field Suite](http://customfieldsuite.com)

## Documentation

Begin by creating your Field Group(s) as you normally would. *Unless setting up an [override](#overrides)*, be sure to set **NO Placement Rules**, this is handled automagically.

Register an Options Screen (with all options)

```php
function my_cfs_options_screens( $screens ) {
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

add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );
```

or register multiple Options Screens

```php
function my_cfs_options_screens( $screens ) {

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

add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );
```

Once your options screen(s) have been registered you can attach CFS Field Groups to them. Done!

### Overrides

You can also use CFS Options Screens to set up Field Group overrides, allowing a Field Group to appear both on a CFS Options Screen and a post edit screen. The CFS Options Screen will act as the default/fallback and the post edit screen will override those defaults.

 ```php
 function my_cfs_options_screens( $screens ) {
	$screens[] = array(
		'name'            => 'options',
		'menu_title'      => __( 'Site Options' ),
		'page_title'      => __( 'Customize Site Options' ),
		'menu_position'   => 100,
		'icon'            => 'dashicons-admin-generic', // optional, dashicons-admin-generic is the default
		'field_groups'      => array(
                array(
                    'title'         => 'My CFS Field Group Name',
                    'has_overrides' => true,
				),
			),
	);

	return $screens;
}

add_filter( 'cfs_options_screens', 'my_cfs_options_screens' );
```

Check out the `cfs_options_screens_override_note_default` and `cfs_options_screens_override_note_override` filters to customize the messaging for CFS Options Screens overrides.

## Retrieve options

Option retrieval requires the screen and field names, it's as easy as:

```php
$value = cfs_get_option( 'options_screen_name', 'field_name_from_field_group' );
```
