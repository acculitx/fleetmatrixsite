<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

?>
    <h2><?php echo is_string($this->item) ? 'Add' : 'Edit'; ?> <?php echo $this->model_key; ?></h2>
    <br />

    <form class="form-validate" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="entity" name="entity">
		<fieldset>
        	<dl>
          	    <dt><?php echo $this->form->getLabel('name'); ?></dt>
             	<dd><?php echo $this->form->getInput('name'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('entity_type'); ?></dt>
        	    <dd><?php echo $this->form->getInput('entity_type'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('parent_entity_id'); ?></dt>
        	    <dd><?php echo $this->form->getInput('parent_entity_id'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('contact_name'); ?></dt>
        	    <dd><?php echo $this->form->getInput('contact_name'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('street_address'); ?></dt>
        	    <dd><?php echo $this->form->getInput('street_address'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('city'); ?></dt>
        	    <dd><?php echo $this->form->getInput('city'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('state'); ?></dt>
        	    <dd><?php echo $this->form->getInput('state'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('zip'); ?></dt>
        	    <dd><?php echo $this->form->getInput('zip'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('phone'); ?></dt>
        	    <dd><?php echo $this->form->getInput('phone'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('email'); ?></dt>
        	    <dd><?php echo $this->form->getInput('email'); ?></dd>
                <dt></dt><dd></dd>
                <dt></dt>
            	<dd><input type="hidden" name="option" value="com_fleetmatrix" />
            	    <input type="hidden" name="task" value="entity.submit" />
                    <?php if ($this->pk_name) : ?>
                    <input type="hidden" name="jform[<?php echo $this->pk_name; ?>]" value="<?php echo $this->pk_val; ?>" />
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
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $('#jform_entity_type').change(function() {
            var val = $('#jform_entity_type').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=parents&cmd='); ?>"+val;
            $.getJSON(url, function(data) {
                $('#jform_parent_entity_id').html(data);
            });
        });
    });
</script>