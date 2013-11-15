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

    $company = JRequest::getInt('company', 0);
    foreach ($ctrl->getGroups($company) as $option) {
        $selected = '';
        if ($option->value == $this->group) {
            $selected = ' selected';
        }
        echo '<option value="'.$option->value.'"'.$selected.'>'.$option->text.'</option>';
    }
?>
</select>
<script>
    function groupChange() {
        var val = jQuery('#group').val();
       
//         if (val != "0" && val != undefined) {
//            url = "
  <?php// echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=group&val='); ?>"+val;
//             jQuery.getJSON(url, function(data) {
//             	alert(data);
//                 jQuery('#group').html(data);
//                 if (data!='<option value="0">Select a Group</option>') {
//                     jQuery('#group').show();
//                 } else {
//                     jQuery('#group').hide();
//                 }
//             });
//         }
        if (jQuery('#vehicle').length) {
            var cval = jQuery('#vehicle').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=vehicle&val='); ?>"+val+"&cval="+cval;
            jQuery.getJSON(url, function(data) {
                jQuery('#vehicle').html(data);
                
                if (data!='<option value="0">Select Company and Group</option>') {
                    jQuery('#vehicle').show();
                } else {
                    jQuery('#vehicle').hide();
                }
            });
        }
        if (jQuery('#driver').length) {
            var cval = jQuery('#driver').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=driver&val='); ?>"+val+"&cval="+cval;
            jQuery.getJSON(url, function(data) {
                jQuery('#driver').html(data);
                
                if (data!='<option value="0">Select Company and Group</option>') {
                    jQuery('#driver').show();
                } else {
                    jQuery('#driver').hide();
                }
            });
        }
    }
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        var val = $('#company').val();
        $('#company').change(function() {
            var val = $('#company').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=group&val='); ?>"+val;
            $.getJSON(url, function(data) {
                $('#group').html(data);
            });
        });
        if (jQuery('#vehicle').length && !$('#group').val()) {
            var cval = jQuery('#vehicle').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=vehicle&cpval='); ?>"+val+"&cval="+cval;
            jQuery.getJSON(url, function(data) {
                jQuery('#vehicle').html(data);
                if (data!='<option value="0">Select Company and Group</option>') {
                    jQuery('#vehicle').show();
                } else {
                    jQuery('#vehicle').hide();
                }
            });
        }
        if (jQuery('#driver').length && !$('#group').val()) {
            var cval = jQuery('#driver').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=driver&cpval='); ?>"+val+"&cval="+cval;
            jQuery.getJSON(url, function(data) {
                jQuery('#driver').html(data);
                if (data!='<option value="0">Select Company and Group</option>') {
                    jQuery('#driver').show();
                } else {
                    jQuery('#driver').hide();
                }
            });
        }
    });
    jQuery(document).ready(function($) {
        $('#group').change(groupChange);
        //groupChange($);
    });
</script>

<script>
    function handlePagination($) {
        var value = this.href;
        this.href = this.href + '&window=' + jQuery('#window_select').val();
        this.href = this.href + '&company=' + jQuery('#company').val();
        this.href = this.href + '&group=' + jQuery('#group').val();
        this.href = this.href + '&vehicle=' + jQuery('#vehicle').val();
        this.href = this.href + '&driver=' + jQuery('#driver').val();
    }

    jQuery(document).ready(function() {
        jQuery('.pagination a').click(handlePagination);
        jQuery('#update').click(handlePagination);
    });
</script>
<!-- REPLACE -->
<div id="submit_buttons">
<button id="update" type="submit" class="button"><?php echo JText::_('Update'); ?></button>
<button id="reset" type="submit" class="button"><?php echo JText::_('Reset'); ?></button>
</div>
<script>
jQuery(document).ready(function(){
    jQuery('#reset').click(function(){
        location.reload();
        return false;
    });
});
</script>
</form>
<!-- REPLACE_END -->
