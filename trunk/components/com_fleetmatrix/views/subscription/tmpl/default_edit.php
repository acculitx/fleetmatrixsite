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
        	    <dt><?php echo $this->form->getLabel('weight_id'); ?></dt>
        	    <dd><?php echo $this->form->getInput('weight_id'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('fuel_capacity'); ?></dt>
        	    <dd><?php echo $this->form->getInput('fuel_capacity'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('driver_id'); ?></dt>
        	    <dd><?php echo $this->form->getInput('driver_id'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('vin'); ?></dt>
        	    <dd><?php echo $this->form->getInput('vin'); ?></dd>
                <dt></dt><dd></dd>
                <dt></dt>
            	<dd><input type="hidden" name="option" value="com_fleetmatrix" />
            	    <input type="hidden" name="task" value="subscription.submit" />
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