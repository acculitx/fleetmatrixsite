<?php

jimport( 'joomla.application.component.view');

ini_set('display_errors', 1);
error_reporting(E_ERROR);

require(JPATH_COMPONENT . DS . 'models' . DS . 'fields' . DS . 'selectgroup.php');

class FleetMatrixViewManageControllers extends JView
{
    function display($tpl = null)
    {
        $task = JRequest::getCmd('task', NULL);
        $cmd = JRequest::getCmd('cmd', NULL);

        switch ($task) {
            case 'group':
                $groups = JFormFieldSelectGroup::getGroups($cmd);
                $output = '';
                foreach ($groups as $group) {
                    $output .= '<option value="'.$group->value.'">'.$group->text.'</option>';
                }
                $data = $output;
            default:
            break;
        }

        $document =& JFactory::getDocument();
        $document->setMimeEncoding('application/json');
        echo json_encode($data);
        return false;

    }

    function setEditModel($value) {
        // edit model not used
    }

    function setListModel($value) {
        // list model not used
    }
}

?>
