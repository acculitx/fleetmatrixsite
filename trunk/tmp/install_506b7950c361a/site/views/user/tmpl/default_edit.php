<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

?>
    <h2><?php echo is_string($this->item) ? 'Add' : 'Edit'; ?> <?php echo $this->model_key; ?></h2>
    <br />

    <form class="form-validate" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="user" name="user">
		<fieldset>
        	<dl>
          	    <dt><?php echo $this->form->getLabel('username'); ?></dt>
             	<dd><?php echo $this->form->getInput('username'); ?></dd>
                <dt></dt><dd></dd>
          	    <dt><?php echo $this->form->getLabel('password'); ?></dt>
             	<dd><?php echo $this->form->getInput('password'); ?></dd>
                <dt></dt><dd></dd>
          	    <dt><?php echo $this->form->getLabel('confirmpassword'); ?></dt>
             	<dd><?php echo $this->form->getInput('confirmpassword'); ?></dd>
                <dt></dt><dd></dd>
          	    <dt><?php echo $this->form->getLabel('name'); ?></dt>
             	<dd><?php echo $this->form->getInput('name'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('phone'); ?></dt>
        	    <dd><?php echo $this->form->getInput('phone'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('fax'); ?></dt>
        	    <dd><?php echo $this->form->getInput('fax'); ?></dd>
                <dt></dt><dd></dd>
          	    <dt><?php echo $this->form->getLabel('email'); ?></dt>
             	<dd><?php echo $this->form->getInput('email'); ?></dd>
                <dt></dt><dd></dd>
          	    <dt><?php echo $this->form->getLabel('entity_type'); ?></dt>
             	<dd><?php echo $this->form->getInput('entity_type'); ?></dd>
                <dt></dt><dd></dd>
        	    <dt><?php echo $this->form->getLabel('entity_id'); ?></dt>
        	    <dd><?php echo $this->form->getInput('entity_id'); ?></dd>
                <dt></dt><dd></dd>
                <dt></dt>
            	<dd><input type="hidden" name="option" value="com_fleetmatrix" />
            	    <input type="hidden" name="task" value="user.submit" />
                    <?php if ($this->pk_name) : ?>
                    <input type="hidden" name="jform[<?php echo $this->pk_name; ?>]" value="<?php echo $this->pk_val; ?>" />
                    <?php else : ?>
                    <script>
                    jQuery(document).ready(function() {
                        jQuery('#jform_password').addClass('required');
                        jQuery('#jform_confirmpassword').addClass('required');
                        jQuery('#jform_password-lbl').text(jQuery('#jform_password-lbl').text()+'*')
                        jQuery('#jform_confirmpassword-lbl').text(jQuery('#jform_confirmpassword-lbl').text()+'*')
                    });
                    </script>
                    <?php endif; ?>
                </dd>
                <dt></dt>
                <dd><button type="submit" class="button" onclick="return Joomla.submitbutton('user.submit');"><?php echo JText::_('Submit'); ?></button>
			                <?php echo JHtml::_('form.token'); ?>
                </dd>
        	</dl>
        </fieldset>
    </form>
    <div class="clr"></div>

<script>
<?php
$path = JPATH_COMPONENT . DS . 'models' . DS . 'forms' . DS . 'user.js';
echo file_get_contents($path);
?>
</script>

<script>
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $('#jform_entity_type').change(function() {
            var val = $('#jform_entity_type').val();
            url = "<?php echo JRoute::_($this->getRoute().'&layout=json&format=raw&task=entity&cmd='); ?>"+val;
            $.getJSON(url, function(data) {
                $('#jform_entity_id').html(data);
            });
        });
    });
</script>