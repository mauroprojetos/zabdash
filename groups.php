<?php
require_once '../include/config.inc.php';
require_once '../include/hosts.inc.php';
require_once '../include/actions.inc.php';
include('config.php');

if(isset($_REQUEST['sel']) && $_REQUEST['sel'] != '' && $_REQUEST['sel'] == 1) {
	$group = $_POST['groupid'];
}	

$dbGroups = DBselect( 'SELECT * FROM groups WHERE groupid <> 1 ORDER BY name ASC'	);

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Language" content="pt-br">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv='refresh' content='90'>

<title>Zabbix Hosts Groups</title>

<link rel="icon" href="img/favicon.ico" type="image/x-icon" />
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/font-awesome.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>

<link href="inc/select2/select2.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="inc/select2/select2.js" language="javascript"></script>	

</head>

<body>

<div class="row col-md-12 col-sm-12" style="margin-top:50px; margin-bottom: 20px; background:#fff; float:none; margin-right:auto; margin-left:auto; text-align:center;">
		<span><h3><i class="fa fa-desktop"></i>  Zabbix Hosts Groups</h3></span>
		
		<form id="form1" name="form1" class="form_rel" method="post" action="groups.php?sel=1" style="margin-top:30px; margin-bottom: 20px;">		
			<select id='groupid' name='groupid[]' multiple style='width: 300px; height: 27px;' autofocus data-placeholder="<?php echo $labels['Select one or more groups']; ?>">				
				<option value='-1'> <?php echo _('All'); ?> </option>
				<?php
					while ($groups = DBFetch($dbGroups)) {
						echo "<option value='".$groups['groupid']."'>".$groups['name']."</option>\n";
										
					}							
				?>
			</select><br><br><p>
			<button type='button' class='btn btn-primary' onclick='javascript:this.form.submit();'><?php echo _('Apply'); ?></button>
			<button type='button' class='btn btn-primary' onclick="javascript:location.href='groups.php';"><?php echo _('Reset'); ?></button>
		</form>
		
<?php

if($group != '') {	
	
	//echo '<script language="javascript"> location.href="hosts.php?groupid='.implode(",",$group).'"; 
	echo '<script language="javascript"> window.open(\'hosts.php?groupid='.implode(",",$group).'\',\'_blank\'); </script>';
}

?>		
		
</div>

<script type="text/javascript">
	$("#groupid").select2();
</script>


</body>
</html>
