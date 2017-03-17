$(document).ready(function(){
	var itemData_store = getItemData();
});

$("li[role=presentation]").click(function(){
	$("li[role=presentation]").not(this).removeClass('active');
	$(this).addClass('active');
	getContent($(this).attr('get-content'),itemData_store);
});

function getItemData(){
	$.ajax({
            url: "ajaxData/getItem.php",
            async: true,
            dataType: "json",
            type: "post",
            data: {},
            beforeSend: function(){
            	$.isLoading({ text:"Loading Item Data",position:"overlay"});
            },
            success: function (result) {
            	//console.log(result);
            	$.isLoading("hide");
            	if(result!=null&&result[0]==true){
            		itemData_store = result[1];
            		getContent($("li[role=presentation][class=active]").attr('get-content'),itemData_store);
            	}else{
            		$(".content").html('Error !!');
            	}
            }
    }); 
}
function getContent(fileName,itemData){
	if(fileName=="getGraph"){    
            var item_all_array = get_itemcode_array(itemData);
            get_graph(item_all_array);
      }else{
            $.ajax({
                  url: "ajaxData/"+fileName+".php",
                  async: true,
                  dataType: "text",
                  type: "post",
                  data: {'itemData':itemData},
                  beforeSend: function(){
                        $(".content").html('').isLoading({ text:"Loading",position:"overlay"});
                  },
                  success: function (result) {

                        $(".content").isLoading("hide").html(result);
                  }
            }); 
      }
}

function get_graph(item_all_array){
    var itemcode_arr = item_all_array[0];
    var WH02_arr = item_all_array[1];
    var W103_arr = item_all_array[2];
    var set_height = itemcode_arr.length;
    $(".content").html('<div id="graph_container" style="min-width:50%; max-width: 90%; min-height:'+(set_height*30)+'px; margin: 0 auto"></div>');
    // console.log(itemData);
    Highcharts.chart('graph_container', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Stock Chart'
    },
    xAxis: {
            
        categories: itemcode_arr
        
    },
    yAxis: {
        min: 0,
        minRange: 10,
            plotLines: [{
          color: 'red', // Color value
          dashStyle: 'Solid', // Style of the plot line. Default to solid
          value: 30, // Value of where the line will appear
          width: 2 // Width of the line    
                  }],
        title: {
            text: 'Total Item in Stock'
        }
    },
    legend: {
        reversed: true
    },
    plotOptions: {
        series: {
            stacking: 'normal'
        }
    },
    series: [{
        name: 'WH02',
        turboThreshold: 10000,
        data: WH02_arr
    }, {
        name: 'W103',
        turboThreshold: 10000,
        data: W103_arr
    }]
});
$(".highcharts-credits").hide();
}

function get_itemcode_array(itemData){
      var arr_item_all = [];
      var arr_item = []; var arr_WH02 = []; var arr_W103 = []; var arr_USEPERSET = []; 
      itemData.forEach(function(element, index, array) {
            arr_item.push(element['Itemcode']);
            arr_USEPERSET.push(element['USE_PER_SET']);
            if(element['USE_PER_SET']!="0"&&element['USE_PER_SET']!=null){
              var WH02_SET = parseFloat(parseFloat(element['WH02_PC'])/parseFloat(element['USE_PER_SET']));
              var W103_SET = parseFloat(parseFloat(element['W103_PC'])/parseFloat(element['USE_PER_SET']));
              arr_WH02.push(WH02_SET); 
              arr_W103.push(W103_SET);
            }else{
              arr_WH02.push(0); 
              arr_W103.push(0);
            }
      });
      arr_item_all.push(arr_item); arr_item_all.push(arr_WH02); arr_item_all.push(arr_W103); arr_item_all.push(arr_USEPERSET);
      console.log(arr_item_all);
      return arr_item_all;
}