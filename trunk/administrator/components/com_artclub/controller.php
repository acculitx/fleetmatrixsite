<?php
// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ArtclubsController extends JController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'save', 'save' );
		$this->registerTask( 'cancel', 'cancel' );
		$this->registerTask( 'upload', 'upload' );
		$this->registerTask( 'back', 'back');
	}

    /**
     * Method to display the view
     *
     * @access    public
     */
    function display()
    {
        parent::display();
    }

    function apply($data=null, $keywords=0)
    {
        $model = $this->getModel('artclubs');

        if ($model->store($data)) {
            $msg = JText::_( 'Entries Saved!' );
        } else {
            $msg = JText::_( 'Error Saving Entries '.$model->getError() );
        }

        $mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        $redir = 'index.php?option=com_artclub&limit=' . $limit .
            '&limitstart=' . $limitstart;

        $this->setRedirect( $redir, $msg );
    }

    function save() {
        $model = $this->getModel('artclubs');

        if ($model->store()) {
            $msg = JText::_( 'Entries Saved!' );
        } else {
            $msg = JText::_( 'Error Saving Entries '.$model->getError() );
        }

        $mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        $redir = 'index.php?option=com_artclub&model=artclubs&view=artclubs&limit=' . $limit .
            '&limitstart=' . $limitstart;

        $this->setRedirect( $redir, $msg );
    }

    function cancel()
    {
        $msg = JText::_( 'Operation Cancelled' );
        $mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        $redir = 'index.php?option=com_artclub&model=artclubs&view=artclubs&limit=' . $limit .
            '&limitstart=' . $limitstart;

        $this->setRedirect( $redir, $msg );
    }

    function upload()
    {
        JRequest::setVar( 'layout', 'upload'  );
        $this->display();
    }

    function back()
    {
        $msg = JText::_( '' );
        $mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        $redir = 'index.php?option=com_artclub&model=artclubs&view=artclubs&limit=' . $limit .
            '&limitstart=' . $limitstart;

        $this->setRedirect( $redir, $msg );
    }
}
