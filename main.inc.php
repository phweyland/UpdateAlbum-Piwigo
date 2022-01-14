<?php
/*
Plugin Name: Update Album
Version: 1.3.a
Description: This plugin updates selected photos.
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=861
Author: PhWeyland
Author URI: http://ph-wd.com
Has Settings: true
*/

/**
 * This is the main file of the plugin, called by Piwigo in "include/common.inc.php" line 137.
 * At this point of the code, Piwigo is not completely initialized, so nothing should be done directly
 * except define constants and event handlers (see http://piwigo.org/doc/doku.php?id=dev:plugins)
 */

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');


// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

define('UPDATEALBUM_ID',      basename(dirname(__FILE__)));
define('UPDATEALBUM_PATH' ,   PHPWG_PLUGINS_PATH . UPDATEALBUM_ID . '/');
define('UPDATEALBUM_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . UPDATEALBUM_ID);


// +-----------------------------------------------------------------------+
// | Add event handlers                                                    |
// +-----------------------------------------------------------------------+
// init the plugin
add_event_handler('init', 'updatealbum_init');

/*
 * this is the common way to define event functions: create a new function for each event you want to handle
 */
//if (defined('IN_ADMIN'))
//{
  // file containing all admin handlers functions
//  $admin_file = UPDATEALBUM_PATH . 'include/admin_events.inc.php';
//}

/**
 * plugin initialization
 *   - check for upgrades
 *   - unserialize configuration
 *   - load language
 */

function updatealbum_init()
{
  global $conf;

  // load plugin language file
  load_language('plugin.lang', UPDATEALBUM_PATH);
  // prepare plugin configuration
  $conf['updatealbum'] = safe_unserialize($conf['updatealbum']);

}
