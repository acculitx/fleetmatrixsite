<?php

jimport( 'joomla.application.component.view');

ini_set('display_errors', 1);
error_reporting(E_ERROR);

require_once(JPATH_COMPONENT . DS . 'models' . DS . 'fields' . DS . 'searchgroup.php');

class FleetMatrixViewReports extends JView
{
    function display($tpl = null)
    {
        $task = JRequest::getCmd('task', NULL);
        $cmd = JRequest::getCmd('val', NULL);
        $cpval = JRequest::getCmd('cpval', NULL);
        $val = JRequest::getCmd('cval', NULL);

        switch ($task) {
            case 'vehicle':
                $groups = JFormFieldSearchGroup::getVehicles($cmd);
                $output = '';
                $select = 1;
                if (!$cmd) {
                    $output .= '<option value="0">Select Company and Group</option>';
                } else
                foreach ($groups as $group) {
                    if ($val && $group->value == $val && $select) {
                        $output .= '<option value="'.$group->value.'" selected>'.$group->text.'</option>';
                        $select = 0;
                    } else {
                        $output .= '<option value="'.$group->value.'">'.$group->text.'</option>';
                    }
                }
                $data = $output;
                break;
            case 'driver':
                $groups = JFormFieldSearchGroup::getDrivers($cmd);
                $output = '';
                $select = 1;
                if (!$cmd) {
                    $output .= '<option value="0">Select Company and Group</option>';
                } else
                foreach ($groups as $group) {
                    if ($val && $group->value == $val && $select) {
                        $output .= '<option value="'.$group->value.'" selected>'.$group->text.'</option>';
                        $select = 0;
                    } else {
                        $output .= '<option value="'.$group->value.'">'.$group->text.'</option>';
                    }
                }
                $data = $output;
                break;
            case 'group':
                $groups = JFormFieldSearchGroup::getGroups($cmd);
                $output = '';
                foreach ($groups as $group) {
                    $output .= '<option value="'.$group->value.'">'.$group->text.'</option>';
                }
                $data = $output;
                break;
            case 'dgroup':
                $groups = JFormFieldSearchGroup::getDriverGroup($cmd);
                $output = '';
                $select = 1;
                foreach ($groups as $group) {
                    if ($cmd && $group->value && $select) {
                        $output .= '<option value="'.$group->value.'" selected>'.$group->text.'</option>';
                        $select = 0;
                    } else {
                        $output .= '<option value="'.$group->value.'">'.$group->text.'</option>';
                    }
                }
                $data = $output;
                break;
            case 'vgroup':
                $groups = JFormFieldSearchGroup::getVehicleGroup($cmd);
                $output = '';
                $select = 1;
                foreach ($groups as $group) {
                    if ($cmd && $group->value && $select) {
                        $output .= '<option value="'.$group->value.'" selected>'.$group->text.'</option>';
                        $select = 0;
                    } else {
                        $output .= '<option value="'.$group->value.'">'.$group->text.'</option>';
                    }
                }
                $data = $output;
                break;
            case 'company':
                $groups = JFormFieldSearchGroup::getCompany($cmd);
                $output = '';
                $select = 1;
                $db =& JFactory::getDBO();
                $query = 'select parent_entity_id from #__fleet_entity where id='.$cmd;
                $db->setQuery($query);
                $value = $db->loadResult();
                foreach ($groups as $group) {
                    if ($cmd && $group->value==$value && $select) {
                        $output .= '<option value="'.$group->value.'" selected>'.$group->text.'</option>';
                        $select = 0;
                    } else {
                        $output .= '<option value="'.$group->value.'">'.$group->text.'</option>';
                    }
                }
                $data = $output;
                break;
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
