<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

class FleetMatrixController extends JController
{
    function display()
    {
        /* Allow modifying of the view */
        $view_type = JRequest::getCmd('view', 'fleetmatrix');
        $model_type = JRequest::getCmd('model', $view_type);

        $GLOBALS['user_companies'] = array();
        $GLOBALS['user_groups'] = array();

        $user =& JFactory::getUser();
        $db =& JFactory::getDBo();
        if ($user) {
            $query = $db->getQuery(true)
                ->select('entity_type, entity_id')
                ->from('#__fleet_user')
                ->where('id = "'.$user->id.'"')
                ;
            $db->setQuery($query);
            $row = $db->loadObject();
            if ($row) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from("#__fleet_entity")
                ->where('parent_entity_id = '.$row->entity_id)
                ;
            $db->setQuery($query);
            $results = $db->loadResultArray();
            switch ($row->entity_type) {
                case 1: // reseller
                    foreach($results as $id) {
                        $GLOBALS['user_companies'][] = $id;
                        $query = $db->getQuery(true)
                            ->select('id')
                            ->from("#__fleet_entity")
                            ->where('parent_entity_id = '.$id)
                            ;
                        $db->setQuery($query);
                        $groups = $db->loadResultArray();
                        foreach($groups as $gid) {
                            $GLOBALS['user_groups'][] = $gid;
                        }
                    }
                    break;
                case 2: // company
                    $GLOBALS['user_companies'][] = $row->entity_id;
                    foreach($results as $id) {
                        $GLOBALS['user_groups'][] = $id;
                    }
                    break;
                case 3: // group
                    $GLOBALS['user_groups'][] = $row->entity_id;
                    break;
                default: // ?? shouldn't happen
                    break;
            }
            }
        }

        if (!array_intersect($user->authorisedLevels(), array(2,3,4,8,16))) {
            return;
        }
        //var_dump($GLOBALS['user_companies']);
        //var_dump($GLOBALS['user_groups']);

        $layout = JRequest::getCmd('layout', 'html');

        $view = & $this->getView($view_type, $layout);

        $model = $this->getModel($model_type);
        $view->setModel($model, true);

        $edit_model = $this->getModel($model_type . 'edit');
        if ($edit_model) {
            $view->setEditModel($edit_model);
        }

        $list_model = $this->getModel($model_type . 'list');
        if ($list_model) {
            $view->setListModel($list_model);
        }

        $view->display();
    }


}
