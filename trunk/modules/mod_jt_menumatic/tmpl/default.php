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
// No direct access.
defined('_JEXEC') or die;
// Note. It is important to remove spaces between elements.
$document 		=& JFactory::getDocument();

if ($jt_style_menu == 0) { $document->addStyleSheet(JURI::base() . 'modules/mod_jt_menumatic/css/horizontal-menumatic.css'); $jt_style = 'horizontal'; }
	else 				 { $document->addStyleSheet(JURI::base() . 'modules/mod_jt_menumatic/css/vertical-menumatic.css'); 	 $jt_style = 'vertical'; }

if ($jt_menu == 1) {	
	if ($jt_mootools == 1) { $document->addScript(JURI::base() . 'modules/mod_jt_menumatic/js/mootools-yui-compressed.js'); }
	if ($jt_mootools == 2) { $document->addScript('http://ajax.googleapis.com/ajax/libs/mootools/1.3.2/mootools-yui-compressed.js'); }
$document->addScript(JURI::base() . 'modules/mod_jt_menumatic/js/MenuMatic_0.68.3.js'); 
}
?>


<ul id="nav" class="menu<?php echo $class_sfx;?>">
<?php
foreach ($list as $i => &$item) :
	$class = '';
	if ($item->id == $active_id) {
		$class .= 'current ';
	}

	if (	$item->type == 'alias' &&
			in_array($item->params->get('aliasoptions'),$path)
		||	in_array($item->id, $path)) {
	  $class .= 'active ';
	}
	if ($item->deeper) {
		$class .= 'deeper ';
	}
	
	if ($item->parent) {
		$class .= 'parent ';
	}

	if (!empty($class)) {
		$class = ' class="'.trim($class) .'"';
	}

	echo '<li id="item-'.$item->id.'"'.$class.'>';

	// Render the menu item.
	switch ($item->type) :
		case 'separator':
		case 'url':
		case 'component':
			require JModuleHelper::getLayoutPath('mod_jt_menumatic', 'default_'.$item->type);
			break;

		default:
			require JModuleHelper::getLayoutPath('mod_jt_menumatic', 'default_url');
			break;
	endswitch;

	// The next item is deeper.
	if ($item->deeper) {
		echo '<ul>';
	}
	// The next item is shallower.
	else if ($item->shallower) {
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
endforeach;
?></ul>
<script type="text/javascript">
        window.addEvent('domready', function() {            
            var myMenu = new MenuMatic({ 
				orientation:'<?php echo $jt_style; ?>',
				opacity:'<?php echo $jt_opacity; ?>',
				hideDelay:'<?php echo $jt_hidedelay; ?>',				
				duration:'<?php echo $jt_duration; ?>',
				effect:'<?php echo $jt_effect; ?>',
				physics:<?php echo $jt_physics;?>
			});         
        });     
</script>
<div style="display:none"><a href="http://jtemplate.ru" target="_blank">jtemplate.ru - free extensions for joomla</a></div>