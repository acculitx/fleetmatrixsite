<?php defined('_JEXEC') or die('Restricted access'); ?>

<div style="background-color: #D7D1C7;">
<form action="index.php" method="post" name="adminForm"  enctype="multipart/form-data" style="background-color: #D7D1C7;">

<?php $row = $this->items[0]; ?>

  <link href="/components/com_chronocontact/themes/default/css/style1.css" rel="stylesheet" type="text/css" />
                <!--[if lt IE 6]><link href="/components/com_chronocontact/themes/default/css/style1-ie6.css" rel="stylesheet" type="text/css" /><![endif]-->
                <!--[if lt IE 7]><link href="/components/com_chronocontact/themes/default/css/style1-ie7.css" rel="stylesheet" type="text/css" /><![endif]-->
                        <script type="text/javascript">
			var CF_LV_Type = 'default';			</script>
            <link rel="stylesheet" href="/components/com_chronocontact/css/calendar2.css" type="text/css" />
            <link href="/components/com_chronocontact/css/tooltip.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="/components/com_chronocontact/js/calendar2.js"></script>
            <script src="/components/com_chronocontact/js/livevalidation_standalone.js" type="text/javascript"></script>
            <link href="/components/com_chronocontact/css/consolidated_common.css" rel="stylesheet" type="text/css" />
			<script src="/components/com_chronocontact/js/customclasses.js" type="text/javascript"></script>

            <script type="text/javascript">
	Element.extend({
		getInputByName2 : function(nome) {
			el = this.getFormElements().filterByAttribute('name','=',nome)
			return (el)?(el.length)?el:el:false;
		}
	});
	window.addEvent('domready', function() {
																									});
</script>
        			                        <script type="text/javascript">
                window.addEvent('domready', function() {
                                });
            </script>
					<style type="text/css">
			span.cf_alert {
				background:#FFD5D5 url(/components/com_chronocontact/css/images/alert.png) no-repeat scroll 10px 50%;
				border:1px solid #FFACAD;
				color:#CF3738;
				display:block;
				margin:15px 0pt;
				padding:8px 10px 8px 36px;
			}
		</style>


                <script src="/components/com_chronocontact/js/jsvalidation2.js" type="text/javascript"></script>
        	<script type='text/javascript'>
				var fieldsarray = new Array();
				var fieldsarray_count = 0;window.addEvent('domready', function() {
				elementExtend();setValidation("ChronoContact_upload", 1, 0, 0);});</script>
        	<script type="text/javascript">
	elementExtend();
	window.addEvent('domready', function() {
		});
</script>

<img class="adminEditImage" src="<?php echo '/images/stories/artwork/' . $row->filename . '_med.jpg'; ?>"
            style="margin-left: 30px; margin-top: 10px;" />

<?php

# text boxes
foreach ($row as $key => $val) {
    $this->form_code = preg_replace("/(name=\"".$key."\")/", "$1 value=\"".$val."\"", $this->form_code);
}

require_once("../functions.php");
$gallery_options = get_gallery_drop_down_options(true, $row);

$this->form_code = preg_replace('/<!-- GALLERY_START.*GALLERY_END -->/s', $gallery_options, $this->form_code);

$this->form_code = preg_replace('/<!-- EXHIB_START.*EXHIB_END -->/s', planned_exhibitions($row->exhibition_date), $this->form_code);

#period
$this->form_code = preg_replace('/(option value=\"'.$row->period.'\")/', '$1 selected', $this->form_code);

# category
$this->form_code = preg_replace('/(option value=\"'.$row->category.'\")/', '$1 selected', $this->form_code);

# preservation
$this->form_code = preg_replace('/(textarea.*preservation)(.*)(<\/textarea>)/', '$1$2'.$row->preservation.'$3', $this->form_code);

# filename
$this->form_code = preg_replace('/(cf_fileinput cf_inputbox) required/', '$1', $this->form_code);
$this->form_code = preg_replace('/Upload an image/', 'Replace existing image', $this->form_code);

$approval_field = <<<APPROVAL
<div class="form_item">
  <div class="form_element cf_checkbox">
    <label class="cf_label" style="width: 100px;">Approved</label>
    <input class="cf_inputbox" title="" id="text_2" name="approved" type="checkbox" value="1" __APPROVAL_CHECKED__ />
  </div>
APPROVAL;

$checked = '';
if ($row->approved) { $checked = 'checked="checked"'; }
$approval_field = str_replace('__APPROVAL_CHECKED__', $checked, $approval_field);

$this->form_code = preg_replace('/<!-- SUBMIT_START.*SUBMIT_END -->/s', $approval_field, $this->form_code);

?>

<?php echo $this->form_code; ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="cf_id" value="<?php echo $row->cf_id; ?>" />
<input type="hidden" name="uid" value="<?php echo $row->uid; ?>" />
<input type="hidden" name="recordtime" value="<?php echo $row->recordtime; ?>" />
<input type="hidden" name="ipaddress" value="<?php echo $row->ipaddress; ?>" />
<input type="hidden" name="cf_user_id" value="<?php echo $row->cf_user_id; ?>" />
<input type="hidden" name="previous_filename" value="<?php echo $row->filename; ?>" />
<input type="hidden" name="option" value="com_artclub" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="artclubs" />
<input type="hidden" name="view" value="artclubs" />

</form>

</div>