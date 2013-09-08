<form action="<?php echo JRequest::getVar('REQUEST_URL', '', 'server'); ?>" >
<select name="window" id="window_select">
    <option value="7" <?php if($this->window==7) {echo "selected";}?> >Last 7 days</option>
    <option value="1" <?php if($this->window==1) {echo "selected";}?> >Last 24 hours</option>
    <option value="30" <?php if($this->window==30) {echo "selected";}?> >Last 30 days</option>
    <option value="90" <?php if($this->window==90) {echo "selected";}?> >Last 90 days</option>
    <option value="180" <?php if($this->window==180) {echo "selected";}?> >Last 180 days</option>
    <option value="365" <?php if($this->window==365) {echo "selected";}?> >Last 365 days</option>
</select>

<select name="company" id="company">
<?php
    require_once(JPATH_COMPONENT . DS . 'models' . DS . 'fields' . DS . 'searchcompany.php');

    $ctrl = new JFormFieldSearchCompany();
    foreach ($ctrl->getCompanies() as $option) {
        $selected = '';
        if ($option->value == $this->company) {
            $selected = ' selected';
        }
        echo '<option value="'.$option->value.'"'.$selected.'>'.$option->text.'</option>';
    }
?>
</select>

<select name="group" id="group">
<?php
    require_once(JPATH_COMPONENT . DS . 'models' . DS . 'fields' . DS . 'searchgroup.php');

    $ctrl = new JFormFieldSearchGroup();
    foreach ($ctrl->getGroups() as $option) {
        $selected = '';
        if ($option->value == $this->group) {
            $selected = ' selected';
        }
        echo '<option value="'.$option->value.'"'.$selected.'>'.$option->text.'</option>';
    }
?>
</select>
<button type="submit" class="button"><?php echo JText::_('Update'); ?></button>

<script>
    function groupChange() {
        var val = jQuery('#group').val();
        if (val != "0") {
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=company&val='); ?>"+val;
            jQuery.getJSON(url, function(data) {
                jQuery('#company').html(data);
            });
        }
        if (jQuery('#vehicle').length) {
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=vehicle&val='); ?>"+val;
            jQuery.getJSON(url, function(data) {
                jQuery('#vehicle').html(data);
            });
        }
    }
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $('#company').change(function() {
            var val = $('#company').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=group&val='); ?>"+val;
            $.getJSON(url, function(data) {
                $('#group').html(data);
            });
        });
    });
    jQuery(document).ready(function($) {
        $('#group').change(groupChange);
        groupChange($);
    });
</script>
</form>

<script>
    function handlePagination($) {
        var value = this.href;
        this.href = this.href + '&window=' + jQuery('#window_select').val();
        this.href = this.href + '&company=' + jQuery('#company').val();
        this.href = this.href + '&group=' + jQuery('#group').val();
    }

    jQuery(document).ready(function() {
        jQuery('.pagination a').click(handlePagination);
    });
</script>