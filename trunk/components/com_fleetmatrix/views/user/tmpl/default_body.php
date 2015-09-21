<?php
ob_start();
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
/*$run = mysql_query("INSERT INTO `fleetmatrix`.`giqwm_menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES ('747', 'top', 'View Alerts', '2012-08-09-23-01-29', '', 'admin/2012-08-09-23-01-29', 'component/fleetmatrix?view=userreports', 'url', '1', '478', '2', '0', '0', '0', '0000-00-00 00:00:00', '0', '5', '', '0', '".'{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1}'."', '94', '95', '0', '*', '0');");
echo $run;*/


	if(@$_GET['action'] == "update"){
		
		mysql_query("UPDATE fleet_reports SET report_type='".$_GET['report_type']."' WHERE id='".$_GET['report_id']."'");
	}
	
	if(@$_GET['action'] == "delete"){
		mysql_query("DELETE FROM fleet_reports WHERE id='".$_GET['report_id']."'");
	}
	
	
	if(@$_GET['action'] == "new"){

		$data =new stdClass();
		$data->user_id = $_GET['user_id'];
		$data->report_name = $_GET['report_name'];
		$data->report_data = $_SESSION['report_data'];
		$data->status = '1';
		//echo "<pre>"; print_r($data); exit;
		$db = JFactory::getDBO();
		$db->insertObject( 'fleet_reports', $data);
		

	}
	if(isset($_GET['type']) && !isset($_GET['duration'])){
	//echo "sammar";
	

	
	$query = mysql_query("SELECT * FROM fleet_reports WHERE user_id = '".$_GET['user_id']."' AND status = 1 order by id DESC");
	
	while($res = mysql_fetch_array($query)){
	?>
	<tr class="row">
		<td>
			<?php if($res['report_name'] == 'drivertrend'){
				echo "Driver Trend Report";
			}else if($res['report_name'] == 'vehicletrend'){
				echo "Vehicle MPG Trend Report";
			}else if($res['report_name'] == 'vigilancetrend'){
				echo "Driver Vigilance Report ";
			} ?>
		</td>
        <td>
       		<a href="component/fleetmatrix/?view=user&user_id=<?php echo $_GET['user_id'] ?>&type=report&report_type=0&report_name=<?php echo str_replace(" ","-",$res['report_name']); ?>&report_id=<?php echo $res['id']; ?>&action=update">Never</a> /
            <a href="component/fleetmatrix/?view=user&user_id=<?php echo $_GET['user_id'] ?>&type=report&report_type=1&report_name=<?php echo str_replace(" ","-",$res['report_name']); ?>&report_id=<?php echo $res['id']; ?>&action=update">Default</a> /
            <a href="component/fleetmatrix/?view=user&user_id=<?php echo $_GET['user_id'] ?>&type=report&report_type=2&report_name=<?php echo str_replace(" ","-",$res['report_name']); ?>&report_id=<?php echo $res['id']; ?>&action=update">Weekly</a> /
            <a href="component/fleetmatrix/?view=user&user_id=<?php echo $_GET['user_id'] ?>&type=report&report_type=3&report_name=<?php echo str_replace(" ","-",$res['report_name']); ?>&report_id=<?php echo $res['id']; ?>&action=update">Monthly</a> /
            <a href="component/fleetmatrix/?view=user&user_id=<?php echo $_GET['user_id'] ?>&type=report&report_type=4&report_name=<?php echo str_replace(" ","-",$res['report_name']); ?>&report_id=<?php echo $res['id']; ?>&action=update">Quarterly</a> /
            <a href="component/fleetmatrix/?view=user&user_id=<?php echo $_GET['user_id'] ?>&type=report&report_type=5&report_name=<?php echo str_replace(" ","-",$res['report_name']); ?>&report_id=<?php echo $res['id']; ?>&action=update">Annually</a>
        </td>

        
        
        
        <td>
			<?php 
			if($res['report_type'] == '0'){
				echo "Never";
			}else if($res['report_type'] == '1'){
				echo "Default";
			}else if($res['report_type'] == '2'){
				echo "Weekly";
			}else if($res['report_type'] == '3'){
				echo "Monthly";
			} else if($res['report_type'] == '4'){
				echo "Quarterly";
			} else if($res['report_type'] == '5'){
				echo "Annually";
			} ?>
		</td>
        
        
        <td>
			<a href="component/fleetmatrix/?view=user&user_id=<?php echo $_GET['user_id'] ?>&type=report&report_type=0&report_name=<?php echo str_replace(" ","-",$res['report_name']); ?>&report_id=<?php echo $res['id']; ?>&action=delete" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes("Are you sure to delete this report?")); ?>')"><?php echo "Delete"; ?></a>
		</td>
	</tr>
    
<?php }
		
	}else if(isset($_GET['type']) && isset($_GET['duration'])){
	
	$queryEmail = mysql_query("SELECT email FROM giqwm_users WHERE id= '".$_GET['user_id']."'");
	$email = mysql_fetch_array($queryEmail);
	//echo $email['email'];
	$query = mysql_query("SELECT title,id FROM giqwm_menu WHERE menutype = 'top' AND published = '1' AND parent_id = '468'");
	
	while($res = mysql_fetch_array($query)){
	?>
     <tr class="row">

		<td>
			<?php echo $res['title']; ?>
		</td>
        <td>
       		<a href="component/fleetmatrix/?view=user&user_id208&type=report&duration=0&report_title=<?php echo str_replace(" ","-",$res['title']); ?>&report_id=<?php echo $res['id']; ?>">Off (default)</a> /
            <a href="component/fleetmatrix/?view=user&user_id208&type=report&duration=1&report_title=<?php echo str_replace(" ","-",$res['title']); ?>&report_id=<?php echo $res['id']; ?>">Weekly</a> /
            <a href="component/fleetmatrix/?view=user&user_id208&type=report&duration=2&report_title=<?php echo str_replace(" ","-",$res['title']); ?>&report_id=<?php echo $res['id']; ?>">Monthly</a> /
            <a href="component/fleetmatrix/?view=user&user_id208&type=report&duration=3&report_title=<?php echo str_replace(" ","-",$res['title']); ?>&report_id=<?php echo $res['id']; ?>">Quarterly</a> /
            <a href="component/fleetmatrix/?view=user&user_id208&type=report&duration=4&report_title=<?php echo str_replace(" ","-",$res['title']); ?>&report_id=<?php echo $res['id']; ?>">Annually</a>
        </td>

        
	</tr>
    
<?php }
		
	}else{
	foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo $item->name; ?>
		</td>
        <td>
            <?php echo $item->title; ?>
        </td>
		<td>
			<?php echo $item->entity_name; ?>
		</td>
        
        <!--<td>
			<a href="component/fleetmatrix/?view=user&user_id=<?php echo $item->id; ?>&type=report"><?php echo "View reports" ?></a>
		</td>-->
       <?php /*?> <td>
			<a href="component/fleetmatrix/?view=userreports">View Alerts</a>
		</td><?php */?>
        
        
        <td>
            <a href="<?php echo JRoute::_($this->getRoute().'&id='.$item->id); ?>">Edit</a>
        </td>
	</tr>
    
<?php endforeach; 

	}?>