<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

?>
    <h2>Manage Safety Controllers</h2>
    <br />

    <form class="form-validate" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="managecontrollers" name="managecontrollers">
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
            	    <input type="hidden" name="task" value="managecontrollers.submit" />
                </dd>
                <dt></dt>
                <dd>
                <button type="submit" class="button" value="Retire Device"><?php echo JText::_('Retire Device'); ?></button>
                <button type="submit" class="button" style="margin-right: 5px" value="Remove from Group"><?php echo JText::_('Remove from Group'); ?></button>
                <button type="submit" class="button" style="margin-right: 5px" value="Add to Group"><?php echo JText::_('Add to Group'); ?></button>
			                <?php echo JHtml::_('form.token'); ?>
                </dd>
        	</dl>
        </fieldset>
    </form>
    <div class="clr"></div>

<script>
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $('#jform_selectcompany').change(function() {
            var val = $('#jform_selectcompany').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=group&cmd='); ?>"+val;
            $.getJSON(url, function(data) {
                $('#jform_selectgroup').html(data);
            });
        });
    });
</script>
