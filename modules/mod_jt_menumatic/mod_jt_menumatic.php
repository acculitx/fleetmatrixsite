<?php
/*
# ------------------------------------------------------------------------
# Templates for Joomla 1.5 / Joomla 1.6
# ------------------------------------------------------------------------
# Copyright (C) 2011 Jtemplate.ru. All Rights Reserved.
# @license - PHP files are GNU/GPL V2.
# Author: JTemplate.ru
# Websites:  http://www.jtemplate.ru 
# ---------  http://code.google.com/p/jtemplate/   
# ------------------------------------------------------------------------
*/
// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$list			= modJTMATICMenuHelper::getList($params);
$app			= JFactory::getApplication();
$menu			= $app->getMenu();
$active			= $menu->getActive();
$active_id 		= isset($active) ? $active->id : $menu->getDefault()->id;
$path			= isset($active) ? $active->tree : array();
$showAll		= $params->get('showAllChildren');
$class_sfx		= htmlspecialchars($params->get('class_sfx'));
$jt_style_menu	= $params->get('stylemenu');
$jt_menu		= $params->get('jtmenu');
$jt_mootools	= $params->get('mootools');
$jt_opacity		= $params->get('opacity');
$jt_hidedelay	= $params->get('hidedelay');
$jt_physics		= $params->get('physics');
$jt_duration	= $params->get('duration');
$jt_effect		= $params->get('effect');

if(count($list)) {
	require JModuleHelper::getLayoutPath('mod_jt_menumatic', $params->get('layout', 'default'));
}
?>

