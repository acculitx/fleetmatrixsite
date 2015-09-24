<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

?>
    <h2><?php echo is_string($this->item) ? 'Add' : 'Edit'; ?> <?php echo $this->model_key; ?></h2>
    <br />

    <form class="form-validate" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="driver" name="driver">
		<fieldset>
        	<dl>
          	    <dt><?php echo $this->form->getLabel('name'); ?></dt>
             	<dd><?php echo $this->form->getInput('name'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('visible'); ?></dt>
        	    <dd><?php echo $this->form->getInput('visible'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('company_nosave'); ?></dt>
        	    <dd><?php echo $this->form->getInput('company_nosave'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('entity_id'); ?></dt>
        	    <dd><?php echo $this->form->getInput('entity_id'); ?></dd>
                <dt></dt><dd></dd>
                <dt></dt>
            	<dd><input type="hidden" name="option" value="com_fleetmatrix" />
            	    <input type="hidden" name="task" value="driver.submit" />
                    <?php if ($this->pk_name) : ?>
                    <input type="hidden" name="jform[<?php echo $this->pk_name; ?>]" value="<?php echo $this->pk_val; ?>" />
                    <?php if ($this->item->visible) : ?>
                    <script>
                        jQuery(document).ready(function() {
                            jQuery('#jform_visible').attr('checked', 'checked');
                        });
                    </script>
                    <?php endif; ?>

                    <?php endif; ?>
                </dd>
                <dt></dt>
                <dd><button type="submit" class="button"><?php echo JText::_('Submit'); ?></button>
			                <?php echo JHtml::_('form.token'); ?>
                </dd>
        	</dl>
        </fieldset>
    </form>
    <div class="clr"></div>

<script>
    function entityChange() {
        var val = jQuery('#jform_entity_id').val();
        url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=company&cmd='); ?>"+val;
        jQuery.getJSON(url, function(data) {
            jQuery('#jform_company_nosave').html(data);
        });
    }
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $('#jform_company_nosave').change(function() {
            var val = $('#jform_company_nosave').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=group&cmd='); ?>"+val;
            $.getJSON(url, function(data) {
                $('#jform_entity_id').html(data);
            });
        });
    });
    jQuery(document).ready(function($) {
        $('#jform_entity_id').change(entityChange);
        entityChange($);
    });
</script>
