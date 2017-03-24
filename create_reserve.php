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
$company_source = $company_allowed; 
if($company_source=="PEM"){$company_destination = "PEM1";}else{"PEM";}
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
		          	<li role="menu"><a href="index.php">หน้าหลัก</a></li>
		          	<li class="dropdown">
		              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">ใบคำขอโอน<span class="caret"></span></a>
		              <ul class="dropdown-menu">
		                <li role="menu" class="active"><a href="create_reserve.php">สร้างใบคำขอ</a></li>
		                <li role="separator" class="divider"></li>
		                <li role="menu"><a href="reserve_list.php">รายการคำขอ <?=$company_allowed?></a></li>
		                <li role="menu"><a href="acknowledge_list.php">รายการรอตอบรับ</a></li>
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
    		<h1><u>เปิดใบคำขอ</u></h1>
			<hr>
			<table class="table-main" style="background-color:#F8F8FF;">
				<tr>
					<td align="right" width="20%">ชื่อ : </td>
					<td width="80%">
						<?php
							echo $personDetail['PersonFnamethai']." ".$personDetail['PersonLnamethai']." (".$personDetail['CompanyCode'].")";
						?>
						<input type="hidden" id="create_userid" value="<?=$personDetail['UserID']?>">
						<input type="hidden" id="create_username" value="<?=$personDetail['PersonFnamethai']." ".$personDetail['PersonLnamethai'];?>">
					</td>
				</tr>
				<tr>
					<td align="right" >บริษัทผู้ขอ : </td>
					<td>
						<?php
							echo $company_source;
							
						?>
						<input type="hidden" id="create_company_source_code" value="<?=$company_source?>">
						<input type="hidden" id="create_company_destination_code" value="<?=$company_destination?>">
					</td>
				</tr>
				<tr>
					<td align="right">ฝ่ายผู้ขอ : </td>
					<td>
						<div class="row">
						  	<div class="col-md-6">
						  		<select id="create_div_source" class="form-control">
								<?php
									$des_db=NULL; $arr_div_des=NULL; $i=0;
									$des_db = new db_class($company_source);
						  			$arr_div_des=$des_db->query_data("select distinct(kstplcode) as div_code,oms25_0 as div_des from kstpl where oms25_0 is not null group by kstplcode,oms25_0",NULL); 
									while($i<sizeof($arr_div_des)){
										echo '<option value="'.trim($arr_div_des[$i]["div_code"]).'">'.trim($arr_div_des[$i]["div_code"]).'-'.trim($arr_div_des[$i]["div_des"]).'</option>';
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
						<input type="hidden" id="create_company_source_code" value="<?=$company_source?>">
						<input type="hidden" id="create_company_destination_code" value="<?=$company_destination?>">
					</td>
				</tr>
				<tr>
					<td align="right">ฝ่ายเจ้าของไอเทม : </td>
					<td>
						<div class="row">
						  	<div class="col-md-6">
						  		<select id="create_div_destination" class="form-control">
								<?php
									$des_db=NULL; $arr_div_des=NULL; $i=0;
									$des_db = new db_class($company_destination);
						  			$arr_div_des=$des_db->query_data("select distinct(kstplcode) as div_code,oms25_0 as div_des from kstpl where oms25_0 is not null group by kstplcode,oms25_0",NULL); 
									while($i<sizeof($arr_div_des)){
										echo '<option value="'.trim($arr_div_des[$i]["div_code"]).'">'.trim($arr_div_des[$i]["div_code"]).'-'.trim($arr_div_des[$i]["div_des"]).'</option>';
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
						  		<select id="create_reason" class="form-control">
								<?php
									$arr_cause=$local_db->query_data("select atid,des from cause",NULL); $i=0;
									while($i<sizeof($arr_cause)){
										echo '<option value="'.$arr_cause[$i]["atid"].'">'.$arr_cause[$i]["des"].'</option>';
										$i++;
									}
								?>	
								</select>
							</div>
							<div class="col-md-3">
								<input id="create_reason_detail" placeholder="CTR, PO, Customer" class="form-control">
							</div>
			  			</div>
					</td>
				</tr>
				<tr>
					<td align="right">ไอเทม : </td>
					<td>
						<table id="create_table_item_add" class="table-small">
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
								</tr>
							</thead>
							<tbody>
								
							</tbody>
							<tfoot>
								<tr>
									<td></td>
									<td></td>
									<td><select class="form-control" style="width:100%" id="create_item_tranfer"><option value="">เลือกไอเทม</option></select></td>
									<td><span id="span_item_code_desitation"></span></td>
									<td><span id="span_item_des_desitation"></span></td>
									<td><input id="create_expect_date" placeholder="วันที่" type="text" class="form-control"></td>
									<td>
										<input id="create_item_qty" placeholder="จำนวน" type="text" class="form-control">
										
									</td>
									<td><input id="create_note_item" placeholder="โน้ต" type="text" class="form-control"></td>
									<td><img style="cursor:pointer;" src="img/add_row.png" onclick="add_row_footer();" width="30" height="30"></td>
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
						  		<textarea id="create_note" class="form-control"></textarea>
							</div>
			  			</div>
					</td>
				</tr>
				<tr>
					<td align="right" style="vertical-align:text-top;">ไฟล์แนบ : </td>
					<td>
						<div class="row">
						  	<div class="col-md-1">
						  		<div class="fileUpload btn btn-info">
			                           <span>เลือกไฟล์</span>
			                           <input type="file" name="create_attach_file" class="upload" />                     
			                    </div>
							</div>
							<div class="col-md-5">
								<input class="form-control" uploadmatch="create_attach_file" placeholder="Choose File" disabled="disabled" />
							</div>
			  			</div>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<button class="btn btn-primary btn-lg" onclick="save_create();" style="width:100%">เปิดใบคำขอ</button>
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

$('#create_item_qty').bind('keypress',function(e){var charCode = (e.which) ? e.which : e.keyCode; if (charCode > 31 && (charCode < 48 || charCode > 57)) { return false;}});
$('#create_expect_date').datepicker({daysOfWeekDisabled: [0,6],format:'dd/mm/yyyy',autoclose: true,todayHighlight:true,language:'th',minViewMode: "0",startDate: new Date()});

var table_item_tranfer=$('#create_table_item_add').DataTable({"dom":'<t>',"columnDefs": [{"targets": [0,1],"visible": false,"searchable": false}],"bPaginate": false,"bSort":false});

$("#create_item_tranfer").change(function(){
	var atid_item = $(this).val();
	if(atid_item==""||atid_item==null){$("#span_item_des_desitation,#span_item_code_desitation").html('');}else{
		$("#span_item_des_desitation").html(itemcode_obj[atid_item]["itemdes_"+$("#create_company_source_code").val()]);
		$("#span_item_code_desitation").html(itemcode_obj[atid_item]["itemcode_"+$("#create_company_destination_code").val()]);
	}
	//$("#create_item_description").html(itemcode_obj[]);
});
$("input[type=file][class=upload]").change(function(){
  var path = $(this).val();
  var filename = path.replace(/^.*\\/, "");
  $("input[uploadmatch="+$(this).attr("name")+"]").val(filename);
});

function push_itemcode_to_selector(){
		for (var key in itemcode_obj) {
			  if (itemcode_obj.hasOwnProperty(key)) {
			    $('#create_item_tranfer').append($("<option></option>").attr("value",itemcode_obj[key]['atid']).text($.trim(itemcode_obj[key]['itemcode_'+$("#create_company_source_code").val()]))); 
			  }
		}
}
function add_row_footer(){
	if($('#create_item_tranfer').val()==""||$('#create_item_qty').val()==""){alert("กรุณาระบุข้อมูลให้ครบถ้วน"); return false;}
	var data_item = itemcode_obj[$("#create_item_tranfer option:selected").val()];
	//console.log(data_item);
	row_node=table_item_tranfer.row.add([null,data_item['atid'],data_item['itemcode_'+$("#create_company_source_code").val()],data_item['itemcode_'+$("#create_company_destination_code").val()],data_item['itemdes_'+$("#create_company_source_code").val()],$("#create_expect_date").val(),$("#create_item_qty").val(),$("#create_note_item").val(),'<img style="cursor:pointer;" class="delete_row" src="img/delete_row.png" onclick="delete_row(this);" width="30" height="30">']).draw().node();
	
}
function delete_row(this_html){
	table_item_tranfer.row($(this_html).parents('tr')).remove().draw();
}
function save_create(){
	var r = confirm("ยืนยันคำขอ");
	if(r==true){
		var rowCount=table_item_tranfer.column(0).data().length; var i=0; var array_item_insert = [];
		while(i<rowCount){
			var data_tr = table_item_tranfer.row(i).data();
			array_item_insert.push(data_tr);
			i++;
		}
		//console.log(array_item_insert);
		if(array_item_insert.length==0){alert("กรุณาเพิ่มไอเทม"); return false;}else{
			$.ajax({
		      url: "ajaxData/save_create.php",
		      async: true,
		      dataType: "json",
		      type: "post",
		      data: {"userid":$("#create_userid").val(),"username":$("#create_username").val(),"company_source":$("#create_company_source_code").val(),
		      "company_destination":$("#create_company_destination_code").val(),"reason":$("#create_reason option:selected").val(),"reason_detail":$.trim($("#create_reason_detail").val()),
		      "div_source":$("#create_div_source option:selected").val(),
		      "div_destination":$("#create_div_destination option:selected").val(),"note":$("#create_note").val(),
		      "array_item_insert":array_item_insert
		  	  },
		      beforeSend: function(){
		      	$(".content").isLoading({ text:"กำลังบันทึก",position:"overlay"});
		      },
		      success: function (result) {
		        $(".content").isLoading("hide");
		        console.log(result);
		        if(result!=null&&result[0]==true){
		        	save_file(result[1]);
		        	window.location = "reserve_list.php";
		        }
		      }
	  		});
		}
	}
}
function save_file(trans_id)
{
	var form_data = new FormData();  
	var file_data_final = $('input[name=create_attach_file]').prop('files')[0];   
	if(file_data_final!=undefined)
	{  	
		form_data.append('file_attach',file_data_final);		
		$.ajax({
			url: 'ajaxData/save_upload_file.php?trans_id='+trans_id, 
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