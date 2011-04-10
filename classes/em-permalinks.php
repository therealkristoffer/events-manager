<?php

if( !class_exists('EM_Permalinks') ){
	class EM_Permalinks {
		
		static $em_queryvars = array(
			'event_id', 'event_slug',
			'location_id', 'location_slug',
			'person_id',
			'booking_id',
			'category_id', 'category_slug',
			'ticket_id',
			'calendar_day',
			'book',
			'rss','ical', 'scope', 'page'
		);
		static $scopes = 'today|tomorrow|this\-month|next\-month|past|all|future';
		
		function init(){			
			add_filter('pre_update_option_dbem_events_page', array('EM_Permalinks','option_update'));
			add_filter('init', array('EM_Permalinks','flush'));
			add_filter('rewrite_rules_array',array('EM_Permalinks','rewrite_rules_array'));
			add_filter('query_vars',array('EM_Permalinks','query_vars'));
			add_action('template_redirect',array('EM_Permalinks','init_objects'));
			add_action('template_redirect',array('EM_Permalinks','redirection'));
			//Add filters to rewrite the URLs
			add_filter('em_event_output_placeholder',array('EM_Permalinks','rewrite_urls'),1,3);
			add_filter('em_location_output_placeholder',array('EM_Permalinks','rewrite_urls'),1,3);
		}
		
		function flush(){
			global $wp_rewrite;
			if( get_option('dbem_flush_needed') ){
			   	$wp_rewrite->flush_rules();
				delete_option('dbem_flush_needed');
			}
		}
		
		function rewrite_urls($replace, $object, $result){
			global $wp_query, $wp_rewrite;
			if( $wp_rewrite->using_permalinks() ){
				switch( $result ){
					case '#_EVENTPAGEURL': //Depreciated	
					case '#_LINKEDNAME': //Depreciated
					case '#_EVENTURL': //Just the URL
					case '#_EVENTLINK': //HTML Link
						if( is_object($object) && get_class($object)=='EM_Event' ){
							$event_link = trailingslashit(trailingslashit(EM_URI).'event/'.$object->slug);
							if($result == '#_LINKEDNAME' || $result == '#_EVENTLINK'){
								$replace = "<a href='{$event_link}' title='{$object->name}'>{$object->name}</a>";
							}else{
								$replace = $event_link;
							}
						}
						break;
					case '#_LOCATIONURL':
					case '#_LOCATIONLINK':
					case '#_LOCATIONPAGEURL': //Depreciated
						if( is_object($object) && get_class($object)=='EM_Location' ){
							$link = trailingslashit(trailingslashit(EM_URI).'location/'.$object->slug);
							$replace = ($result == '#_LOCATIONURL' || $result == '#_LOCATIONPAGEURL') ? $link : '<a href="'.$link.'">'.$object->name.'</a>';
						}
						break;
				}
			}
			return $replace;
		}
		
		/**
		 * will redirect old links to new link structures.
		 * @return mixed
		 */
		function redirection(){
			global $wp_rewrite, $post, $wp_query;
			if( $wp_rewrite->using_permalinks() && !is_admin() ){
				//is this a querystring url?
				$events_page_id = get_option ( 'dbem_events_page' );
				if ( is_object($post) && $post->ID == $events_page_id && $events_page_id != 0 ) {
					$page = ( !empty($_GET['page']) && is_numeric($_GET['page']) )? $_GET['page'] : '';
					if ( !empty($_GET['calendar_day']) ) {
						//Events for a specific day
						wp_redirect( self::url($_GET['calendar_day'],$page), 301);
						exit();
					} elseif ( !empty($_GET['location_id']) && is_numeric($_GET['location_id']) ) {
						//Just a single location
						wp_redirect( self::url('location', $_GET['location_id'],$page), 301);
						exit();
					} elseif ( !empty($_GET['location_slug']) ) {
						//Just a single location with slug
						wp_redirect( self::url('location', $_GET['location_slug'],$page), 301);
						exit();
					} elseif ( !empty($_GET['book']) && !empty($_GET['event_id']) ) {
						//bookings page
						wp_redirect( self::url('book', $_GET['event_id']), 301);
						exit();
					} elseif ( !empty($_GET['event_id']) && is_numeric($_GET['event_id']) ) {
						//single event page
						wp_redirect( self::url('event', $_GET['event_id']), 301);
						exit();
					} elseif ( !empty($_GET['event_slug']) ) {
						//single event page with slug
						wp_redirect( self::url('event', $_GET['event_slug']), 301);
						exit();
					} elseif ( !empty($_GET['category_id']) && is_numeric($_GET['category_id']) ){
						//category page
						wp_redirect( self::url('category', $_GET['category_id'], $page), 301);
						exit();
					} elseif ( !empty($_GET['category_slug']) ){
						//category page with slug
						wp_redirect( self::url('category', $_GET['category_slug'], $page), 301);
						exit();
					} elseif( !empty($_GET['scope']) ) {
						// Multiple events page
						wp_redirect( self::url($_GET['scope'], $page), 301);
						exit();
					}			
				}elseif( !empty($_GET['dbem_rss']) ){
					//RSS page
					wp_redirect( self::url('rss'), 301);
					exit();
				}
			}
		}		
		// Adding a new rule
		function rewrite_rules_array($rules){
			//get the slug of the event page
			$events_page_id = get_option ( 'dbem_events_page' );
			$events_page = get_post($events_page_id);
			$em_rules = array();
			if( is_object($events_page) ){
				$events_slug = $events_page->post_name;
				$em_rules[$events_slug.'/('.self::$scopes.')$'] = 'index.php?pagename='.$events_slug.'&scope=$matches[1]'; //events with scope
				$em_rules[$events_slug.'/(\d{4}-\d{2}-\d{2})$'] = 'index.php?pagename='.$events_slug.'&calendar_day=$matches[1]'; //event calendar date search
				$em_rules[$events_slug.'/event/(\d*)$'] = 'index.php?pagename='.$events_slug.'&event_id=$matches[1]'; //single event page with id
				$em_rules[$events_slug.'/event/book/(\d*)$'] = 'index.php?pagename='.$events_slug.'&event_id=$matches[1]&book=1'; //single event booking form with id
				$em_rules[$events_slug.'/event/book/(.+)$'] = 'index.php?pagename='.$events_slug.'&event_slug=$matches[1]&book=1'; //single event booking form with slug
				$em_rules[$events_slug.'/event/(.+)$'] = 'index.php?pagename='.$events_slug.'&event_slug=$matches[1]'; //single event page with slug
				$em_rules[$events_slug.'/location/(\d*)$'] = 'index.php?pagename='.$events_slug.'&location_id=$matches[1]'; //location page with id
				$em_rules[$events_slug.'/location/(.+)$'] = 'index.php?pagename='.$events_slug.'&location_slug=$matches[1]'; //location page with slug
				$em_rules[$events_slug.'/category/(.+)$'] = 'index.php?pagename='.$events_slug.'&category_id=$matches[1]'; //category page with id
				$em_rules[$events_slug.'/category/(.+)$'] = 'index.php?pagename='.$events_slug.'&category_slug=$matches[1]'; //category page with slug
				$em_rules[$events_slug.'/rss$'] = 'index.php?pagename='.$events_slug.'&rss=1'; //rss page
				$em_rules[$events_slug.'/ical$'] = 'index.php?pagename='.$events_slug.'&ical=1'; //ical page
				$em_rules[$events_slug.'/(\d+)$'] = 'index.php?pagename='.$events_slug.'&page=$matches[1]'; //event pageno
			}
			return $em_rules + $rules;
		}
		
		/**
		 * Generate a URL. Pass each section of a link as a parameter, e.g. EM_Permalinks::url('event',$event_id); will create an event link.
		 * @param mixed 
		 */
		function url(){
			$args = func_get_args();
			$em_uri = ( !defined('EM_URI') ) ? get_permalink(get_option("dbem_events_page")):EM_URI; //PAGE URI OF EM
			$event_link = trailingslashit(trailingslashit($em_uri). implode('/',$args));
			return $event_link;
		}
		
		/**
		 * checks if the events page has changed, and sets a flag to flush wp_rewrite.
		 * @param mixed $val
		 * @return mixed
		 */
		function option_update( $val ){
			if( get_option('dbem_events_page') != $val ){
				update_option('dbem_flush_needed',1);
			}
		   	return $val;
		}
		
		// Adding the id var so that WP recognizes it
		function query_vars($vars){
			foreach(self::$em_queryvars as $em_queryvar){
				array_push($vars, $em_queryvar);
			}
		    return $vars;
		}
		
		/**
		 * Not the "WP way" but for now this'll do! 
		 */
		function init_objects(){
			//Build permalinks here
			global $wp_query, $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {
				foreach(self::$em_queryvars as $em_queryvar){
					if( $wp_query->get($em_queryvar) ) {
						$_REQUEST[$em_queryvar] = $wp_query->get($em_queryvar);
					}
				}
		    }
			//dirty rss condition
			if( !empty($_REQUEST['rss']) ){
				$_REQUEST['rss_main'] = 'main';
			}
		}
	}
	EM_Permalinks::init();
}