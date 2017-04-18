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
$trans_id = $_GET['id'];

$q_reserve_old = "select rt.*,c.des as cause_des from reserve_transaction rt left join cause c on rt.cause_id=c.atid where rt.atid=?";
$arr_old_reserve = $local_db->query_data($q_reserve_old,array($trans_id));
if(!is_array($arr_old_reserve)||(is_array($arr_old_reserve)&&sizeof($arr_old_reserve)==0)){echo "ERROR !!!"; exit();}
$arr_reserve = $arr_old_reserve[0];

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
		          	<li role="menu" <?=($_GET['rev']=='index')?'class="active"':'';?>><a href="index.php">หน้าหลัก</a></li>
		          	<li class="dropdown">
		              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">ใบคำขอโอน<span class="caret"></span></a>
		              <ul class="dropdown-menu">
		                <li role="menu" <?=($_GET['rev']=='create_reserve')?'class="active"':'';?>><a href="create_reserve.php">สร้างใบคำขอ</a></li>
		                <li role="separator" class="divider"></li>
		                <li role="menu" <?=($_GET['rev']=='reserve_list')?'class="active"':'';?>><a href="reserve_list.php">รายการคำขอ <?=$company_allowed?></a></li>
		                <li role="menu" <?=($_GET['rev']=='acknowledge_list')?'class="active"':'';?>><a href="acknowledge_list.php">รายการรอตอบรับ</a></li>
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
    		<h1><u>ตัดโอน</u></h1>
    		<input type="hidden" id="transfer_last_status" value="<?=$arr_reserve['status']?>">
    		<input type="hidden" id="transfer_trans_id" value="<?=$trans_id?>">
			<hr>
			<input type="hidden" id="transfer_userid" value="<?=$personDetail['UserID']?>">
			<input type="hidden" id="transfer_username" value="<?=$personDetail['PersonFnamethai']." ".$personDetail['PersonLnamethai'];?>">
			<div align="left"><a id="hide_show_detail" style="font-size:16px">แสดงรายละเอียด</a></div>
			<table width="40%" id="transfer-table-detail" style="background-color:#F8F8FF;font-size:18px;display:none;">	
				<tr>
					<td width="40%" align="right" >บริษัท-ฝ่ายผู้ขอ : </td>
					<td width="60%">
					<?=trim($arr_reserve['source_cmp'])."-".trim($arr_reserve['source_div']);?>
					<input type="hidden" id="transfer_company_source_code" value="<?=$arr_reserve['source_cmp'];?>">
					<input type="hidden" id="transfer_company_destination_code" value="<?=$arr_reserve['destination_cmp'];?>">
					</td>
				</tr>
				<tr>
					<td align="right">บริษัท-ฝ่ายเจ้าของไอเทม : </td>
					<td>
					<?=trim($arr_reserve['destination_cmp'])."-".trim($arr_reserve['destination_div']);?>
					</td>
				</tr>
				<tr>
					<td align="right">สาเหตุที่ขอ : </td>
					<td>
						<?php
							echo $arr_reserve['cause_des']." รายละเอียด : ".$arr_reserve['cause_detail'];
						?>
					</td>
				</tr>
				
				<tr>
					<td align="right" style="vertical-align:text-top;">Serial, Note (<?=$arr_reserve['source_cmp'];?>) : </td>
					<td>
						<?=$arr_reserve['source_note']?>
					</td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:text-top;">Serial, Note (<?=$arr_reserve['destination_cmp'];?>) : </td>
					<td>
						<?=$arr_reserve['destination_note'];?>
					</td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:text-top;">Job : </td>
					<td>
						<?=$arr_reserve['job'];?>
					</td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:text-top;">วันที่ส่งมอบ : </td>
					<td>
						<?=$class_general->change_date_from_db_to_show_date($arr_reserve['confirm_deldate']);?>
					</td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:text-top;">ไฟล์แนบ : </td>
					<td>
					<div class="file_attach_div">
					<?php
					$q_file = "select * from file_upload where file_upload_trans_id=?";
					$arr_q_file = $local_db->query_data($q_file,array($trans_id));
					if(is_array($arr_q_file)&&sizeof($arr_q_file)>0){
						echo '<a href="'.$arr_q_file[0]['file_upload_path'].'">';
						echo $arr_q_file[0]['file_upload_name'];
						echo '</a>';
					}else{
						echo 'ไม่มีไฟล์แนบ';
			  		}
			  		?>
			  		</div>
					</td>
				</tr>
			</table>

			<table id="transfer_table_item_add" class="table-transfer" >
							<thead>
								<tr>
									<td>index</td>
									<td>old id</td>
									<td>Itemid</td>
									<td width="15%">Itemcode <?=$company_source?></td>
									<td width="18%">Description <?=$company_source?></td>
									<td width="15%">Itemcode <?=$company_destination?></td>
									<td width="18%">Description <?=$company_destination?></td>
									<td width="9%">Expect Date</td>
									<td width="8%">Quantity</td>
									<td width="8%">Note</td>
									<td width="6%">จำนวนที่ตัดแล้ว</td>
									<td width="3%">Your Ref.</td>

								</tr>
							</thead>
							<tbody>
								<?php
								$q_item = "select ri.*,(select ISNULL(SUM(ISNULL(yrf_qty,0)),0) from your_ref where yrf_item_id=ri.atid) as sum_qty from (select ri.*,im.itemcode_PEM,im.itemcode_PEM1,im.itemdes_PEM,im.itemdes_PEM1 from reserve_item ri left join item_map im on ri.item_id = im.atid where reserve_transaction_id=?) as ri";
								$arr_q_item = $local_db->query_data($q_item,array($trans_id));
								$i=0;
								while($i<sizeof($arr_q_item)){
									echo '<tr>';
										echo '<td>'.$i.'</td>';
										echo '<td>'.$arr_q_item[$i]["atid"].'</td>';
										echo '<td>'.$arr_q_item[$i]["item_id"].'</td>';
										echo '<td>'.$arr_q_item[$i]["itemcode_".$company_source].'</td>';
										echo '<td>'.$arr_q_item[$i]["itemdes_".$company_source].'</td>';
										echo '<td>'.$arr_q_item[$i]["itemcode_".$company_destination].'</td>';
										echo '<td>'.$arr_q_item[$i]["itemdes_".$company_destination].'</td>';
										echo '<td>'.$class_general->change_date_from_db_to_show_date($arr_q_item[$i]["expect_date"]).'</td>';
										echo '<td>'.$arr_q_item[$i]["qty"].'</td>';
										echo '<td>'.$arr_q_item[$i]["note_item"].'</td>';
										echo '<td><span class="span_already_transfer">'.number_format($arr_q_item[$i]["sum_qty"],4).'</span></td>';
										echo '<td><img style="cursor:pointer;" src="img/ref.png" width="30" index="'.$i.'" class="add_your_ref" height="30"></td>';			
									echo '</tr>';
									$i++;
								}
								?>
							</tbody>
			</table>
			<table width="100%">
				<tr>
					<td align="center" colspan="2">
						<div class="row">
						  	<div class="col-md-6">
						  		<button role="save_ref" class="btn btn-default btn-lg" onclick="save_your_ref();" style="width:100%"><img src="img/save.png" width="30" title="บันทึก" height="30"> <b>บันทึก</b></button>
								
							</div>
							<div class="col-md-6">
								<button role="close_transfer" class="btn btn-default btn-lg" onclick="close_transfer();" style="width:100%"><img src="img/folder.png" width="30" title="บันทึก" height="30"> <b>ปิดงาน</b></button>
							</div>
						</div>
					</td>
				</tr>
			</table>
			<div id="dialog_all">
				    <div id="dialog_your_ref">
				    	<div id="dialog_html_your_ref"></div>
				    </div>
			</div>
    	</div>
    </div>
	</body>
<script>
var table_item_transfer=$('#transfer_table_item_add').DataTable({"dom":'<t>',"columnDefs": [{"targets": [0,1,2],"visible": false,"searchable": false}],"bPaginate": false,"bSort":false});
var your_ref_obj = {}; 
get_old_your_ref();
console.log(your_ref_obj);
$('#approve_delivery_date').datepicker({daysOfWeekDisabled: [0,6],format:'dd/mm/yyyy',autoclose: true,todayHighlight:true,language:'th',minViewMode: "0",startDate: new Date()});
$('#dialog_your_ref').dialog({
            autoOpen: false,
            width: 700,
            height: 500,
            position: [((win_width/2)-350),((win_height/2)-250)],
            resizable: false,
            buttons: [
            {
                text: "ยืนยัน",
                click: function() {      
                	push_dialog_data_to_obj();
                	$("#dialog_your_ref").dialog("close");
              }
            }
          ]
});
$(".add_your_ref").click(function(){
	var this_row_data = table_item_transfer.row($(this).closest("tr")).data();
	var index = this_row_data[0];
	var itemcode = this_row_data[3];
	var item_atid = this_row_data[1];
	$('#dialog_your_ref').dialog('option', 'title','*** ระบุ Your Ref. ทางด้านซ้าย แล้วกด <img src="img/right.png" width="15" height="15"> ***');
	var array_ref = your_ref_obj["index_"+item_atid];
	$.ajax({
		url: "ajaxData/get_from_add_your_ref.php",
		async: true,
		dataType: "text",
		type: "post",
		data: {"index":index,"item_atid":item_atid,"itemcode":itemcode,"company_code":$("#transfer_company_destination_code").val(),"array_ref":array_ref},
		beforeSend: function(){
			$("#dialog_your_ref").dialog("open");
			$("#dialog_html_your_ref").isLoading({ text:"กำลังโหลด",position:"overlay"});
		},
		success: function (result) {
			$("#dialog_html_your_ref").html(result);
			$("#dialog_html_your_ref").isLoading("hide");
		}
	});
});
$("#hide_show_detail").click(function(){
  if($("#transfer-table-detail").css('display')=='none'){
    $("#transfer-table-detail").fadeIn();
  }else{
    $("#transfer-table-detail").fadeOut();
  }
});
function push_dialog_data_to_obj(){

	var array_ref = [];
	$("div.item_all div.row").each(function(){
		//if($.trim($(this).find('input[role=add_your_ref]').val())!=""&&$(this).find('input[role=add_your_ref]').attr('disabled')=="disabled"){
			array_ref.push([$('#hidden_itematid_yourref').val(),$('#hidden_itemcode_yourref').val(),$(this).find('span.ref_doc_number').html(),$(this).find('span.ref_your_qty').html()]);	
		//}
	});

	if(array_ref.length==0){
		delete your_ref_obj["index_"+$('#hidden_itematid_yourref').val()];	
		$("button[role=close_transfer]").attr("disabled","disabled");
	}
	else{
		your_ref_obj["index_"+$('#hidden_itematid_yourref').val()] = array_ref;
		$("button[role=close_transfer]").removeAttr("disabled");
	}
	//console.log(your_ref_obj);
	/////////////// 0 = item_id , 1 = itemcode , 2 = yourref , 3 = qty ///////////////////
}
function get_old_your_ref(){
	$.ajax({
		url: "ajaxData/get_old_your_ref.php",
		async: true,
		dataType: "json",
		type: "post",
		data: {'trans_id':$("#transfer_trans_id").val()},
		beforeSend: function(){
			$.isLoading({ text:"กำลังโหลด Your Ref.",position:"overlay"});
		},
		success: function (result) {
			//console.log(result[1]);
			if(result[0]==true){
				your_ref_obj = result[1];
			}else{
				your_ref_obj = {};
				$("button[role=close_transfer]").attr("disabled","disabled");
			}
			$.isLoading("hide");
		}
	});
}
function save_your_ref(){
	if(Object.keys(your_ref_obj).length!=0){
		var type_save = "edit";
	}else{
		var type_save = "delete";
	}
	
	var i=0;  var rowCount = table_item_transfer.column(0).data().length; 
		var item_id_arr = [];
		while(i<rowCount){
			var this_row_data = table_item_transfer.row(i).data();
			item_id_arr.push(parseInt(this_row_data[1])); 
			i++;
	}
	$.ajax({
			url: "ajaxData/save_your_ref.php",
			async: true,
			dataType: "text",
			type: "post",
			data: {"type_save":type_save,"username":$("#hidden_user_name").val(),"userid":$("#hidden_user_id").val(),"your_ref_obj":your_ref_obj,"item_id_arr":item_id_arr},
			beforeSend: function(){
				$(".content").isLoading({ text:"กำลังบันทึก",position:"overlay"});
			},
			success: function (result) {
				console.log(result);
				$(".content").isLoading("hide");
				location.reload();
			}
	});
}
function close_transfer(){
	console.log(Object.keys(your_ref_obj).length);
	console.log(your_ref_obj);
	if(Object.keys(your_ref_obj).length!=table_item_transfer.column(0).data().length){ // จำนวน your_ref obj น้อยกว่าจำนวนแถวของ Itemcode
			alert("กรุณาผูก Your Ref. ให้ครบทุกไอเทม");
	}else{
		save_your_ref();
		$.ajax({
			url: "ajaxData/save_close_transfer.php",
			async: true,
			dataType: "text",
			type: "post",
			data: {"username":$("#hidden_user_name").val(),"userid":$("#hidden_user_id").val(),"trans_id":$("#transfer_trans_id").val()},
			beforeSend: function(){
				$(".content").isLoading({ text:"กำลังบันทึก",position:"overlay"});
			},
			success: function (result) {
				console.log(result);
				$(".content").isLoading("hide");
				window.location = "transfer_list.php";
			}
		});
	}
}
</script>
</html>