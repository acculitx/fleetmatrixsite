<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

include(JPATH_COMPONENT . DS . 'models' . DS . 'search_controls.php');

?>

<select name="vehicle" id="vehicle">
<?php
    require_once(JPATH_COMPONENT . DS . 'models' . DS . 'fields' . DS . 'searchgroup.php');

    $ctrl = new JFormFieldSearchGroup();
    foreach ($ctrl->getVehicles() as $option) {
        $selected = '';
        if ($option->value == $this->vehicle) {
            $selected = ' selected';
        }
        echo '<option value="'.$option->value.'"'.$selected.'>'.$option->text.'</option>';
    }
?>
</select>

<script>
    function vehicleChange() {
        var val = jQuery('#vehicle').val();
        if (val != "0") {
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=vgroup&val='); ?>"+val;
            jQuery.getJSON(url, function(data) {
                jQuery('#company').html(data);
            });
        }
    }
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $('#vehicle').change(vehicleChange);
        vehicleChange($);
    });
</script>