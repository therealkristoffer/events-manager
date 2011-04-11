<?php
function em_rss() {
	if ( !empty( $_REQUEST ['dbem_rss'] ) ) {
		header ( "Content-type: text/xml" );
		echo "<?xml version='1.0'?>\n";
		?>
<rss version="2.0">
	<channel>
		<title><?php echo get_option ( 'dbem_rss_main_title' ); ?></title>
		<link><?php	echo get_permalink ( get_option('dbem_events_page') ); ?></link>
		<description><?php echo get_option ( 'dbem_rss_main_description' ); ?></description>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>Weblog Editor 2.0</generator>
				
		<?php
		$description_format = str_replace ( ">", "&gt;", str_replace ( "<", "&lt;", get_option ( 'dbem_rss_description_format' ) ) );
		//$events = EM_Events::get( array('limit'=>5, 'owner'=>false) );
		$events = EM_Events::get( array('month'=>4, 'year'=>2011, 'owner'=>false) );
		foreach ( $events as $event ) {
			$description = $event->output( get_option ( 'dbem_rss_description_format' ), "rss");
			$description = ent2ncr(convert_chars(strip_tags($description))); //Some RSS filtering
			?>
			<item>
				<title><?php echo $event->output( get_option('dbem_rss_title_format'), "rss" ); ?></title>
				<link><?php echo $event->output('#_EVENTURL'); ?></link>
				<description><?php echo $description; ?></description>
			</item>
			<?php
		}
		?>
		
	</channel>
</rss>
		<?php
		die ();
	}
}
add_action ( 'init', 'em_rss' );
?>