<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 * Keyword Exchange Model
 *
 */

if (FALSE !== strpos(gethostname(),'drydock')) {
    ini_set('display_errors', 1);
    error_reporting(E_ERROR);
}


require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_artclub'.DS.'tables'.DS.'artclubs.php');
jimport('joomla.html.pagination');

class ArtclubsModelArtclubs extends JModel
{
    /**
     * data array
     *
     * @var array
     */
    var $_data;
    var $_total = null;
    var $_pagination = null;

    function store($data=null)
    {
        JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
        JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
      	$auth =& JFactory::getACL();
       	$auth->addACL('com_artclub', 'store', 'users', 'super administrator');
        $auth->addACL('com_artclub', 'store', 'users', 'administrator');

        $user =& JFactory::getUser();
        if (!$user->authorize('com_artclub', 'store')) {
            $this->setError("Not authorized");
            return false;
        }

        if ($data == null) {
            $data = JRequest::get( 'post' );
        }

        $error = $this->validate($data);
        if ($error) {
            $this->setError($error);
            return false;
        }

        foreach ($data as $key => $val) {
            $item->$key = $val;
        }

        foreach ($_FILES as $file) {
            if (!$file['name']) { continue; }
            require_once('../functions.php');
            $image = new SimpleImage();
            $image->load($file['tmp_name']);
            $w = $image->getWidth();
            $h = $image->getHeight();
            if ($w > $h) {
                $image->resizeToWidth(500);
            } else {
                $image->resizeToHeight(500);
            }
            $image->save('../images/stories/artwork/'.$file['name'].'_big.jpg');
            if ($w > $h) {
                $image->resizeToWidth(200);
            } else {
                $image->resizeToHeight(200);
            }
           $image->save('../images/stories/artwork/'.$file['name'].'_med.jpg');
           $item->filename = $file['name'];
        }
        if (array_key_exists('allocatedimagename', $_REQUEST)) {
            $allocated = JRequest::getVar('allocatedimagename', NULL, 'post', 'path');
            if ($allocated) {
                $allocated = JPATH_BASE . $allocated;
                if (file_exists($allocated)) {
                    $new = str_replace('thumb200_', '', $allocated) . '_med.jpg';
                    $new = str_replace('/tmp/', '/images/stories/artwork/', $new);
                    rename($allocated, $new);
                    $allocated = str_replace('200_', '500_', $allocated);
                    $new = str_replace('thumb500_', '', $allocated) . '_big.jpg';
                    $new = str_replace('/tmp/', '/images/stories/artwork/', $new);
                    rename($allocated, $new);
                    $allocated = str_replace('thumb500_', '', $allocated);
                    $item->filename = basename($allocated);
                    unlink($allocated);
                }
            }
        }

        $row =& $this->getTable();

        // Bind the form fields to the table
        if (!$row->bind($item)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    function __construct()
    {
        parent::__construct();

        $mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    /**
     * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */
    function _buildQuery($content_id = null)
    {
        $db =& JFactory::getDBO();
        $content_id = JRequest::getInt('content_id', NULL);
        $query = <<<QUERY
SELECT *
FROM #__chronoforms_upload
QUERY;
        if ($content_id) {
            $query .= ' where cf_id = ' . $db->quote($content_id) ;
        }

        return $query . ' order by print_date;';
    }

    /**
     * Retrieves the data
     * @return array Array of objects containing the data from the database
     */
    function getData()
    {
        // Lets load the data if it doesn't already exist
        if (empty( $this->_data ))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }

    static function sort_by_exhib($a, $b)
    {
        /* custom sort function since I used a string to hold the date to ease
          handling in the form. basically just converts the month back into a number
          and compares */
        $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $ma = array_search(substr($a->exhibition_date, 0, 3), $months);
        $mb = array_search(substr($b->exhibition_date, 0, 3), $months);

        if ($ma != $mb)
            return $mb - $ma;

        $va = 0;
        $vb = 0;

        if ($a->period == 'Modern & Contemporary') {
            $va += 99999;
        }
        if ($b->period == 'Modern & Contemporary') {
            $vb += 99999;
        }

        return ((int)$a->print_date + $va) - ((int)$b->print_date + $vb);
    }

    function _getList($query, $start, $limit)
    {
        /* Note by Bill: Not sure why the base doesn't handle the pagination
           arguments, but this gets around the issue for now. Check again after
           1.5.
        */
        $full_list = JModel::_getList($query);
        usort($full_list, array("ArtclubsModelArtclubs", "sort_by_exhib"));

        $mode = JRequest::getCmd('layout', 'default');
        if ($mode != 'default') {
            return $full_list;
        }
        if ($full_list)
            return array_slice($full_list, $start, $limit);

        return array();
    }

    function getTotal()
    {
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }
     	return $this->_total;
    }

    function getPagination()
    {
        // Load the content if it doesn't already exist
     	if (empty($this->_pagination)) {
     	    $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
     	return $this->_pagination;
    }

    function validate($data) {
        $required = array('artist' => 1, 'title' => 1, 'print_date' => 1,
                          'category' => 1, 'dimensions' => 1, 'preservation' => 1,
                          'holding_gallery' => 1, 'period' => 1, 'exhibition_date' => 1);

        $db =& JFactory::getDBO();

        if ($data['cf_id']) {
            # cannot update locked items
            $query = "SELECT `catnum` FROM #__chronoforms_upload where cf_id = ". $db->quote((int)$data['cf_id']);
            $db->setQuery($query);
            $catum = $db->loadResult();
            if ($catnum) {
                return "Item is locked from further updates";
            }
        }

        # cannot add to locked exhibition
        $query = "SELECT `locked` FROM exhibitions where exhibition_date = ". $db->quote($data['exhibition_date']);
        $db->setQuery($query);
        $locked = $db->loadResult();

        if ($locked) {
            return 'Selected Exhibition has been locked';
        }

        foreach ($data as $key => $value) {
            # remove field from required. array should be empty at end
            if (array_key_exists($key, $required) && $value) {
                unset($required[$key]);
            }
            # validate field contents
            switch ($key) {
                case 'entry_price':
                    if (!is_numeric($value)) {
                        return "Invalid Entry Price";
                    }
                    break;
            }
        }

        if (count($required)) {
            return "All required fields are not met. Check: " . implode(", ", array_keys($required));
        }

        return NULL;
    }
}
?>
