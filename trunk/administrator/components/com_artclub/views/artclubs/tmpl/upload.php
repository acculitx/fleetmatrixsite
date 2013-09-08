<?php defined('_JEXEC') or die('Restricted access'); ?>

<div style="background-color: #D7D1C7;">
<form action="index.php" method="post" name="adminForm"  enctype="multipart/form-data">

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

<?php

require_once("../functions.php");
$gallery_options = get_gallery_drop_down_options(true);

$this->form_code = preg_replace('/<!-- GALLERY_START.*GALLERY_END -->/s', $gallery_options, $this->form_code);

$this->form_code = preg_replace('/<!-- EXHIB_START.*EXHIB_END -->/s', planned_exhibitions(), $this->form_code);

$this->form_code = preg_replace('/<!-- SUBMIT_START.*SUBMIT_END -->/s', '', $this->form_code);
?>

<?php echo $this->form_code; ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_artclub" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="artclubs" />
<input type="hidden" name="view" value="artclubs" />

</form>

</div>