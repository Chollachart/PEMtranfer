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
		                <li role="menu"><a href="reserve_list.php">รายการคำขอ <?=$company_allowed?></a></li>
		                <li role="menu"><a href="acknowledge_list.php">รายการรอตอบรับ</a></li>
		              </ul>
		            </li>
		          	<li role="menu"><a href="transfer_list.php">รายการรอตัดโอน</a></li>
		            <li class="dropdown">
		              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">ตั้งค่า<span class="caret"></span></a>
		              <ul class="dropdown-menu">
		                <li role="menu" class="active"><a href="mapping_itemcode.php">จับคู่ไอเทม</a></li>
		                <!--<li role="separator" class="divider"></li>-->
		              </ul>
		            </li>
		          </ul>
		    	</div>      
      		</div>		    		
    </nav>
    <div class="content" style="display:none;">
    	<div class="inner-content">
    		<div style="float:right"><button id="button_add_match" class="btn btn-success btn-md">เพิ่ม</button></div>
    		<table id="table_match_item" class="table table-striped table-bordered" width="100%">
    			<thead>
    				<tr>
    					<td>Item ID</td>
    					<td width="14%">Itemcode <?=$company_source;?></td>
    					<td width="30%">Description <?=$company_source;?></td>
    					<td width="14%">Itemcode <?=$company_destination;?></td>
    					<td width="30%">Description <?=$company_destination;?></td>
    					<td width="8%">Status</td>
    					<td width="4%">Edit</td>
    				</tr>
    			</thead>
    			<tbody>
    			<?php
    			$all_item = $local_db->query_data("EXEC Get_itemcode_match @com='".$company_allowed."'",NULL);
				$i=0;
				if(is_array($all_item)&&sizeof($all_item)>0){
					while($i<sizeof($all_item)){
	    			?>
	    				<tr>
	    					<td><?=$all_item[$i]['atid'];?></td>
	    					<td><?=$all_item[$i]['itemcode_'.$company_source];?></td>
	    					<td><?=$all_item[$i]['itemdes_'.$company_source];?></td>
	    					<td><?=$all_item[$i]['itemcode_'.$company_destination];?></td>
	    					<td><?=$all_item[$i]['itemdes_'.$company_destination];?></td>
	    					<td><?=$all_item[$i]['status'];?></td>
	    					<td align="center"><img class="img_edit" item-atid="<?=$all_item[$i]['atid'];?>" src="img/edit_data.png" width="30" height="30"></td>
	    				</tr>
	    			<?php
	    			$i++;
	    			}
    			}
    			?>
    			</tbody>
    		</table>
    	</div>
    	<div id="dialog_all">
			<div id="dialog_add_match" title="เพิ่ม Itemcode ของ <?=$company_source;?>">
				<div id="dialog_html_add_match">
					<input type="text" class="form-control" company="<?=$company_source;?>" id="add_item_<?=$company_source;?>">
				</div>
			</div>
			<div id="dialog_edit_match" title="แก้ไข">
				<div id="dialog_html_edit_match">
					<div>Itemcode เดิม : <font size="4"><span id="old_itemcode_edit"></span></font>&nbsp;,จับคู่กับ : <font size="4"><span id="old_itemcode_edit2"></span></font></div><hr>
					<input type="text" class="form-control" role="edit_item" item-atid="" company="<?=$company_source;?>" id="edit_item_<?=$company_source;?>">
				</div>
			</div>
		</div>
    </div>
	</body>
<script type="text/javascript">
var table_match_item=$('#table_match_item').DataTable({"dom":'<f<t>p>',"columnDefs": [{"targets": [0],"visible": false,"searchable": false}],"bPaginate": false,"bSort":true,"aaSorting": [],"createdRow":function(row,data,index){
	//$(row).css('background-color', '#FFE4E1');
	if(data[5]=="Matched"){
		$(row).css('background-color', '#FFF');
	}else{
		$(row).css('background-color', '#FFCCCC');
	}
}});
var arr_autocomplete_itemcode_pem = null; get_itemcode($("#hidden_user_company_allowed").val());
var arr_autocomplete_itemcode_pem1 = null;
var itemcode_add = null;
var itemdes_add = null;
var itematid_edit = null;
var itemcode_edit = null;
var itemdes_edit = null;
$("#add_item_PEM,input[role=edit_item][company=PEM]").autocomplete({
 	source: function(request, response) {
 		if(arr_autocomplete_itemcode_pem!=null){
        	var results = $.ui.autocomplete.filter(arr_autocomplete_itemcode_pem, request.term);
        	response(results.slice(0, 10));
    	}
    },
 	select : function(event, ui){
		//var value = arr_autocomplete_itemcode_pem[ui.item.value];
		itemcode_add=$.trim(ui.item.itemcode);	itemdes_add=$.trim(ui.item.description);	
		itemcode_edit=$.trim(ui.item.itemcode);	itemdes_edit=$.trim(ui.item.description);	
		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", false).removeClass("ui-state-disabled");
		
    },
    change: function (event, ui) {
    	if(!ui.item){$(this).val(""); 
    		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", true).addClass("ui-state-disabled"); 
    		itemcode_add = null; itemdes_add = null;
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

$("#add_item_PEM1,input[role=edit_item][company=PEM1]").autocomplete({
 	source: function(request, response) {
 		if(arr_autocomplete_itemcode_pem1!=null){
        	var results = $.ui.autocomplete.filter(arr_autocomplete_itemcode_pem1, request.term);
        	response(results.slice(0, 10));
    	}
    },
 	select : function(event, ui){
		//var value = arr_autocomplete_itemcode_pem1[ui.item.value];
		itemcode_add=$.trim(ui.item.itemcode);	itemdes_add=$.trim(ui.item.description);	
		itemcode_edit=$.trim(ui.item.itemcode);	itemdes_edit=$.trim(ui.item.description);	
		jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", false).removeClass("ui-state-disabled");
    },
    change: function (event, ui) {
       	if(!ui.item){$(this).val(""); 
       	    jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", true).addClass("ui-state-disabled"); 
       		itemcode_add = null; itemdes_add = null;
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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$('#dialog_add_match').dialog({
            autoOpen: false,
            width: 700,
            height: 170,
            position: [((win_width/2)-350),((win_height/2)-85)],
            resizable: false,
            buttons: [
            {
                text: "ยืนยัน",
                disable:true,
                click: function() {      
                	add_item_match();
              }
            },
            {
                text: "ปิด",
                click: function() {      
                	$('#dialog_add_match').dialog("close");
              }
            }
          ]
});
$('#dialog_edit_match').dialog({
            autoOpen: false,
            width: 700,
            height: 250,
            position: [((win_width/2)-350),((win_height/2)-125)],
            resizable: false,
            buttons: [
            {
                text: "ยืนยัน",
                disable:true,
                click: function() {      
                	edit_item_match();
              }
            }
            /*,
            {
                text: "เลิกจับคู่",
                click: function() {      
                	delete_item_match();
              }
            }*/
            ,
            {
                text: "ปิด",
                click: function() {      
                	$('#dialog_edit_match').dialog("close");
              }
            }
          ]
});
$('#dialog_edit_match').on('dialogclose', function(event) {
    $("table#table_match_item  tbody  td  img.img_edit").attr("src","img/edit_data.png");
});
$("#button_add_match").click(function(){
	$('#dialog_add_match').dialog("close");
	itemcode_add = null;
	itemdes_add = null; 
	$('div#dialog_html_add_match input').val('');
	$('#dialog_add_match').dialog("open"); 
	jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", true).addClass("ui-state-disabled");
});
$("table#table_match_item  tbody  td  img.img_edit").click(function(){
	$('#dialog_edit_match').dialog("close");
	itematid_edit = $(this).attr("item-atid");
	itemcode_edit = null;
	itemdes_edit = null;
	$("div#dialog_html_edit_match > input").val('').attr("placeholder",$(this).closest('tr').find('td:eq(0)').html());
	$(this).attr("src","img/edit_data2.png");
	$('#dialog_edit_match').dialog("open");
	$("#old_itemcode_edit").html($(this).closest('tr').find('td:eq(0)').html());
	$("#old_itemcode_edit2").html($(this).closest('tr').find('td:eq(2)').html());
	jQuery(".ui-dialog-buttonpane button:contains('ยืนยัน')").attr("disabled", true).addClass("ui-state-disabled");
	//$("input[role=edit_item][company="+$('#hidden_user_company_allowed').val()+"]").autocomplete('search',$(this).closest('tr').find('td:eq(0)').html());
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
			//console.log(result);
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

function add_item_match(){
	$.ajax({
		url: "ajaxData/save_add_match_itemcode.php",
		async: true,
		dataType: "json",
		type: "post",
		data: {"cmp_code":$("#hidden_user_company_allowed").val(),"itemcode_add":itemcode_add,"itemdes_add":itemdes_add},
		beforeSend: function(){
			$.isLoading({ text:"กำลังบันทึก",position:"overlay"});
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
function edit_item_match(){
	console.log(itemcode_edit);
	console.log(itemdes_edit);
	console.log(itematid_edit);
	/*$.ajax({
		url: "ajaxData/save_edit_match_itemcode.php",
		async: true,
		dataType: "json",
		type: "post",
		data: {"cmp_code":$("#hidden_user_company_allowed").val(),"itematid_edit":itematid_edit,"itemcode_edit":itemcode_edit,"itemdes_edit":itemdes_edit},
		beforeSend: function(){
			$.isLoading({ text:"กำลังแก้ไข",position:"overlay"});
		},
		success: function (result) {
			$.isLoading("hide");
			if(result[0]==false){
				alert(result[1]);
			}else{
				location.reload();
			}
			
		}
	});*/
}
function delete_item_match(){
	$.ajax({
		url: "ajaxData/save_delete_match_itemcode.php",
		async: true,
		dataType: "json",
		type: "post",
		data: {"cmp_code":$("#hidden_user_company_allowed").val(),"itematid_edit":itematid_edit},
		beforeSend: function(){
			$.isLoading({ text:"กำลังยกเลิกการจับคู่",position:"overlay"});
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