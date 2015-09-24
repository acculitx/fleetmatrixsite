<?php

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JHTML::_ ( 'behavior.framework', true );

// including base setup file
include_once (JPATH_ROOT . "/templates/" . $this->template . '/lib/php/tmptools.php');

$option = JRequest::getVar ( 'option' );
$view = JRequest::getVar ( 'view' );
$id = JRequest::getVar ( 'id' );

$home = 0;
if ($option == 'com_content' && $view == 'article' && $id == '81') {
	$home = 1;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
	xml:lang="<?php echo $this->language; ?>"
	lang="<?php echo $this->language; ?>"
	dir="<?php echo $this->direction; ?>">
<head>
<jdoc:include type="head" />
<link rel="shortcut icon"
	href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicon.ico" />
<link
	href='http://fonts.googleapis.com/css?family=Comfortaa:400,300,700&subset=latin,cyrillic-ext,latin-ext,cyrillic,greek'
	rel='stylesheet' type='text/css'>
	<link rel="stylesheet"
		href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_css.css"
		type="text/css" />
	<script
		src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"
		type="text/javascript"></script>
	<script type="text/javascript">  $.noConflict(); // Code that uses other library's $ can follow here.
</script>
	<script
		src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/tools.js"
		type="text/javascript"></script>
	</script>
	<script src="/includes/geoxml3.js"></script>

</head>


<body>
	<div id="top_wrapper">
<?php if($this->countModules('topmininav')) : ?>
<div id="topmininav_wrapper">
			<div id="topmininav">
				<jdoc:include type="modules" name="topmininav" />
			</div>
		</div>
<?php endif; ?>
<div id="top">
			<!-- logo -->
<?php if ($logo): ?>
<a title="<?php echo $app->getCfg('sitename'); ?>" href="index.php"> <img
				id="logo"
				src="<?php echo $this->baseurl ?>/<?php echo htmlspecialchars($logo); ?>"
				alt="<?php echo htmlspecialchars($templateparams->get('sitetitle'));?>"
				border="0" /></a>
<?php endif;?>
<?php if (!$logo ): ?>
	<h1 id="logo">
				<a href="index.php" title="<?php echo $app->getCfg('sitename'); ?>"><?php echo htmlspecialchars($templateparams->get('sitetitle'));?> <span><?php echo htmlspecialchars($templateparams->get('sitedescription'));?></span></a>
			</h1>
<?php endif;?>
<jdoc:include type="modules" name="user4" />
		</div>
		<div id="topmenu">
			<jdoc:include type="modules" name="position-1" />
		</div>

<?php if($this->countModules('position-2')) : ?>
<div id="topbottom">
			<jdoc:include type="modules" name="position-2" />
		</div>
<?php endif; ?>
</div>
<?php if ($home) : ?>
<div id="full">
<?php endif; ?>
<div id="main">

<?php if(($this->countModules('left') >= 1) && ($this->countModules('right') >= 1)) { ?>
<div id="sidebar-left">
				<jdoc:include type="modules" name="left" style="normal"
					headerLevel="3" />
			</div>
			<div id="contentlr">
				<jdoc:include type="message" />
				<jdoc:include type="component" />
			</div>
			<div id="sidebar-right">
				<jdoc:include type="modules" name="right" style="normal"
					headerLevel="3" />
			</div>
<?php } ?>
<?php if(($this->countModules('left') >= 1) && ($this->countModules('right') == 0)) { ?>
<div id="sidebar-left">
				<jdoc:include type="modules" name="left" style="normal"
					headerLevel="3" />
			</div>
			<div id="contentl">
				<jdoc:include type="message" />
				<jdoc:include type="component" />
			</div>
<?php } ?>
<?php if(($this->countModules('left') == 0) && ($this->countModules('right') >= 1)) { ?>
<div id="contentr">
				<jdoc:include type="message" />
				<jdoc:include type="component" />
			</div>
			<div id="sidebar-right">
				<jdoc:include type="modules" name="right" style="normal"
					headerLevel="3" />
			</div>
<?php } ?>
<?php if(($this->countModules('left') == 0) && ($this->countModules('right') == 0)) { ?>
<div id="contentfull">
				<jdoc:include type="message" />
				<jdoc:include type="component" />
			</div>
<?php } ?>

</div>

		<div id="bottom_wrapper">

		<?php if ($this->countModules('user1 or user2 or user5 or user6')) : ?>
			<div id="bottom_modules">
				<?php if ($this->countModules('user1')) : ?>
					<div id="user1-<?php echo $userwidth; ?>">
					<jdoc:include type="modules" name="user1" style="bottom" />
				</div>
				<?php endif; ?>
            
				<?php if ($this->countModules('user2')) : ?>
					<div id="user2-<?php echo $userwidth; ?>">
					<jdoc:include type="modules" name="user2" style="bottom" />
				</div>
				<?php endif; ?>
            
				<?php if ($this->countModules('user5')) : ?>
					<div id="user5-<?php echo $userwidth; ?>">
					<jdoc:include type="modules" name="user5" style="bottom" />
				</div>
				<?php endif; ?>
				<?php if ($this->countModules('user6')) : ?>
					<div id="user6-<?php echo $userwidth; ?>">
					<jdoc:include type="modules" name="user6" style="bottom" />
				</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		
</div>
		<div id="gotop">
			<a href="#"
				title="<?php echo htmlspecialchars($templateparams->get('backtotop'));?>"><?php echo htmlspecialchars($templateparams->get('backtotop'));?></a>
		</div>
		<div id="footer_wrapper">
			<div id="footer">
				<jdoc:include type="modules" name="user3" />
			</div>
			<div id="footbottom">
				<p id="cop">Copyright &copy; <?php $config = new JConfig(); echo $config->sitename; ?> <?php echo (date("Y")); ?></p>
				<!-- You CAN NOT remove (or unreadable) these links without our permission. When you don't want to have link back on the template footer, you have to pay 9 euro via PayPal: contact@templatesforjoomla.eu Please read license.txt 
<p id="author">Template by TFJ <a href="http://templatesforjoomla.eu" title="Free and Premium Joomla Templates" target="_blank">Joomla Templates</a> and template sponsor: <a href="http://www.pmo.pl/mala-architektura/kosze-na-smieci.html" title="PMO" target="_blank">kosze</a> - PMO.</p>
 You CAN NOT remove (or unreadable) these links without our permission. When you don't want to have link back on the template footer, you have to pay 9 euro via PayPal: contact@templatesforjoomla.eu Please read license.txt -->
			</div>
		</div>
<?php if ($home) : ?>
</div>
<?php endif; ?>
<jdoc:include type="modules" name="debug" />
</body>
</html>
