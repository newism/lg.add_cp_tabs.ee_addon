<?php
/**
* Class file for LG Add CP Tabs
* 
* This file must be placed in the
* /system/extensions/ folder in your ExpressionEngine installation.
*
* @package LgAddCPTabs
* @version 1.2.0
* @author Leevi Graham <http://leevigraham.com>
* @see http://leevigraham.com/cms-customisation/expressionengine/addon/lg-add-cp-tabs/
* @copyright Copyright (c) 2007-2008 Leevi Graham
* @license http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-Share Alike 3.0 Unported
*/

$L = array(

	"lg_add_cp_tabs_title" => "LG Add CP Tabs",

	'enable_extension_title'	=> 'Enable extension',
	'enable_extension_label'	=> 'Enable {addon_name} for this site?',

	'check_for_updates_title' 	=> 'Check for updates',
	'check_for_updates_info' 	=> '{addon_name} can call home, check for recent updates and display them on your CP homepage? This feature requires <a href="http://leevigraham.com/cms-customisation/expressionengine/lg-addon-updater/">LG Addon Updater</a> to be installed and activated.',
	'check_for_updates_label' 	=> 'Would you like this extension to check for updates?',

	'success_extension_settings_saved'	=> 'Extension settings saved successfully',

	'member_groups_defaults_title' => 'Member groups default',
	'member_groups_defaults_info' 	=> "<p>Default tabs and CP links can be assigned to a member group. When a new member is created the default member group tabs and links will be assigned.</p>
									<p><strong>Tabs</strong> must be declared in the following format: <code>tab_name|cp_url|sort_order</code> Example: <code>Extensions|C=admin&M=utilities&P=extensions_manager|1</code></p>
									<p><strong>Links</strong> must be declared in the following format: <code>link_text|url|sort_order</code> Example: <code>My Site|http://ee.sandbox/index.php|1</code></p>
									<p>Multiple tabs &amp; links must be separated by a line break.</p>",

	// END
	''=>''
);
?>