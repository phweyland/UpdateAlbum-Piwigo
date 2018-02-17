<?php
/**
 * This is the main administration page, if you have only one admin page you can put
 * directly its code here or using the tabsheet system like bellow
 */

defined('UPDATEALBUM_PATH') or die('Hacking attempt!');

global $template, $page, $conf;

$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : 'config';

// include page 
include(UPDATEALBUM_PATH . 'admin/' . $page['tab'] . '.php');



// template vars
$template->assign(array(
  'UPDATEALBUM_PATH'=> UPDATEALBUM_PATH, // used for images, scripts, ... access
  'UPDATEALBUM_ABS_PATH'=> realpath(UPDATEALBUM_PATH), // used for template inclusion (Smarty needs a real path)
  'UPDATEALBUM_ADMIN' => UPDATEALBUM_ADMIN,
  ));

// send page content
 $template->assign_var_from_handle('ADMIN_CONTENT', 'updatealbum_content');
