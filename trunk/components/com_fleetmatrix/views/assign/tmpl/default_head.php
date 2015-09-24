<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<div id="assignment_controls">
<div id="group_controls" class="fieldsbg">
<?php
include(JPATH_COMPONENT . DS . 'models' . DS . 'vehicle_group_controls.php');
?>
    
<select name="driver" id="driver">
<?php
    require_once(JPATH_COMPONENT . DS . 'models' . DS . 'fields' . DS . 'selectgroup.php');

    $ctrl = new JFormFieldSelectGroup();
    $group = JRequest::getInt('group', 0);
    if (!$group) {
        echo '<option value="0">Select Company and Group</option>';
    } else
    foreach ($ctrl->getDriver($group) as $option) {
        $selected = '';
        if ($option->value == $this->driver) {
            $selected = ' selected';
        }
        echo '<option value="'.$option->value.'"'.$selected.'>'.$option->text.'</option>';
    }
?>
</select>

    <input type="hidden" name="option" value="com_fleetmatrix" />
    <input type="hidden" id="task" name="task" value="assign.submit" />
    <button type="submit" id="submit" class="button"><?php echo JText::_('Submit'); ?></button>
</div>
</div>


    <?php echo JHtml::_('form.token'); ?>

<tr>
    <th width="5">
        <input type="checkbox" class="front_check_toggle" name="toggle" value="" />
    </th>
	<th>
        <?php echo JText::_('Trip Id'); ?>
    </th>
    <th>
		<?php echo JText::_('Trip Start'); ?>
	</th>
	<th>
		<?php echo JText::_('Trip End'); ?>
	</th>
	<th>
		<?php echo JText::_('Miles Driven'); ?>
	</th>
    <th>
        <?php echo JText::_('Assigned Driver'); ?>
    </th>
</tr>

<script>
    function handleSubmit() {
        var driver = jQuery('#driver').val();
        var cb = jQuery('.cb').is(':checked');
        if (driver=="0" && cb) {
            if (!confirm('One or more trips have been selected without selecting a driver. This will remove the current driver from those trips. Continue?')) {
                return false;
            }
        }
        return true;
    }
    jQuery(document).ready(function() {
        jQuery('#submit').click(handleSubmit);
        jQuery('.front_check_toggle').click(function() {
            if (jQuery('.front_check_toggle').is(':checked')) {
                jQuery('.cb').attr('checked', 'checked');
            } else {
                jQuery('.cb').removeAttr('checked');
            }
        });
        jQuery('#driver').hide();
    });
</script>