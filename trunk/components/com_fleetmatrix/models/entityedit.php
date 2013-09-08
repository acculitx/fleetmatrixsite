<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baseedit.php');

class FleetMatrixModelEntityEdit extends FleetMatrixModelBaseEdit
{
    protected $table_name = '#__fleet_entity';
    var $model_key = 'Entity';

}