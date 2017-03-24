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
if($company_source=="PEM"){$company_destination = "PEM1";}else{"PEM";}
$trans_id = $_GET['id'];

$q_reserve_old = "select * from reserve_transaction where atid=?";
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
		                <li role="menu" get-content="mapping_itemcode"><a href="#">จับคู่ไอเทม</a></li>
		                <!--<li role="separator" class="divider"></li>-->
		              </ul>
		            </li>
		          </ul>
		    	</div>      
      		</div>		     		
    </nav>
    <div class="content">
    	<div class="inner-content">
    		<h1><u>แก้ไขใบคำขอ</u></h1>
    		<input type="hidden" id="edit_last_status" value="<?=$arr_reserve['status']?>">
    		<input type="hidden" id="edit_last_page" value="<?=$_GET['rev'];?>">
			<hr>
			<table class="table-main" style="background-color:#F8F8FF;">
				<tr>
					<td align="right" width="20%">แก้ไขโดย : </td>
					<td width="80%">
						<?php
							echo $personDetail['PersonFnamethai']." ".$personDetail['PersonLnamethai']." (".$personDetail['CompanyCode'].")";
						?>
						<input type="hidden" id="edit_userid" value="<?=$personDetail['UserID']?>">
						<input type="hidden" id="edit_username" value="<?=$personDetail['PersonFnamethai']." ".$personDetail['PersonLnamethai'];?>">
					</td>
				</tr>
				<tr>
					<td align="right" >บริษัทผู้ขอ : </td>
					<td>
						<?php
							echo $company_source;
							
						?>
						<input type="hidden" id="edit_company_source_code" value="<?=$company_source?>">
						<input type="hidden" id="edit_company_destination_code" value="<?=$company_destination?>">
					</td>
				</tr>
				<tr>
					<td align="right">ฝ่ายผู้ขอ : </td>
					<td>
						<div class="row">
						  	<div class="col-md-6">
						  		<select id="edit_div_source" class="form-control">
								<?php
									$des_db=NULL; $arr_div_des=NULL; $i=0; $set_select = NULL;
									$des_db = new db_class($company_source);
						  			$arr_div_des=$des_db->query_data("select distinct(kstplcode) as div_code,oms25_0 as div_des from kstpl where oms25_0 is not null group by kstplcode,oms25_0",NULL); 
									while($i<sizeof($arr_div_des)){
										if($arr_reserve['source_div']==trim($arr_div_des[$i]["div_code"])){$set_select = "selected";}else{$set_select = NULL;}
										echo '<option value="'.trim($arr_div_des[$i]["div_code"]).'" '.$set_select.'>'.trim($arr_div_des[$i]["div_code"]).'-'.trim($arr_div_des[$i]["div_des"]).'</option>';
										$i++;
									}
								?>	
								</select>
							</div>
			  			</div>
					</td>
				</tr>
				<tr>
					<td align="right" >บริษัทเจ้าของไอเทม : </td>
					<td>
						<?php
							echo $company_destination;
							
						?>
						<input type="hidden" id="edit_company_source_code" value="<?=$company_source?>">
						<input type="hidden" id="edit_company_destination_code" value="<?=$company_destination?>">
					</td>
				</tr>
				<tr>
					<td align="right">ฝ่ายเจ้าของไอเทม : </td>
					<td>
						<div class="row">
						  	<div class="col-md-6">
						  		<select id="edit_div_destination" class="form-control">
								<?php
									$des_db=NULL; $arr_div_des=NULL; $i=0; $set_select = NULL;
									$des_db = new db_class($company_destination);
						  			$arr_div_des=$des_db->query_data("select distinct(kstplcode) as div_code,oms25_0 as div_des from kstpl where oms25_0 is not null group by kstplcode,oms25_0",NULL); 
									while($i<sizeof($arr_div_des)){
										if($arr_reserve['destination_div']==trim($arr_div_des[$i]["div_code"])){$set_select = "selected";}else{$set_select = NULL;}
										echo '<option value="'.trim($arr_div_des[$i]["div_code"]).'" '.$set_select.'>'.trim($arr_div_des[$i]["div_code"]).'-'.trim($arr_div_des[$i]["div_des"]).'</option>';
										$i++;
									}
								?>	
								</select>
							</div>
			  			</div>
					</td>
				</tr>
				<tr>
					<td align="right">สาเหตุที่ขอ : </td>
					<td>
						<div class="row">
						  	<div class="col-md-3">
						  		<select id="edit_reason" class="form-control">
								<?php
									$arr_cause=$local_db->query_data("select atid,des from cause",NULL); $i=0; $set_select = NULL;
									while($i<sizeof($arr_cause)){
										if($arr_reserve['cause_id']==$arr_cause[$i]["atid"]){$set_select = "selected";}else{$set_select = NULL;}
										echo '<option value="'.$arr_cause[$i]["atid"].'" '.$set_select.'>'.$arr_cause[$i]["des"].'</option>';
										$i++;
									}
								?>	
								</select>
							</div>
							<div class="col-md-3">
								<input id="edit_reason_detail" placeholder="CTR, PO, Customer" class="form-control" value="<?=$arr_reserve['cause_detail']?>">
							</div>
			  			</div>
					</td>
				</tr>
				<tr>
					<td align="right">ไอเทม : </td>
					<td>
						<table id="edit_table_item_add" class="tableStrikeout">
							<thead>
								<tr>
									<td>old id</td>
									<td>ไอเทมไอดี</td>
									<td width="20%">ไอเทม <?=$company_source?></td>
									<td width="20%">ไอเทม <?=$company_destination?></td>
									<td width="20%">คำอธิบาย</td>
									<td width="5%">Expect Date</td>
									<td width="10%">จำนวนที่ต้องการ</td>
									<td width="10%">โน้ต</td>
									<td width="5%">เพิ่ม/ลบ</td>
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
										echo '<td>'.$arr_q_item[$i]["itemcode_".$company_source].'</td>';
										echo '<td>'.$arr_q_item[$i]["itemcode_".$company_destination].'</td>';
										echo '<td>'.$arr_q_item[$i]["itemdes_".$company_source].'</td>';
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
							<tfoot>
								<tr>
									<td></td>
									<td></td>
									<td><select class="form-control" style="width:100%" id="edit_item_tranfer"><option value="">เลือกไอเทม</option></select></td>
									<td><span id="span_item_code_desitation"></span></td>
									<td><span id="span_item_des_desitation"></span></td>
									<td><input id="edit_expect_date" placeholder="วันที่" type="text" class="form-control"></td>
									<td>
										<input id="edit_item_qty" placeholder="จำนวน" type="text" class="form-control">
										
									</td>
									<td><input id="edit_note_item" placeholder="โน้ต" type="text" class="form-control"></td>
									<td><img style="cursor:pointer;" src="img/add_row.png" onclick="add_row_footer();" width="30" height="30"></td>
									<td></td>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:text-top;">ซีเรียล, โน้ต : </td>
					<td>
						<div class="row">
						  	<div class="col-md-6">
						  		<textarea id="edit_note" class="form-control"><?=$arr_reserve['note']?></textarea>
							</div>
			  			</div>
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
						echo '&nbsp;';
						echo '<img style="cursor:pointer;" title="ลบไฟล์" onclick="delete_file('.$arr_q_file[0]["atid"].')" src="img/delete.png" width="30" height="30">';
					}else{
					?>
						<div class="row">
						  	<div class="col-md-1">
						  		<div class="fileUpload btn btn-info">
			                           <span>เลือกไฟล์</span>
			                           <input type="file" name="edit_attach_file" class="upload" />                     
			                    </div>
							</div>
							<div class="col-md-5">
								<input class="form-control" uploadmatch="edit_attach_file" placeholder="Choose File" disabled="disabled" />
							</div>
			  			</div>
			  		<?php
			  		}
			  		?>
			  		</div>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<button class="btn btn-primary btn-lg" onclick="save_edit('<?=$trans_id?>');" style="width:100%">แก้ไขใบคำขอ</button>
					</td>
				</tr>
			</table>
    	</div>
    </div>
	</body>
<script>
var win_width = window.innerWidth;
var win_height = window.innerHeight;
var itemcode_obj = get_itemcode_array();
push_itemcode_to_selector();

$('#edit_item_qty').bind('keypress',function(e){var charCode = (e.which) ? e.which : e.keyCode; if (charCode > 31 && (charCode < 48 || charCode > 57)) { return false;}});
$('#edit_expect_date').datepicker({daysOfWeekDisabled: [0,6],format:'dd/mm/yyyy',autoclose: true,todayHighlight:true,language:'th',minViewMode: "0",startDate: new Date()});

var table_item_tranfer=$('#edit_table_item_add').DataTable({"dom":'<t>',"columnDefs": [{"targets": [0,1,9],"visible": false,"searchable": false}],"bPaginate": false,"bSort":false});

$("#edit_item_tranfer").change(function(){
	var atid_item = $(this).val();
	if(atid_item==""||atid_item==null){$("#span_item_des_desitation,#span_item_code_desitation").html('');}else{
		$("#span_item_des_desitation").html(itemcode_obj[atid_item]["itemdes_"+$("#edit_company_source_code").val()]);
		$("#span_item_code_desitation").html(itemcode_obj[atid_item]["itemcode_"+$("#edit_company_destination_code").val()]);
	}
	//$("#create_item_description").html(itemcode_obj[]);
});
$("input[type=file][class=upload]").change(function(){
  var path = $(this).val();
  var filename = path.replace(/^.*\\/, "");
  $("input[uploadmatch="+$(this).attr("name")+"]").val(filename);
});

$('#edit_table_item_add tbody').on( 'click', 'img.delete_row', function () {
	$(this).parents('tr').removeAttr('class').addClass('strikeout');
	var this_row_data = table_item_tranfer.row($(this).closest("tr")).data(); 
	this_row_data[9] = "0"; // set to delete
	table_item_tranfer.row($(this).closest("tr")).data(this_row_data).draw(); 
});

function push_itemcode_to_selector(){
		for (var key in itemcode_obj) {
			  if (itemcode_obj.hasOwnProperty(key)) {
			    $('#edit_item_tranfer').append($("<option></option>").attr("value",itemcode_obj[key]['atid']).text($.trim(itemcode_obj[key]['itemcode_'+$("#edit_company_source_code").val()]))); 
			  }
		}
}
function add_row_footer(){
	if($('#edit_item_tranfer').val()==""||$('#edit_item_qty').val()==""){alert("กรุณาระบุข้อมูลให้ครบถ้วน"); return false;}
	var data_item = itemcode_obj[$("#edit_item_tranfer option:selected").val()];
	//console.log(data_item);
	row_node=table_item_tranfer.row.add([null,data_item['atid'],data_item['itemcode_'+$("#edit_company_source_code").val()],data_item['itemcode_'+$("#edit_company_destination_code").val()],data_item['itemdes_'+$("#edit_company_source_code").val()],$("#edit_expect_date").val(),$("#edit_item_qty").val(),$("#edit_note_item").val(),'<img style="cursor:pointer;" class="delete_row" src="img/delete_row.png" onclick="delete_row(this);" width="30" height="30">','2']).draw().node();
}
function delete_row(this_html){
	table_item_tranfer.row($(this_html).parents('tr')).remove().draw();
}
function delete_file(trans_id){
	$(".file_attach_div").html('<div class="row"><div class="col-md-1"><div class="fileUpload btn btn-info"><span>เลือกไฟล์</span><input type="file" name="edit_attach_file" class="upload" /></div></div><div class="col-md-5"><input class="form-control" uploadmatch="edit_attach_file" placeholder="Choose File" disabled="disabled" /></div></div>');
	$("input[type=file][class=upload]").change(function(){
  		var path = $(this).val();
  		var filename = path.replace(/^.*\\/, "");
  		$("input[uploadmatch="+$(this).attr("name")+"]").val(filename);
	});
}

function save_edit(trans_id){
	var r = confirm("ยืนยันการแก้ไข");
	if(r==true){
		var rowCount=table_item_tranfer.column(0).data().length; var i=0; var array_item_insert = [];  var count_delete = 0;
		while(i<rowCount){
			var data_tr = table_item_tranfer.row(i).data();
			array_item_insert.push(data_tr);
			if(data_tr[9]=="0"){count_delete++;}
			i++;
		}
		//console.log(array_item_insert)
		
		if(array_item_insert.length==0){alert("กรุณาเพิ่มไอเทม"); return false;}
		else if(count_delete==rowCount){alert("ไม่สามารถแก้ไขได้ กรุณาตรวจสอบจำนวนไอเทม");}
		else{
			$.ajax({
		      url: "ajaxData/save_edit.php",
		      async: true,
		      dataType: "json",
		      type: "post",
		      data: {"trans_id":trans_id,"last_status":$("#edit_last_status").val(),"userid":$("#edit_userid").val(),"username":$("#edit_username").val(),"company_source":$("#edit_company_source_code").val(),
		      "company_destination":$("#edit_company_destination_code").val(),"reason":$("#edit_reason option:selected").val(),"reason_detail":$.trim($("#edit_reason_detail").val()),
		      "div_source":$("#edit_div_source option:selected").val(),
		      "div_destination":$("#edit_div_destination option:selected").val(),"note":$("#edit_note").val(),
		      "array_item_insert":array_item_insert
		  	  },
		      beforeSend: function(){
		      	$.isLoading({ text:"กำลังบันทึกการแก้ไข",position:"overlay"});
		      },
		      success: function (result) {
		        $.isLoading("hide");
		        console.log(result);
		        if(result!=null&&result[0]==true){
		        	save_file(trans_id);
		        	window.location = $("#edit_last_page").val()+'.php';
		        }
		      }
	  		});
		}
	}
}

function save_file(trans_id)
{
	if($('input[name=edit_attach_file]').length==1){
		//console.log("up or del");
		var form_data = new FormData();  
		var file_data_final = $('input[name=edit_attach_file]').prop('files')[0];   
		if(file_data_final!=undefined)
		{  	form_data.append('file_attach',file_data_final);		}

		$.ajax({
			url: 'ajaxData/edit_upload_file.php?trans_id='+trans_id, 
			dataType: 'text',  
			async:false,
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post',
			success: function(php_script_response){
				console.log(php_script_response);      
			}
		});
	}
}

</script>
</html>