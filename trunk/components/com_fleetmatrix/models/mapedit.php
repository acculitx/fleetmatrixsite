<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baseedit.php');

class FleetMatrixModelMapEdit extends FleetMatrixModelBaseEdit
{
    protected $table_name = '#__fleet_gps';
    var $model_key = 'Map';

}