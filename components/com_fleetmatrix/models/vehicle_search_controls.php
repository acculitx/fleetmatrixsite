<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

include(JPATH_COMPONENT . DS . 'models' . DS . 'search_controls.php');

?>

<select name="vehicle" id="vehicle">
<?php
    require_once(JPATH_COMPONENT . DS . 'models' . DS . 'fields' . DS . 'searchgroup.php');

    $ctrl = new JFormFieldSearchGroup();
    $group = JRequest::getInt('group', 0);
    if (!$group) {
        echo '<option value="0">Select Company and Group</option>';
    } else
    foreach ($ctrl->getVehicles($group) as $option) {
        $selected = '';
        if ($option->value == $this->vehicle) {
            $selected = ' selected';
        }
        echo '<option value="'.$option->value.'"'.$selected.'>'.$option->text.'</option>';
    }
?>
</select>
<?php //echo $_POST['vehicle'] ?>
<script>
    function vehicleChange() {
        var val = jQuery('#vehicle').val();
        if (val != "0" && val != undefined) {
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=vgroup&val='); ?>"+val;
            jQuery.getJSON(url, function(data) {
                jQuery('#group').html(data);
            });
        }
    }
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $('#vehicle').change(vehicleChange);
		<?php if(!empty($_POST['vehicle'])){ ?>
		$('#vehicle').show();
		<?php }else{?>
		$('#vehicle').hide();
		<?php } ?>
        //vehicleChange($);
   
	
	
        /*$('#vehicle').change(driverChange);
        driverChange($);
        var vehicle = $('#vehicle')
        if (vehicle && $('#group')==0) {
            $('#vehicle').hide();
        } else if (vehicle) {
            $('#vehicle').show();
        }
        groupChange(); */
    
	});
</script>

<!-- REPLACE_HERE -->
