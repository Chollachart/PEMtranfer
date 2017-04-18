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
if($company_source=="PEM"){$company_destination = "PEM1";}else{ $company_destination = "PEM";}
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
    <div class="content" style="display:none;">
    	<div class="inner-content">
    		<h1><u>อนุมัติคำขอ</u></h1>
    		<input type="hidden" id="approve_last_status" value="<?=$arr_reserve['status']?>">
    		<input type="hidden" id="approve_trans_id" value="<?=$trans_id?>">
    		<input type="hidden" id="approve_user_source" value="<?=$arr_reserve['source_user']?>">
			<hr>
			<table class="table-main" style="background-color:#F8F8FF;">
				<tr>
					<td align="right" width="20%">ผู้เขียนคำขอ : </td>
					<td width="80%">
						<?php
							echo $arr_reserve['source_user_name'];
						?>
						<input type="hidden" id="approve_userid" value="<?=$personDetail['UserID']?>">
						<input type="hidden" id="approve_username" value="<?=$personDetail['PersonFnamethai']." ".$personDetail['PersonLnamethai'];?>">
					</td>
				</tr>
				<tr>
					<td align="right" >บริษัทผู้ขอ : </td>
					<td><?=trim($arr_reserve['source_cmp']);?><input type="hidden" id="approve_company_source_code" value="<?=$company_source?>"><input type="hidden" id="approve_company_destination_code" value="<?=$company_destination?>">
					</td>
				</tr>
				<tr>
					<td align="right">ฝ่ายผู้ขอ : </td>
					<td>
						  		<?php
						  		echo trim($arr_reserve['source_div']);
						  		?>
					</td>
				</tr>
				<tr>
					<td align="right" >บริษัทเจ้าของไอเทม : </td>
					<td>
						<?php
							echo trim($arr_reserve['destination_cmp']);
						?>
					</td>
				</tr>
				<tr>
					<td align="right">ฝ่ายเจ้าของไอเทม : </td>
					<td>
						  		<?php
						  			echo trim($arr_reserve['destination_div']);
						  		?>
					</td>
				</tr>
				<tr>
					<td align="right">สาเหตุที่ขอ : </td>
					<td>
						<?php
							echo $arr_reserve['cause_des'];
							echo "<br>";
							echo $arr_reserve['cause_detail'];
						?>
					</td>
				</tr>
				<tr>
					<td align="right">ไอเทม : </td>
					<td>
						<table id="edit_table_item_add" class="tableStrikeout" width="100%">
							<thead>
								<tr>
									<td>old id</td>
									<td>Itemid</td>
									<td width="15%">Itemcode <?=$company_source?></td>
									<td width="22%">Description <?=$company_source?></td>
									<td width="15%">Itemcode <?=$company_destination?></td>
									<td width="22%">Description <?=$company_destination?></td>
									<td width="5%">Expect Date</td>
									<td width="8%">Quantity</td>
									<td width="8%">Note</td>
									<td width="5%">Add/Delete</td>
									<td width="">action</td>
								</tr>
							</thead>
							<tbody>
								<?php
								$q_item = "select ri.*,im.itemcode_PEM,im.itemcode_PEM1,im.itemdes_PEM,im.itemdes_PEM1 from reserve_item ri left join item_map im on ri.item_id = im.atid where reserve_transaction_id=?";
								$arr_q_item = $local_db->query_data($q_item,array($trans_id));
								$i=0;
								while($i<sizeof($arr_q_item)){
									echo '<tr>';
										echo '<td>'.$arr_q_item[$i]["atid"].'</td>';
										echo '<td>'.$arr_q_item[$i]["item_id"].'</td>';
										if($arr_q_item[$i]["itemcode_".$company_source]==""||$arr_q_item[$i]["itemcode_".$company_source]==NULL){
											echo '<td><a item-atid="'.$arr_q_item[$i]["item_id"].'" class="edit_match_itemcode">จับคู่ Itemcode</a></td>';
										}else{
											echo '<td>'.$arr_q_item[$i]["itemcode_".$company_source].'</td>';
										}
										echo '<td>'.$arr_q_item[$i]["itemdes_".$company_source].'</td>';
										echo '<td>'.$arr_q_item[$i]["itemcode_".$company_destination].'</td>';
										echo '<td>'.$arr_q_item[$i]["itemdes_".$company_destination].'</td>';
										echo '<td>'.$class_general->change_date_from_db_to_show_date($arr_q_item[$i]["expect_date"]).'</td>';
										echo '<td>'.$arr_q_item[$i]["qty"].'</td>';
										echo '<td>'.$arr_q_item[$i]["note_item"].'</td>';
										echo '<td><img style="cursor:pointer;" class="delete_row" src="img/delete_row.png" width="30" height="30"></td>';
										echo '<td>1</td>'; // action (0=delete,1=update,2=create)
									echo '</tr>';
									$i++;
								}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:text-top;">Serial, Note (<?=$arr_reserve['source_cmp'];?>) : </td>
					<td>
						<?=$arr_reserve['source_note']?>
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
				<tr>
					<td align="right" style="vertical-align:text-top;">Serial, Note (<?=$arr_reserve['destination_cmp'];?>) : </td>
					<td>
						<div class="row">
						  	<div class="col-md-6">
						  		<textarea class="form-control" id="approve_serial"></textarea>
							</div>
			  			</div>
					</td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:text-top;">Job : </td>
					<td>
						<div class="row">
						  	<div class="col-md-6">
						  		<input type="text" class="form-control" id="approve_job" value="">
							</div>
			  			</div>
					</td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:text-top;">ยืนยันวันที่ส่งมอบ : </td>
					<td>
						<div class="row">
						  	<div class="col-md-6">
						  		<input type="text" class="form-control" id="approve_delivery_date" value="" readonly>
							</div>
			  			</div>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<div class="row">
						  	<div class="col-md-6">
						  		<button class="btn btn-danger btn-lg" onclick="show_dialog_decline();" style="width:100%">ปฏิเสธคำขอ</button>
								
							</div>
							<div class="col-md-6">
								<button class="btn btn-success btn-lg" onclick="acknowledge_approve();" style="width:100%">อนุมัติคำขอ</button>
							</div>
						</div>
					</td>
				</tr>
			</table>
			<div id="dialog_all">
				    <div id="dialog_decline" title="ระบุเหตุผล">
				    	<div id="dialog_html_decline" ><input type="text" id="approve_decline_reason" class="form-control"></div>
				    </div>
				    <div id="dialog_edit_match" title="ระบุเหตุผล">
				    	<div>Itemcode ปลายทาง : <font size="4"><span id="dialog_span_itemcode_destination"></span></font></div><hr>
				    	<div id="dialog_html_edit_match" ><input type="text" com="<?=$company_source;?>" id="textbox_edit_match" class="form-control"></div>
				    </div>
			</div>
    	</div>
    </div>
	</body>
<script>
var win_width = window.innerWidth;
var win_height = window.innerHeight;

var table_item_tranfer=$('#edit_table_item_add').DataTable({"dom":'<t>',"columnDefs": [{"targets": [0,1,9,10],"visible": false,"searchable": false}],"bPaginate": false,"bSort":false});
$('#approve_delivery_date').datepicker({daysOfWeekDisabled: [0,6],format:'dd/mm/yyyy',autoclose: true,todayHighlight:true,language:'th',minViewMode: "0",startDate: new Date()});
$( "#dialog_decline" ).dialog({
            autoOpen: false,
            width: 700,
            height: 200,
            position: [((win_width/2)-350),((win_height/2)-100)],
            resizable: false,
            buttons: [
            {
                text: "ยืนยัน",
                click: function() {      
                  acknowledge_decline();
              }
            },
            {
                text: "ยกเลิก",
                click: function() {      
                   $("#dialog_decline").dialog("close");
              }
            }
          ]
});
$( "#dialog_edit_match" ).dialog({
            autoOpen: false,
            width: 700,
            height: 250,
            position: [((win_width/2)-350),((win_height/2)-125)],
            resizable: false,
            buttons: [
            {
                text: "ยืนยัน",
                click: function() {      
                	save_edit_itemmap();
              }
            },
            {
                text: "ยกเลิก",
                click: function() {      
                   $('#dialog_edit_match').dialog("close");
              }
            }
          ]
});
function acknowledge_approve(){
	if($("#edit_table_item_add").find("a.edit_match_itemcode").length>0){alert("กรุณาจับคู่ Itemcode"); return false;}
	if($("#approve_delivery_date").val()==""){alert("กรุณาระบุวันที่ยืนยันการส่งมอบ"); return false;}
	var r=confirm("ยืนยันการอนุมัติ");
	if(r==true){ 
		$.ajax({
			      url: "ajaxData/acknowledge_approve.php",
			      async: true,
			      dataType: "json",
			      type: "post",
			      data: {"trans_id":$("#approve_trans_id").val(),"username":$("#approve_username").val(),"userid":$("#approve_userid").val(),"approve_action":"true","approve_serial":$("#approve_serial").val(),"approve_job":$("#approve_job").val(),"approve_delivery_date":$("#approve_delivery_date").val(),"source_user":$("#approve_user_source").val()},
			      beforeSend: function(){
			      	$.isLoading({ text:"กำลังอนุมัติ",position:"overlay"});
			      },
			      success: function (result) {
			      	console.log(result);
			       	$.isLoading("hide");
			       	if(result[0]==true){
			        	window.location = "acknowledge_list.php";
			        }else{
			        	alert(result[1]);
			        }
			      }
		});
	}
}
function show_dialog_decline(){ jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", false).removeClass("ui-state-disabled"); $("#dialog_decline").dialog("open");}
function acknowledge_decline(){
	$.ajax({
		      url: "ajaxData/acknowledge_approve.php",
		      async: true,
		      dataType: "json",
		      type: "post",
		      data: {"trans_id":$("#approve_trans_id").val(),"username":$("#approve_username").val(),"userid":$("#approve_userid").val(),"approve_action":"false","decline_reason":$("#approve_decline_reason").val(),"source_user":$("#approve_user_source").val()},
		      beforeSend: function(){
		      	$.isLoading({ text:"กำลังตีกลับ",position:"overlay"});
		      },
		      success: function (result) {
		      	console.log(result);
		        $.isLoading("hide");
		        if(result[0]==true){
		        	window.location = "acknowledge_list.php";
		        }else{
		        	alert(result[1]);
		        }
		      }
	});
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($("#edit_table_item_add").find("a.edit_match_itemcode").length>0){get_itemcode($("#approve_company_source_code").val());}else{$(".content").show();}
var arr_autocomplete_itemcode_pem = null;
var arr_autocomplete_itemcode_pem1 = null;
var itematid_edit = null;
var itemcode_edit = null;
var itemdes_edit = null;
$("#textbox_edit_match[com=PEM]").autocomplete({
 	source: function(request, response) {
 		if(arr_autocomplete_itemcode_pem!=null){
        	var results = $.ui.autocomplete.filter(arr_autocomplete_itemcode_pem, request.term);
        	response(results.slice(0, 10));
    	}
    },
 	select : function(event, ui){
		//var value = arr_autocomplete_itemcode_pem[ui.item.value];	
		itemcode_edit=$.trim(ui.item.itemcode);	itemdes_edit=$.trim(ui.item.description);	
		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", false).removeClass("ui-state-disabled");
		
    },
    change: function (event, ui) {
    	if(!ui.item){$(this).val(""); 
    		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", true).addClass("ui-state-disabled"); 
    		itemdes_edit = null; itemcode_edit = null; 
    	}
    	else{
    		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", false).removeClass("ui-state-disabled");
    	}
    }
}).click(function() {
	    $(this).autocomplete('search',' ');
}).focusout(function(){
	if($(this).val()==""){
		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", true).addClass("ui-state-disabled"); 
	}
});

$("#textbox_edit_match[com=PEM1]").autocomplete({
 	source: function(request, response) {
 		if(arr_autocomplete_itemcode_pem1!=null){
        	var results = $.ui.autocomplete.filter(arr_autocomplete_itemcode_pem1, request.term);
        	response(results.slice(0, 10));
    	}
    },
 	select : function(event, ui){
		//var value = arr_autocomplete_itemcode_pem1[ui.item.value];
		itemcode_edit=$.trim(ui.item.itemcode);	itemdes_edit=$.trim(ui.item.description);	
		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", false).removeClass("ui-state-disabled");
    },
    change: function (event, ui) {
       	if(!ui.item){$(this).val(""); 
       	    jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", true).addClass("ui-state-disabled"); 
       		itemdes_edit = null; itemcode_edit = null;
       	}
       	else{
       		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", false).removeClass("ui-state-disabled");
       	}
    }
}).click(function() {
	    $(this).autocomplete('search',' ');
}).focusout(function(){
	if($(this).val()==""){
		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", true).addClass("ui-state-disabled"); 
	}
});
function get_itemcode(cmp_code){
	$.ajax({
		url: "ajaxData/get_itemcode_exact.php",
		async: true,
		dataType: "json",
		type: "post",
		data: {"cmp_code":cmp_code},
		beforeSend: function(){
			$.isLoading({ text:"กำลังโหลดข้อมูล ITEMCODE",position:"overlay"});
		},
		success: function (result) {
			console.log(result);
			$(".content").show();
			$.isLoading("hide");
			if(result[0]==true){
				if(cmp_code=="PEM"){

					arr_autocomplete_itemcode_pem = result[1];
				}else{
					arr_autocomplete_itemcode_pem1 = result[1];
				}
				
			}else{
				
				if(cmp_code=="PEM"){
					arr_autocomplete_itemcode_pem = null;
				}else{
					arr_autocomplete_itemcode_pem1 = null;
				}	
			}
			//console.log(arr_autocomplete_itemcode_pem);
		}
	});
}
$(".edit_match_itemcode").click(function(){
	$('#dialog_edit_match').dialog("close");
	$('#dialog_edit_match').dialog("open"); 
	$('div#dialog_html_edit_match input').val('').blur();
	itematid_edit=$(this).attr("item-atid");
	jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", true).addClass("ui-state-disabled");
});
function save_edit_itemmap(){
	$.ajax({
		url: "ajaxData/save_edit_match_itemcode.php",
		async: true,
		dataType: "json",
		type: "post",
		data: {"cmp_code":$("#approve_company_source_code").val(),"itematid_edit":itematid_edit,"itemcode_edit":itemcode_edit,"itemdes_edit":itemdes_edit},
		beforeSend: function(){
			$.isLoading({ text:"กำลังจับคู่ Itemcode",position:"overlay"});
		},
		success: function (result) {
			$.isLoading("hide");
			if(result[0]==false){
				alert(result[1]);
			}else{
				location.reload();
			}
			
		}
	});
}
</script>
</html>