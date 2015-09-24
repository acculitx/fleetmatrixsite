<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

include(JPATH_COMPONENT . DS . 'models' . DS . 'search_controls.php');

?>

<select name="driver" id="driver">
<?php
    require_once(JPATH_COMPONENT . DS . 'models' . DS . 'fields' . DS . 'searchgroup.php');

    $ctrl = new JFormFieldSearchGroup();
    $group = JRequest::getInt('group', 0);
    if (!$group) {
        echo '<option value="0">Select Company and Group</option>';
    } else
    foreach ($ctrl->getDrivers($group) as $option) {
        $selected = '';
        if ($option->value == $this->driver) {
            $selected = ' selected="selected"';
        }
        echo '<option value="'.$option->value.'"'.$selected.'>'.$option->text.'</option>';
    }
?>
</select>

<script>
    var driverChanged = 0;
    function driverChange() {
        var val = jQuery('#driver').val();
        var trend = jQuery('#trend').val();
        if ((val!=undefined && val!="0") && (trend == 'overall') && !driverChanged) {
            jQuery('#trend').val('all');
            driverChanged = 1;
        }
        if (val != "0" && val != undefined) {
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=dgroup&val='); ?>"+val;
            jQuery.getJSON(url, function(data) {
                jQuery('#group').html(data);
        var driver = $('#driver')
        if (driver && $('#group')==0) {
            $('#driver').hide();
        } else if (driver) {
            $('#driver').show();
        }

            });
        }
    }
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $('#driver').change(driverChange);
        driverChange($);
        var driver = $('#driver')
        if (driver && $('#group')==0) {
            $('#driver').hide();
        } else if (driver) {
            $('#driver').show();
        }
        groupChange();
    });
</script>

<!-- REPLACE_HERE -->
