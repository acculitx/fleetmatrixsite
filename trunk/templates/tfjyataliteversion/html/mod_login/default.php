<?php
/**
 * @version		$Id: default.php 18062 2010-07-09 02:58:04Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	Templates.atomic
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<?php if ($type == 'logout') : ?>
<form action="index.php" method="post" id="form-login">
<?php if ($params->get('greeting')) : ?>
	<div>
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('name'));
	} else : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('username'));
	} endif; ?>
	</div>
<?php endif; ?>
<div style="margin-left:15px;">
<div class="imge_use"><img src="images/user_image.jpg" width="" height="" /></div>
<div class="username">User Name</div>
<div class="clear" style="height:5px;"></div>
<div class="acc_s"><a href="#" >Account Settings</a></div>
</div>
	<div class="logout-button">
    
		<input type="submit" name="Submit" class="new-logout" value="<?php echo JText::_('JLOGOUT'); ?>" />
	</div>

	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php else : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="form-login" >
	<?php echo $params->get('pretext'); ?>
	<fieldset class="input">
	<p id="form-login-username">
		<label for="modlgn_username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>
		<input id="modlgn_username" type="text" name="username" class="new_inp"  size="18" />
	</p>
	<p id="form-login-password">
		<label for="modlgn_passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
		<input id="modlgn_passwd" type="password" name="password" class="new_inp" size="18"  />
	</p>
	<p id="form-login-remember">
    <div style="width:183px; float:right">
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<label for="modlgn_remember"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label>
		<input id="modlgn_remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
	<?php endif; ?>
	<input type="submit" name="Submit" class="login" value="<?php echo JText::_('JLOGIN') ?>" />
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
    </div>
    <div class="clear"></div>
	</p>
	</fieldset>
	<div class="pwhelper">
    <div>
     <div class="left_fo acc_s"><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a></div>
     <div class="right_fo acc_s"><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a></div>
     <div class="clear" style="height:10px;"></div>
    </div>
	<!--<ul>
		<li>
			
		</li>
		<li>
			
		</li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
				<?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>-->
    
	</div>
	<?php echo $params->get('posttext'); ?>
</form>
<?php endif; ?>