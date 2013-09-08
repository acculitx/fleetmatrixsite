<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

    ini_set('display_errors', 1);
    error_reporting(E_ERROR);

class ArtclubsViewArtclubs extends JView
{
    function display($tpl = null)
    {
        echo <<<ICON
<style>
.icon-48-artclub {
    background: url('/components/com_artclub/logo48.png') no-repeat left;
}
</style>
ICON;
        JToolBarHelper::title( JText::_( 'FINARTA Art Club' ), 'artclub' );

        JToolBarHelper::divider();

        JToolBarHelper::spacer();

        if (JRequest::getCmd('layout', '') == 'form' || JRequest::getCmd('layout', '') == 'upload') {
            JToolBarHelper::save();
            JToolBarHelper::cancel();
        }

        else if (JRequest::getCmd('layout', '') == '') {
            //JToolBarHelper::apply('upload', $alt="Upload");

            JToolBarHelper::preferences('com_artclub', $height='650');
        }

        // Get data from the model
        $items =& $this->get('Data');
     	$pagination =& $this->get('Pagination');

        // push data into the template
    	$this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);


        $this->assignRef('form_code', file_get_contents(JPATH_BASE . '/forms/upload.php'));
        # read the chronoforms upload form so our edit form looks the same.
        #$db =& JFactory::getDBO();
        #$query = "
        #SELECT html
        #    FROM ".$db->nameQuote('#__chrono_contact')."
        #    WHERE ".$db->nameQuote('name')." = ".$db->quote('upload').";
        #";
        #$db->setQuery($query);
        #$this->assignRef('form_code', $db->loadResult());

        $params = &JComponentHelper::getParams( 'com_artclub' );

        //parent::display($tpl);
    }

}