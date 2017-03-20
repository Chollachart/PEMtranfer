<?php
require_once '../function.php';
$local_db = new db_class("localhost");
$company_source = $_POST['user_company_allowed']; 
if($company_source=="PEM"){$company_destination = "PEM1";}else{"PEM";}

?>
<h1><u>เปิดใบคำขอ</u></h1>
<hr>
<table class="table-main" style="background-color:#F8F8FF;">
	<tr>
		<td align="right" width="20%">ชื่อ : </td>
		<td width="80%">
			<?php
				echo $_POST['user_name']." (".$_POST['user_company'].")";
			?>
			<input type="hidden" id="create_userid" value="<?=$_POST['userid']?>">
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
		<td align="right" style="vertical-align:text-top;">รายละเอียด : </td>
		<td align="right"><div class="row"><div class="col-md-6"><textarea id="create_detail" class="form-control"></textarea></div></div></td>
	</tr>
	<tr>
		<td align="right">ไอเทม : </td>
		<td>
			<table id="create_table_item_add" class="table-small">
				<thead>
					<tr>
						<td>ไอเทมไอดี</td>
						<td width="20%">ไอเทม <?=$company_source?></td>
						<td width="20%">ไอเทม <?=$company_destination?></td>
						<td width="40%">คำอธิบาย</td>
						<td width="20%">จำนวนที่ต้องการ</td>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td width="20%"><select class="form-control" id="create_item_tranfer"><option value="">เลือกไอเทม</option></select></td>
						<td width="20%"><span id="span_item_code_desitation"></span></td>
						<td width="40%"><span id="span_item_des_desitation"></span></td>
						<td width="20%"><input id="create_item_qty" type="number" class="form-control" style="float:left;"><img style="float:left;cursor:pointer;" src="img/add_row.png" onclick="add_row_footer();" width="30" height="30"></td>
					</tr>
				</tfoot>
			</table>
		</td>
	</tr>
	<tr>
		<td align="right">Expact Date : </td>
		<td>
			<div class="row">
			  	<div class="col-md-6">
			  		<input type="text" id="create_expect_date" class="form-control">
				</div>
  			</div>
		</td>
	</tr>
	<tr>
		<td align="right">ฝ่ายเจ้าของไอเทม : </td>
		<td>
			<div class="row">
			  	<div class="col-md-6">
			  		<select id="create_div_destination" class="form-control">
					<?php
						$des_db = new db_class($company_destination);
			  			$arr_div_des=$des_db->query_data("select distinct(kstplcode) as div_code,oms25_0 as div_des from kstpl where oms25_0 is not null group by kstplcode,oms25_0",NULL); 
			  			$i=0;
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
			  	<div class="col-md-6">
			  		<div class="fileUpload btn btn-info">
                           <span>เลือกไฟล์</span>
                           <input type="file" name="create_attach_file" class="upload" />                     
                    </div>
                    <input uploadmatch="create_attach_file" placeholder="Choose File" disabled="disabled" /><br>
				</div>
  			</div>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<button class="btn btn-primary btn-lg" style="width:100%">เปิดใบคำขอ</button>
		</td>
	</tr>
</table>
<script>
var win_width = window.innerWidth;
var win_height = window.innerHeight;
var itemcode_obj = get_itemcode_array();
push_itemcode_to_selector();

$('#create_expect_date').datepicker({daysOfWeekDisabled: [0,6],format:'dd/mm/yyyy',autoclose: true,todayHighlight:true,language:'th',minViewMode: "0",startDate: new Date()});

var table_item_tranfer=$('#create_table_item_add').DataTable({"dom":'<t>',"columnDefs": [{"targets": [0],"visible": false,"searchable": false}],"bPaginate": false,"bSort":false});

function push_itemcode_to_selector(){
		for (var key in itemcode_obj) {
			  if (itemcode_obj.hasOwnProperty(key)) {
			    $('#create_item_tranfer').append($("<option></option>").attr("value",itemcode_obj[key]['atid']).text($.trim(itemcode_obj[key]['itemcode_'+$("#create_company_source_code").val()]))); 
			  }
		}
}
function add_row_footer(){
	if($('#create_item_tranfer').val()==""||$('#create_item_qty').val()==""){alert("กรุณาระบุข้อมูลให้ครบถ้วน"); return false;}
	row_node=table_item_tranfer.row.add(["1","2","3","4","5"]).draw().node();
}
function save_create(){
		$.ajax({
	      url: "ajaxData/save_create.php",
	      async: true,
	      dataType: "json",
	      type: "post",
	      data: {},
	      beforeSend: function(){
	      },
	      success: function (result) {
	        
	      }
  		});
}
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

</script>