<?php 
	
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	new ssi_content_groups_gtm_acf();
	
	class ssi_content_groups_gtm_acf {
		
		private $groups = array(
			'group_669fedc17762e'
		);
		
		public function __construct() {
			add_action('acf/update_field_group', array($this, 'update_field_group'), 1, 1);
			add_filter('acf/validate_field_group', array($this, 'remove_private_if_dev'), 20, 1);
			add_filter('acf/prepare_field_group_for_export', array($this, 'set_private'), 20, 1);
			add_filter('acf/settings/load_json', array($this, 'add_load_point'));
			//add_filter('acf/location/rule_types', array($this, 'rule_types'));
			add_filter('acf/location/rule_values/post_type', array($this, 'post_type_rule_values'));
			add_filter('acf/location/rule_match/post_type', array($this, 'match_public_post_type'), 20, 3);
		} // end public function __construct
		
		public function rule_types($choices) {
			//echo '<pre>'; print_r($choices); echo '</pre>';
			return $choices;
		} // end public function rule_types
		
		public function post_type_rule_values($choices) {
			//echo '<pre>'; print_r($choices); echo '</pre>';
			if (!isset($choices['public-post-type'])) {
				$choices['public-post-type'] = __('Public Post Type');
			}
			return $choices;
		} // end public function post_type_rule_values
		
		public function match_public_post_type($match, $rule, $options) {
			if ($rule['value'] != 'public-post-type' || (isset($options['comment']) && $options['comment'])) {
				return $match;
			}
			if (isset($options['post_type']) && !empty($options['post_type'])) {
				$post_type = $options['post_type'];
			} else {
				if (!isset($options['post_id'])) {
					return false;
				}
				$post_type = get_post_type(intval($options['post_id']));
			}
			$post_type = get_post_type_object($post_type);
			if (!$post_type) {
				return false;
			}
			if ($rule['operator'] == '==') {
				$match = $post_type->public;
			} elseif ($rule['operator'] == '!=') {
				$match = !$post_type->public;
			}
			//$match = true;
			return $match;
		} // end public function match_public_post_type
		
		public function add_load_point($paths) {
			$paths[] = dirname(dirname(__FILE__)).'/acf-json';
			return $paths;
		} // end public function add_load_point
		
		public function set_private($group) {
			if (in_array($group['key'], $this->groups)) {
				$group['private'] = true;
			}
			return $group;
		} // end public function set_private
		
		public function remove_private_if_dev($group) {
			//echo '<pre>'; print_r($group); die;
			if (in_array($group['key'], $this->groups)) {
				if (defined('SSI_DEV_SITE') && SSI_DEV_SITE) {
					$group['private'] = false;
				}
			}
			return $group;
		} // end public function remove_private_if_dev
		
		public function update_field_group($group) {
			if (in_array($group['key'], $this->groups)) {
				//$group['private'] = true;
				add_filter('acf/settings/save_json',  array($this, 'override_location'), 9999);
			}
			return $group;
		} // end public function update_field_group
		
		public function override_location($path) {
			remove_filter('acf/settings/save_json',  array($this, 'override_location'), 9999);
			$path = dirname(dirname(__FILE__)).'/acf-json';
			return $path;
		} // end public function override_json_location
		
	} // end class ssi_content_groups_gtm_acf