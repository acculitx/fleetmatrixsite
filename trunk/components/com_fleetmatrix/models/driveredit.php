<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baseedit.php');

class FleetMatrixModelDriverEdit extends FleetMatrixModelBaseEdit
{
    var $model_key = 'Driver';
    protected $table_name = '#__fleet_driver';

    function makeDataConversions($data) {
        $data = parent::makeDataConversions($data);
        $data['visible'] = $data['visible'] == 'checked' ? 1 : 0;
        return $data;
    }
}