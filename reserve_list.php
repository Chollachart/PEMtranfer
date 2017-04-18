<?php
require_once 'SSO/SSO.php'; // นำเข้าไฟล์ Library
require_once 'function.php'; 
$APP_ID = 1703200827; // ไอดีของแอพพลิเคชั่น
// ตรวจสอบการล็อกอิน
$sso = new SSO($APP_ID);
$ssoResponse = $sso->getAuthentication();
$personDetail = $ssoResponse['personDetail']; // ข้อมูลพนักงาน
$panelLogout = $ssoResponse['panelLogout']; // html code แสดงปุ่มออกจากระบบ
if(is_array($personDetail['CompanyAllowed'])){
	if(array_search("PEM",$personDetail['CompanyAllowed'])!==false){
		$company_allowed = "PEM";
	}else if(array_search("PEM1",$personDetail['CompanyAllowed'])!==false){
		$company_allowed = "PEM1";
	}else if(array_search("PEM",$personDetail['CompanyAllowed'])!==false&&array_search("PEM1",$personDetail['CompanyAllowed'])!==false){
		$company_allowed = "PEM";
	}else{
		$company_allowed = "PEM";
		//echo '<script>alert("คุณไม่มีสิทธิ์เข้าถึงโปรแกรมนี้ กรุณาติดต่อ Admin");</script>'; exit();
	}
}else{
	$company_allowed=NULL;
	exit();
}
// แสดงข้อมูล
echo $panelLogout;
$local_db = new db_class("localhost");
$class_general = new general_class();
$company_source = $company_allowed; 
if($company_source=="PEM"){$company_destination = "PEM1";}else{$company_destination = "PEM";}
?>
<html>
	<head>
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<title>PEM Tranfer</title>
    	<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/isloading.css" rel="stylesheet">
		<link href="css/myStyle.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="datatable/datatable.bootstrap.css">
	    <link type="text/css" rel="stylesheet" href="datepicker/datepicker3.css" media="screen" />
	    <link href="dialog/css/black-tie/jquery-ui-1.9.2.custom.css" rel="stylesheet">
		<script type='text/javascript' src="js/jquery.js"></script>
	    <script type='text/javascript' src="js/bootstrap.js"></script>
	    <script src="dialog/js/jquery-ui-1.9.2.custom.js"></script>
	    <script type='text/javascript' src='js/jquery.isloading.js'></script>
	    <script type="text/javascript" src="datepicker/bootstrap-datepicker.js"></script>
	    <script type="text/javascript" src="datepicker/bootstrap-datepicker.th.js"></script>
	    <script type='text/javascript' src="js/myScript.js"></script>
	    <script type="text/javascript" language="javascript" src="datatable/jquery.dataTables.js"></script>
	    <script type="text/javascript" language="javascript" src="datatable/dataTables.tableTools.js"></script>
	    <script type="text/javascript" language="javascript" src="datatable/dataTables.bootstrap.js"></script>
	    <script type="text/javascript" src="js/wz_tooltip.js"></script>

	</head>
	<body>
		<input type="hidden" id="hidden_user_id" value="<?=$personDetail['UserID']?>">
		<input type="hidden" id="hidden_user_name" value="<?=$personDetail['PersonFnamethai']." ".$personDetail['PersonLnamethai']?>">
		<input type="hidden" id="hidden_user_email" value="<?=$personDetail['ExtEmail']?>">
		<input type="hidden" id="hidden_user_company" value="<?=$personDetail['CompanyCode']?>">
		<input type="hidden" id="hidden_user_company_allowed" value="<?=$company_allowed?>">
		<nav class="navbar navbar-default navbar-fixed-top">
	        <div class="container">
	        	<div class="navbar-header">
		          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
		            <span class="sr-only">Toggle navigation</span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		          </button>
		          <a class="navbar-brand" ><img src="img/LOGO_ART_PRECISE.png" width="120" height="20"></a>
		        </div>
		        <div id="navbar" class="navbar-collapse collapse">
		          <ul class="nav navbar-nav">
		          	<li role="menu"><a href="index.php">หน้าหลัก</a></li>
		          	<li class="dropdown">
		              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">ใบคำขอโอน<span class="caret"></span></a>
		              <ul class="dropdown-menu">
		                <li role="menu"><a href="create_reserve.php">สร้างใบคำขอ</a></li>
		                <li role="separator" class="divider"></li>
		                <li role="menu"  class="active"><a href="reserve_list.php">รายการคำขอ <?=$company_allowed?></a></li>
		                <li role="menu"><a href="acknowledge_list.php">รายการรอตอบรับ</a></li>
		              </ul>
		            </li>
		          	<li role="menu"><a href="transfer_list.php">รายการรอตัดโอน</a></li>
		            <li class="dropdown">
		              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">ตั้งค่า<span class="caret"></span></a>
		              <ul class="dropdown-menu">
		                <li role="menu"><a href="mapping_itemcode.php">จับคู่ไอเทม</a></li>
		                <!--<li role="separator" class="divider"></li>-->
		              </ul>
		            </li>
		          </ul>
		    	</div>      
      		</div>		    		
    </nav>
    <div class="content">
    	<div class="inner-content">
    		<table width="100%" id="table_reserve_list" class="table-main">
    			<thead>
    				<tr>
    					<td>id</td>
    					<td width="20%">แก้ไข</td>
    					<td width="15%">ร้องขอไปยัง</td>
    					<td width="10%">สาเหตุ</td>
    					<td width="20%">รายละเอียด</td>
    					<td width="20%">Note</td>
    					<td width="15%">สถานะ</td>
    				</tr>
    			</thead>
    			<tbody>
    				<?php
   					$q_trans = "select rt.*,c.des as cause_des,s.name as status_des from reserve_transaction rt left join cause c on rt.cause_id=c.atid left join status s on rt.status=s.atid where rt.source_cmp='".$company_allowed."' and rt.row_active=1 and (rt.status=1 or rt.status=2)";
   					$arr_trans=$local_db->query_data($q_trans,NULL);
    				$i=0;
    				while($i<sizeof($arr_trans)){
    					?>
    					<tr>
    						<td><?=$arr_trans[$i]["atid"];?></td>
    						<td><a onclick="edit_reserve(<?=$arr_trans[$i]["atid"];?>,'<?=basename(__FILE__, '.php');?>');"><img src="img/edit_data.png" width="30" height="30" title="แก้ไข" class="edit_data">&nbsp;<?=$arr_trans[$i]["reserve_no"]?></a></td>
    						<td><?=$arr_trans[$i]["destination_cmp"].'-'.$arr_trans[$i]["destination_div"];?></td>
    						<td><?=$arr_trans[$i]["cause_des"];?></td>
    						<td><?=$arr_trans[$i]["cause_detail"];?></td>
    						<td><?=$arr_trans[$i]["source_note"];?></td>
    						<td>
    							<?php
    								if($arr_trans[$i]["status"]=="1"){
    									echo $arr_trans[$i]["status_des"];
    								}else{
    									echo "ถูกตีกลับ ";
    									$q_reason_flow = "select TOP 1 f.atid,f.status_note FROM flow f where f.status=2 and reserve_id=".$arr_trans[$i]["atid"]." order by atid desc ";
    									$arr_reason_flow = $local_db->query_data($q_reason_flow,NULL);
    									if(is_array($arr_reason_flow)&&sizeof($arr_reason_flow)>0){
    										echo ($arr_reason_flow[0]['status_note']!=NULL)?" - ".$arr_reason_flow[0]['status_note']:"";
    									}
    								}
    							?>
    						</td>
    					</tr>
    					<?php
    					$i++;
    				}
    				?>
    			</tbody>
    		</table>
    	</div>
    </div>
	</body>
<script type="text/javascript">
var table_reserve_list=$('#table_reserve_list').DataTable({"dom":'<f<t>>',"columnDefs": [{"targets": [0],"visible": false,"searchable": false}],"bPaginate": true,"bPagelength":10,"bSort":true});

</script>
</html>