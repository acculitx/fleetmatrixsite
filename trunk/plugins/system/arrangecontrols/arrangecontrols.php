<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Language Code plugin class.
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.language
 */
class plgSystemArrangeControls extends JPlugin
{
	/**
	 * Plugin that change the language code used in the <html /> tag
	 */
	public function onAfterRender()
	{
		// Use this plugin only in site application
		if (JFactory::getApplication()->isSite())
		{
			// Get the response body
$matches = array();
 		$body = JResponse::getBody();
                $res = preg_match('/<!-- REPLACE -->(.*)<!-- REPLACE_END -->/s', $body, $matches);
                if ($matches && FALSE!==strpos($body, 'REPLACE_HERE')) {
                    $body = preg_replace('/<!-- REPLACE_HERE -->/', $matches[1], $body);
                    $body = preg_replace('/<!-- REPLACE -->(.*)<!-- REPLACE_END -->/s', '', $body);
                }

		JResponse::setBody($body);
		}
return true;
	}

	/**
	 * @param	JForm	$form	The form to be altered.
	 * @param	array	$data	The associated data for the form.
	 *
	 * @return	boolean
	 * @since	2.5
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Check we have a form
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}

		// Check we are manipulating a valid form.
		$app = JFactory::getApplication();
		if ($form->getName() != 'com_plugins.plugin'
			|| isset($data->name) && $data->name != 'plg_system_languagecode'
			|| empty($data) && !$app->getUserState('plg_system_language_code.edit')
		)
		{
			return true;
		}

		// Mark the plugin as being edited
		$app->setUserState('plg_system_language_code.edit', $data->name == 'plg_system_languagecode');

		// Get site languages
		$languages = JLanguage::getKnownLanguages(JPATH_SITE);

		// Inject fields into the form
		foreach ($languages as $tag => $language)
		{
			$form->load('
<form>
	<fields name="params">
		<fieldset
			name="languagecode"
			label="PLG_SYSTEM_LANGUAGECODE_FIELDSET_LABEL"
			description="PLG_SYSTEM_LANGUAGECODE_FIELDSET_DESC"
		>
			<field
				name="'.strtolower($tag).'"
				type="text"
				description="' . htmlspecialchars(JText::sprintf('PLG_SYSTEM_LANGUAGECODE_FIELD_DESC', $language['name']), ENT_COMPAT, 'UTF-8') . '"
				translate_description="false"
				label="' . $tag . '"
				translate_label="false"
				size="7"
				filter="cmd"
			/>
		</fieldset>
	</fields>
</form>
			');
		}
		return true;
	}
}
