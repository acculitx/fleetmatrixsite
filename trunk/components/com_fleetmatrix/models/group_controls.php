<form action="<?php echo JRequest::getVar('REQUEST_URL', '', 'server'); ?>" >

    
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
    var processCompany = true;
    function groupChange() {
        var val = jQuery('#group').val();
//        if (val != "0" && val != undefined && processCompany) {
//            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=group&val='); ?>"+val;
//            jQuery.getJSON(url, function(data) {
//                jQuery('#group').html(data);
//            });
//        }
        if (jQuery('#vehicle').length) {
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=vehicle&val='); ?>"+val;
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
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=select_driver&val='); ?>"+val;
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
        $('#company').change(function() {
            var val = $('#company').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=group&val='); ?>"+val;
            $.getJSON(url, function(data) {
                $('#group').html(data);
                var gval = $('#group').val();
                if (gval!=undefined && gval!="0") {
                    processCompany = false;
                    groupChange();
                    processCompany = true;
                }
            });
        });
    });
    jQuery(document).ready(function($) {
        $('#group').change(groupChange);
        var gval = $('#group').val();
        if (gval!=undefined && gval!="0") {
            processCompany = false;
            groupChange();
            processCompany = true;
        }
    });
</script>

<script>
    function handlePagination($) {
        var value = this.href;
        this.href = this.href + '&window=' + "7";
        this.href = this.href + '&company=' + jQuery('#company').val();
        this.href = this.href + '&group=' + jQuery('#group').val();
        this.href = this.href + '&vehicle=' + jQuery('#vehicle').val();
        this.href = this.href + '&driver=' + jQuery('#driver').val();
        if (jQuery('#task').length) {
            jQuery('#task').val('');
        }
    }
    function handlePaginationChange($) {
        handlePagination();
        jQuery('#update').click();
        return false;
    }
    

    jQuery(document).ready(function() {
        jQuery('.pagination a').click(handlePagination);
        jQuery('#update').click(handlePagination);
        jQuery('#limit').change(handlePaginationChange);
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
