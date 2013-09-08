<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

?>
    <h2>Manage Safety Controllers</h2>
    <br />

    <form class="form-validate" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="managedevices" name="managedevices">
		<fieldset>
        	<dl>
          	    <dt><?php echo $this->form->getLabel('serialnumbers'); ?></dt>
             	<dd><?php echo $this->form->getInput('serialnumbers'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('selectcompany'); ?></dt>
        	    <dd><?php echo $this->form->getInput('selectcompany'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('selectgroup'); ?></dt>
        	    <dd><?php echo $this->form->getInput('selectgroup'); ?></dd>
                <dt></dt><dd></dd>
                <dt></dt>
            	<dd><input type="hidden" name="option" value="com_fleetmatrix" />
            	    <input type="hidden" name="task" value="managedevices.submit" />
                </dd>
                <dt></dt>
                <dd>
                <button type="submit" name="submit" class="button remove" value="Retire Device"><?php echo JText::_('Retire Device'); ?></button>
                <button type="submit" name="submit" class="button remove" style="margin-right: 5px" value="Remove from Group"><?php echo JText::_('Remove from Group'); ?></button>
                <button type="submit" name="submit" class="button" style="margin-right: 5px" value="Add to Group"><?php echo JText::_('Add to Group'); ?></button>
			                <?php echo JHtml::_('form.token'); ?>
                </dd>
        	</dl>
        </fieldset>
    </form>
    <div class="clr"></div>

<script>
    function removalClick() {
        if (jQuery(this).hasClass('remove')) {
            return true;
        }
        if (jQuery('#jform_selectcompany').val()=='0') {
            alert('Please select a company');
            return false;
        }
        if (jQuery('#jform_selectgroup').val()=='0') {
            alert('Please select a group');
            return false;
        }
        return true;
    }
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $('.button').click(removalClick);
        $('#jform_selectcompany').change(function() {
            var val = $('#jform_selectcompany').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=group&cmd='); ?>"+val;
            $.getJSON(url, function(data) {
                $('#jform_selectgroup').html(data);
            });
        });
    });
    jQuery(document).ready(function($) {
        $('#jform_selectgroup').change(function() {
            var val = $('#jform_selectgroup').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=company&cmd='); ?>"+val;
            $.getJSON(url, function(data) {
                $('#jform_selectcompany').html(data);
            });
        });
    })
</script>
