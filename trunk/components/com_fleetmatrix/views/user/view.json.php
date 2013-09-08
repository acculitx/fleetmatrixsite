<?php

jimport( 'joomla.application.component.view');

ini_set('display_errors', 1);
error_reporting(E_ERROR);

require(JPATH_COMPONENT . DS . 'models' . DS . 'fields' . DS . 'parententity.php');

class FleetMatrixViewUser extends JView
{
    function display($tpl = null)
    {
        $task = JRequest::getCmd('task', NULL);
        $cmd = JRequest::getCmd('cmd', NULL);

        switch ($task) {
            case 'entity':
                $entities = JFormFieldParentEntity::getParents($cmd, false);
                $output = '';
                foreach ($entities as $entity) {
                    $output .= '<option value="'.$entity->value.'">'.$entity->text.'</option>';
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
