<?php 
	
	/*
		Plugin Name: Content Groups by Site-Seeker, Inc
		Plugin URI: https://github.com/Hube2/ssi-content-groups
		Description: Add Content Groups and Push to GTM datalayer
		Version: 0.1.0
		Author: Site-Seeker, Inc.
		Author URI: http://www.site-seeker.com/
		Author URI: https://github.com/Hube2
		GitHub Plugin URI: https://github.com/Hube2/ssi-content-groups
	*/
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	require(dirname(__FILE__).'/include/acf.php');
	
	new ssi_content_groups_gtm();
	
	class ssi_content_groups_gtm {
		
		public function __construct() {
			
			add_action('wp_head', array($this, 'wp_head'));
			
		} // end public function __construct
		
		public function wp_head() {
			if (!function_exists('get_field')) {
				return;
			}
			$groups = array();
			$queried_object = get_queried_object();
			if (is_a($queried_object, 'WP_Post')) {
				for ($i=1; $i<4; $i++) {
					if (get_field('content_group_'.$i)) {
						$groups[] = get_field('content_group_'.$i, $queried_object->ID);
					}
				}
			}
			if (!empty($groups)) {
				//echo "\r\n\r\n".'<!-- JH ';print_r($groups); echo ' -->';
				?>
					<script type="text/javascript">
						var ssi_content_groups = [];
						<?php 
							$i = 1;
							foreach ($groups as $group) {
								?>ssi_content_groups['content_group_<?php echo $i; ?>'] = '<?php echo $group; ?>';
								<?php 
								$i++;
							}
						?>
						ssi_content_groups['action'] = 'content_group_push';
						window.dataLayer = window.dataLayer || [];
						window.dataLayer.push(ssi_content_groups);
					</script>
				<?php 
			}
		} // end public function wp_head
		
	} // end class ssi_content_groups_gtm