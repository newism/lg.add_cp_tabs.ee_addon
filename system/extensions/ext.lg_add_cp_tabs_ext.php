<?php

/**
 * LG Add CP Tabs extension file
 * 
 * This file must be placed in the
 * /system/extensions/ folder in your ExpressionEngine installation.
 *
 * @package LGAddCPTabs
 * @version 1.2.0
 * @author Leevi Graham <http://leevigraham.com>
 * @see http://leevigraham.com/cms-customisation/expressionengine/addon/lg-htaccess-generator/
 * @copyright Copyright (c) 2007-2008 Leevi Graham
 * @license {@link http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-Share Alike 3.0 Unported} All source code commenting and attribution must not be removed. This is a condition of the attribution clause of the license.
 */

if ( ! defined('EXT')) exit('Invalid file request');

/**
 * This extension adds a new tab to the CP publish / edit pages. It also adds a 'quick tweet' bar to the footer of the CP.
 *
 * @package LGAddCPTabs
 * @version 1.2.0
 * @author Leevi Graham <http://leevigraham.com>
 * @see http://leevigraham.com/cms-customisation/expressionengine/addon/lg-htaccess-generator/
 * @copyright Copyright (c) 2007-2008 Leevi Graham
 * @license {@link http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-Share Alike 3.0 Unported} All source code commenting and attribution must not be removed. This is a condition of the attribution clause of the license.
 *
 */
class Lg_add_cp_tabs_ext{

	/**
	 * Extension name
	 * 
	 * @var		string
	 * @since	Version 1.0.0
	 */
	var $name = 'LG Add CP Tabs';

	/**
	 * Extension version
	 * 
	 * @var		string
	 * @since	Version 1.0.0
	 */
	var $version = '1.2.0';

	/**
	 * Extension description
	 * 
	 * @var		string
	 * @since	Version 1.0.0
	 */
	var $description = 'Automatically add CP Tabs for new members';

	/**
	 * If $settings_exist = 'y' then a settings page will be shown in the ExpressionEngine admin
	 * 
	 * @since  	Version 1.0.0
	 * @var 	string
	 */
	var $settings_exist = 'y';
	
	/**
	 * Link to extension documentation
	 * 
	 * @since  	Version 1.0.0
	 * @var 	string
	 */
	var $docs_url = "http://leevigraham.com/cms-customisation/expressionengine/lg-add-cp-tabs/";

	/**
	 * Default settings
	 * 
	 * @var 	array
	 * @since	Version 1.2.0
	 */
	var $default_settings = array(
		'enabled' => TRUE,
		'check_for_updates' => TRUE,
		'member_group_prefs' => array()
	);

	/**
	 * Extension hooks
	 * 
	 * @var 	array
	 * @since	Version 1.2.0
	 */
	var $hooks = array(
			'cp_members_member_create',
			'show_full_control_panel_end',
			'lg_addon_update_register_addon',
			'lg_addon_update_register_source'
	);

	/**
	 * Paypal details for donate button
	 * 
	 * @var 	array
	 * @since	Version 1.2.0
	 */
	var $paypal 			=  array(
		"account"				=> "sales@newism.com.au",
		"donations_accepted"	=> TRUE,
		"donation_amount"		=> "20.00",
		"currency_code"			=> "USD",
		"return_url"			=> "http://leevigraham.com/donate/thanks/",
		"cancel_url"			=> "http://leevigraham.com/donate/cancel/"
	);

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array|string $settings Extension settings associative array or an empty string
	 * @since	Version 1.0.0
	 */
	public function __construct($settings='')
	{
		global $IN, $SESS;
		$this->settings = ($settings == FALSE) ? $this->get_settings() : $this->save_settings_to_session($settings);
		// print_r($this->settings);
	}

	/**
	 * Activate the extension
	 * 
	 * @access 	public
	 * @see 	http://expressionengine.com/docs/development/extensions.html#enable
	 * @since	Version 1.0.0
	 */
	public function activate_extension()
	{
		$this->create_hooks();
	}

	/**
	 * Update the extension
	 *
	 * @access 	public
	 * @see		http://expressionengine.com/docs/development/extensions.html#enable
	 * @since	Version 1.0.0
	 * @param	string $current The current installed version
	 */
	public function update_extension($current = '')
	{
		if($current < "1.1.0")
		{
			$old_settings = $this->settings;
			$new_settings['enabled'] = ($old_settings['enable'] == "y") ? TRUE : FALSE;
			$new_settings['check_for_updates'] = ($old_settings['check_for_updates'] == "y") ? TRUE : FALSE;
			$member_group_query = $DB->query("SELECT group_id, group_title FROM exp_member_groups WHERE site_id = {$site_id} ORDER BY group_id");
			foreach ($member_group_query->result as $member_group)
			{
				if(isset($old_settings['tabs_' . $member_group['group_id']]) == FALSE)
				{
					$old_settings['tabs_' . $member_group['group_id']] = "";
					$old_settings['links_' . $member_group['group_id']] = "";
				}
				$new_settings['member_group_prefs'][$member_group['group_id']]['tabs'] = $old_settings['tabs_' . $member_group['group_id']];
				$new_settings['member_group_prefs'][$member_group['group_id']]['links'] = $old_settings['links_' . $member_group['group_id']];
			}
			$this->settings = $new_settings;
		}
		$this->update_hooks();
		$this->save_settings_to_session($this->settings);
		$this->save_settings_to_db($this->settings);
	}

	/**
	 * Disable the extension
	 * 
	 * @access 	public
	 * @since	Version 1.0.0
	 * @see		http://expressionengine.com/docs/development/extensions.html#disable
	 */
	public function disable_extension(){}

	/**
	 * Render the settings form
	 * 
	 * @access 	public
	 * @param	string $current_settings The current settings
	 * @see		http://expressionengine.com/docs/development/extensions.html#settings
	 * @since	Version 1.0.0
	 */
	public function settings_form($current_settings)
	{

		global $DB, $DSP, $LANG, $PREFS, $REGX, $SESS;

		$site_id = $PREFS->ini("site_id");
		$settings = $this->settings;
		$addon_name = $this->name;

		$lgau_query = $DB->query("SELECT class FROM exp_extensions WHERE class = 'Lg_addon_updater_ext' AND enabled = 'y' LIMIT 1");
		$lgau_enabled = $lgau_query->num_rows ? TRUE : FALSE;

		$member_group_query = $DB->query("SELECT group_id, group_title FROM exp_member_groups WHERE site_id = {$site_id} ORDER BY group_id");

		$DSP->title = $this->name . " " . $this->version . " | " . $LANG->line('extension_settings');
		$DSP->crumbline = TRUE;
		$DSP->crumb  = $DSP->anchor(BASE.AMP.'C=admin'.AMP.'area=utilities', $LANG->line('utilities'));
		$DSP->crumb .= $DSP->crumb_item($DSP->anchor(BASE.AMP.'C=admin'.AMP.'M=utilities'.AMP.'P=extensions_manager', $LANG->line('extensions_manager')));
		$DSP->crumb .= $DSP->crumb_item($this->name . " " . $this->version);

		$DSP->body .= "<div class='mor settings-form'>";
		// PAYPAL
		if(isset($this->paypal["donations_accepted"]) === TRUE)
		{
			$DSP->body .= "<p class='donate paypal'>
								<a rel='external'"
									. "href='https://www.paypal.com/cgi-bin/webscr?"
										. "cmd=_donations&amp;"
										. "business=".rawurlencode($this->paypal["account"])."&amp;"
										. "item_name=".rawurlencode($this->name . " Development: Donation")."&amp;"
										. "amount=".rawurlencode($this->paypal["donation_amount"])."&amp;"
										. "no_shipping=1&amp;return=".rawurlencode($this->paypal["return_url"])."&amp;"
										. "cancel_return=".rawurlencode($this->paypal["cancel_url"])."&amp;"
										. "no_note=1&amp;"
										. "tax=0&amp;"
										. "currency_code=".$this->paypal["currency_code"]."&amp;"
										. "lc=US&amp;"
										. "bn=PP%2dDonationsBF&amp;"
										. "charset=UTF%2d8'"
									."class='button'
									target='_blank'>
									Support this addon by donating via PayPal.
								</a>
							</p>";
		}
		$DSP->body .= $DSP->heading("{$this->name} <small>{$this->version}</small>");
		$DSP->body .= $DSP->form_open(
								array('action' => 'C=admin'.AMP.'M=utilities'.AMP.'P=save_extension_settings'),
								array('name' => strtolower(get_class($this))
							));
		ob_start(); include(PATH_LIB.'lg_add_cp_tabs/views/Lg_add_cp_tabs_ext/form_settings.php'); $DSP->body .= ob_get_clean();
		$DSP->body .= $DSP->qdiv('itemWrapperTop', $DSP->input_submit("Save extension settings"));
		$DSP->body .= $DSP->form_c();
		$DSP->body .= "</div>";
	}

	/**
	 * Save the settings
	 * 
	 * @access 	public
	 * @since	Version 1.0.0
	 * @see http://expressionengine.com/docs/development/extensions.html#settings
	 */
	public function save_settings()
	{
		global $IN, $PREFS;
		$new_settings = $IN->GBL("Lg_add_cp_tabs_ext", "POST");
		$this->save_settings_to_db($new_settings);
	}

	/**
	* Updates the new member with the default tabs
	*
	* @see http://expressionengine.com/developers/extension_hooks/cp_members_member_create/
	* @since version 1.0.0
	* @param $member_id		integer		The Member ID of the newly created member
	* @param $data			array		Array of data for new member like username, screen_name, and email
	*/
	function cp_members_member_create($member_id, $data)
	{
		global $DB;
		if(isset($this->settings['member_group_prefs'][$data['group_id']]))
		{
			$group_prefs = $this->settings['member_group_prefs'][$data['group_id']];
			$DB->query($DB->update_string('exp_members',
												array(
													'quick_tabs' => $group_prefs['tabs'],
													'quick_links' => $group_prefs['links']
												),
												"member_id = {$member_id}")
						);
		}
	}

	/**
	 * Saves the objects hooks to the DB
	 * 
	 * @access 	private
	 * @since	Version 1.2.0
	 */
	private function create_hooks()
	{
		global $DB;

		$hook_template = array(
			'class'    => __CLASS__,
			'settings' => FALSE,
			'priority' => 10,
			'version'  => $this->version,
			'enabled'  => 'y'
		);

		foreach($this->hooks as $key => $value)
		{
			if(is_array($value))
			{
				$hook["hook"] = $key;
				$hook["method"] = (isset($value["method"]) === TRUE) ? $value["method"] : $key;
				$hook = array_merge($hook, $value);
			}
			else
			{
				$hook["hook"] = $hook["method"] = $value;
			}
			$hook = array_merge($hook_template, $hook);
			$DB->query($DB->insert_string('exp_extensions', $hook));
		}
	}

	/**
	 * Updates the objects hooks in the DB
	 * 
	 * Delete the current hooks, recreate them from scratch
	 * 
	 * @access 	private
	 * @since	Version 1.2.0
	 */
	private function update_hooks()
	{
		$this->delete_hooks();
		$this->create_hooks();
	}

	/**
	 * Delete the objects hooks from the DB
	 * 
	 * @access 	private
	 * @since	Version 1.2.0
	 */
	private function delete_hooks()
	{
		global $DB;
		$DB->query("DELETE FROM `exp_extensions` WHERE `class` = '".get_class($this)."'");
		return $DB->affected_rows;
	}

	/**
	 * Takes the control panel html before it is sent to the browser
	 *
	 * This method does two main things:
	 * - Changes the custom field name in the {@link http://expressionengine.com/docs/cp/admin/weblog_administration/custom_fields_edit.html Add/Edit Custom Fields page}
	 * - Adds custom scripts and styles to the publish / edit form when creating editing polls.
	 *
	 * @access	public
	 * @param	string $out The control panel html
	 * @return	string The modified control panel html
	 * @since 	Version 1.0.0
	 * @see		http://expressionengine.com/developers/extension_hooks/show_full_control_panel_end/
	 */
	public function show_full_control_panel_end($out)
	{
		global $IN;
		$this->get_last_call($out);
		return $out;
	}

	/**
	 * Register a new Addon Source
	 *
	 * @access	public
	 * @param	array $sources The existing sources
	 * @return	array The new source list
	 * @see 	http://leevigraham.com/cms-customisation/expressionengine/lg-addon-updater/
	 * @since	Version 1.0.0
	 */
	public function lg_addon_update_register_source($sources)
	{
		global $EXT, $PREFS;
		$this->get_last_call($sources);

		// add a new source
		// must be in the following format:
		/*
		<versions>
			<addon id='LG Addon Updater' version='2.0.0' last_updated="1218852797" docs_url="http://leevigraham.com/" />
		</versions>
		*/
		if($this->settings['check_for_updates'] == TRUE)
		{
			$sources[] = 'http://leevigraham.com/version-check/versions.xml';
		}

		return $sources;

	}

	/**
	 * Register a new Addon
	 *
	 * @access	public
	 * @param	array $addons The existing sources
	 * @return	array The new addon list
	 * @see		http://leevigraham.com/cms-customisation/expressionengine/lg-addon-updater/
	 * @since	Version 1.0.0
	 */
	public function lg_addon_update_register_addon($addons)
	{
		global $EXT, $PREFS;
		// -- Check if we're not the only one using this hook
		$this->get_last_call($addons);

		// add a new addon
		// the key must match the id attribute in the source xml
		// the value must be the addons current version
		if($this->settings['check_for_updates'] == TRUE)
		{
			$addons["LG Add CP Tabs"] = $this->version;
		}

		return $addons;
	}

	/**
	 * Get the extension settings from the $SESS or database
	 *
	 * @access	private
	 * @param	array $addons The existing sources
	 * @return	array The new addon list
	 * @since	Version 1.0.0
	 */
	private function get_settings($refresh = FALSE)
	{
		global $DB, $PREFS, $REGX;
		$settings = FALSE;
		$site_id = $PREFS->ini("site_id");
		if(isset($SESS->cache['lg_add_cp_tabs'][__CLASS__]['settings']) === FALSE || $refresh === TRUE)
		{
			$settings_query = $DB->query("SELECT `settings` FROM `exp_extensions` WHERE `enabled` = 'y' AND `class` = '".__CLASS__."' LIMIT 1");
			// if there is a row and the row has settings
			if ($settings_query->num_rows > 0 && $settings_query->row['settings'] != '')
			{
				// save them to the cache
				$settings = $REGX->array_stripslashes(unserialize($settings_query->row['settings']));
			}
		}
		else
		{
			$settings = $SESS->cache['lg_add_cp_tabs'][__CLASS__]['settings'];
		}
		if($settings == FALSE)
		{
			$settings = $this->build_default_settings();
			$this->save_settings_to_db($settings);
		}
		$this->save_settings_to_session($settings);
		return $settings;
	}

	/**
	 * Save the extension settings to the current $SESS
	 *
	 * @access	private
	 * @param	array $settings The existing sources
	 * @since	Version 1.2.0
	 */
	private function save_settings_to_session($settings = FALSE)
	{
		$SESS->cache['lg_add_cp_tabs'][__CLASS__]['settings'] = $settings;
		return $settings;
	}

	/**
	 * Save the extension settings to the database
	 *
	 * @access	private
	 * @param	array $settings The existing sources
	 * @since	Version 1.2.0
	 */
	private function save_settings_to_db($settings)
	{
		global $DB;
		$DB->query($sql = $DB->update_string("exp_extensions", array("settings" => $this->serialize($settings)), array("class" => __CLASS__)));
	}

	/**
	 * Build the default settings array
	 *
	 * @access	private
	 * @param	array $settings The existing sources
	 * @since	Version 1.2.0
	 */
	private function build_default_settings()
	{
		global $DB, $PREFS;
		$site_settings = $this->default_settings;
		$site_id = $PREFS->ini('site_id');
		$member_group_query = $DB->query("SELECT group_id, group_title FROM exp_member_groups WHERE site_id = {$site_id} ORDER BY group_id");
		foreach ($member_group_query->result as $member_group)
		{
			$site_settings['member_group_prefs'][$member_group['group_id']]['tabs'] = "";
			$site_settings['member_group_prefs'][$member_group['group_id']]['links'] = "";
		}
		return $site_settings;
	}

	/**
	 * Creates a select box
	 *
	 * @access	private
	 * @param	mixed $selected The selected value
	 * @param	array $options The select box options in a multi-dimensional array. Array keys are used as the option value, array values are used as the option label
	 * @param	string $input_name The name of the input eg: Lg_polls_ext[log_ip]
	 * @param	string $input_id A unique ID for this select. If no id is given the id will be created from the $input_name
	 * @param	boolean $use_lanng Pass the option label through the $LANG->line() method or display in a raw state
	 * @param 	array $attributes Any other attributes for the select box such as class, multiple, size etc
	 * @return	string Select box html
	 * @since	Version 1.2.0
	 */
	private function select_box($selected, $options, $input_name, $input_id = FALSE, $use_lang = TRUE, $key_is_value = TRUE, $attributes = array())
	{
		global $LANG;

		$input_id = ($input_id === FALSE) ? str_replace(array("[", "]"), array("_", ""), $input_name) : $input_id;

		$attributes = array_merge(array(
			"name" => $input_name,
			"id" => strtolower($input_id)
		), $attributes);

		$attributes_str = "";
		foreach ($attributes as $key => $value)
		{
			$attributes_str .= " {$key}='{$value}' ";
		}

		$ret = "<select{$attributes_str}>";

		foreach($options as $option_value => $option_label)
		{
			if (!is_int($option_value))
				$option_value = $option_value;
			else
				$option_value = ($key_is_value === TRUE) ? $option_value : $option_label;

			$option_label = ($use_lang === TRUE) ? $LANG->line($option_label) : $option_label;
			$checked = ($selected == $option_value) ? " selected='selected' " : "";
			$ret .= "<option value='{$option_value}'{$checked}>{$option_label}</option>";
		}

		$ret .= "</select>";
		return $ret;
	}

	/**
	 * Serialise the array
	 * 
	 * @access	private
	 * @param	array The array to serialise
	 * @return	array The serialised array
	 */ 
	private function serialize($vals)
	{
		global $PREFS;

		if ($PREFS->ini('auto_convert_high_ascii') == 'y')
		{
			$vals = $this->array_ascii_to_entities($vals);
		}

	 	return addslashes(serialize($vals));
	}

	/**
	 * Unerialise the array
	 * 
	 * @access	private
	 * @param	array $vals The array to unserialise
	 * @param	boolean $convert convert the entities to ascii
	 * @return	array The serialised array
	 */ 
	private function unserialize($vals, $convert=TRUE)
	{
		global $REGX, $PREFS;

		if (($tmp_vals = @unserialize($vals)) !== FALSE)
		{
			$vals = $REGX->array_stripslashes($tmp_vals);

			if ($convert AND $PREFS->ini('auto_convert_high_ascii') == 'y')
			{
				$vals = $this->array_entities_to_ascii($vals);
			}
		}

	 	return $vals;
	}

	/**
	 * Get the last call from a previous hook
	 * 
	 * @access	private
	 * @param	mixed $param The variable we are going to fill with the last call
	 * @param	mixed $default The value to use if no last call is available
	 */ 
	private function get_last_call(&$param, $default = NULL)
	{
		global $EXT;

		if ($EXT->last_call !== FALSE)
			$param = $EXT->last_call;
		else if ($param !== NULL && $default !== NULL)
			$param = $default;
	}
}
?>