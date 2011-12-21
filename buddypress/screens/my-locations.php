<?php
/**
 * Controller for the location views in BP (using mvc terms here)
 */
function bp_em_my_locations() {
	global $bp, $EM_Location;
	if( !is_object($EM_Location) && !empty($_REQUEST['location_id']) ){
		$EM_Location = new EM_Location($_REQUEST['location_id']);
	}
	
	do_action( 'bp_em_my_locations' );
	
	//plug into EM admin code (at least for now)
	include_once(EM_DIR.'/admin/em-admin.php');
	EM_Scripts_and_Styles::localize_script();
	
	$template_title = 'bp_em_my_locations_title';
	$template_content = 'bp_em_my_locations_content';

	if( !empty($_GET['action']) ){
		switch($_GET['action']){
			case 'edit':
				$template_title = 'bp_em_my_locations_editor_title';
				$template_content = 'bp_em_my_locations_editor_content';
				break;
			default :
				$template_title = 'bp_em_my_locations_title';
				$template_content = 'bp_em_my_locations_content';
				break;
		}
	}else{
		$template_title = 'bp_em_my_locations_title';
		$template_content = 'bp_em_my_locations_content';
	}

	add_action( 'bp_template_title', $template_title );
	add_action( 'bp_template_content', $template_content );
	
	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_em_my_locations_title() {
	_e( 'My Locations', 'dbem' );
}

/**
 * Determines whether to show location page or locations page, and saves any updates to the location or locations
 * @return null
 */
function bp_em_my_locations_content() {
	em_locate_template('buddypress/my-locations.php', true);
}

function bp_em_my_locations_editor_title() {
	global $EM_Location;
	if( empty($EM_Location) || !is_object($EM_Location) ){
		$title = __('Add location', 'dbem');
		$EM_Location = new EM_Location();
	}else{
		$title = __('Edit location', 'dbem');
	}
}

function bp_em_my_locations_editor_content(){
	em_locate_template('forms/location-editor.php', true);
}

?>