<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');
jimport('joomla.html.pagination');

class AssignPagination extends JPagination
{
            public function getLimitBox()
        {
                $app = JFactory::getApplication();

                // Initialise variables.
                $limits = array();

                // Make the option list.
                for ($i = 5; $i <= 30; $i += 5)
                {
                        $limits[] = JHtml::_('select.option', "$i");
                }
                $limits[] = JHtml::_('select.option', '50', JText::_('J50'));
                $limits[] = JHtml::_('select.option', '100', JText::_('J100'));
                $limits[] = JHtml::_('select.option', '0', JText::_('JALL'));

                $selected = $this->_viewall ? 0 : $this->limit;

                // Build the select list.
                if ($app->isAdmin())
                {
                        $html = JHtml::_(
                                'select.genericlist',
                                $limits,
                                $this->prefix . 'limit',
                                'class="inputbox" size="1" onchange="Joomla.submitform();"',
                                'value',
                                'text',
                                $selected
                        );
                }
                else
                {
                        $html = JHtml::_(
                                'select.genericlist',
                                $limits,
                                $this->prefix . 'limit',
                                'class="inputbox" size="1" ',
                                'value',
                                'text',
                                $selected
                        );
                }
                return $html;
        }

}

class FleetMatrixModelAssignList extends FleetMatrixModelBaseList
{
    var $model_key = 'Assign';

	protected function populateState()
	{
		$app = JFactory::getApplication();
        $trend = JRequest::getCmd('trend', 'overall');
        $this->setState(strtolower($this->model_key).'.trend', $trend);

		parent::populateState();
	}

        public function getPagination()
        {
                // Get a storage key.
                $store = $this->getStoreId('getPagination');

                // Try to load the data from internal storage.
                if (isset($this->cache[$store]))
                {
                        return $this->cache[$store];
                }

                // Create the pagination object.
                jimport('joomla.html.pagination');
                $limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
                $page = new AssignPagination($this->getTotal(), $this->getStart(), $limit);

                // Add the object to the internal cache.
                $this->cache[$store] = $page;

                return $this->cache[$store];
        }

	protected function getListQuery()
	{
        $cmd = $this->getState(strtolower($this->model_key).'.cmd');
        $company = JRequest::getInt('company', 0);
        $group = JRequest::getInt('group', 0);
        $vehicle = JRequest::getInt('vehicle', 0);

		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

        $clause = "h.id, h.start_date as trip_start, h.end_date as trip_end, ".
                "(odo_end - odo_start) as miles, b.name as assigned_driver";

        $query = $query->select($clause)
            ->from('fleet_trip as h')
            ->leftJoin('#__fleet_subscription as c on h.subscriber_id = c.serial')
            ->join("LEFT OUTER", '#__fleet_trip_driver as d on h.id = d.trip_id')
            ->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
            ->leftJoin('#__fleet_entity as a on b.entity_id = a.id')
            ->order('h.end_date DESC')
            ->where('c.visible')
            ;

        if ($company) {
            $query = $query->where('a.parent_entity_id = "'.$company.'"');
        }
        if ($group) {
            $query = $query->where('a.id = "'.$group.'"');
        }
        if ($vehicle) {
            $query = $query->where('c.id = "'.$vehicle.'"');
        }

        #var_dump((string)$query);
		return $query;
	}

}

?>
