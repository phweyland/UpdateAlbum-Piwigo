<?php
defined('UPDATEALBUM_PATH') or die('Hacking attempt!');

/**
 * admin plugins menu link
 */
 function updatealbum_admin_plugin_menu_links($menu)
{
  $menu[] = array(
    'NAME' => l10n('Update Album'),
    'URL' => UPDATEALBUM_ADMIN,
    );

  return $menu;
}

