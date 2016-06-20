<?php
include_once('defines.php');
include_once(__ROOT__.'/header.php');
include_once(__ROOT__.'/util/date_util.php');


$date_range = date_util::date_period();

?>


<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Inventory Stock Current</h4>
	</div>

  	<div class="panel-body">
		
		<form class="form-horizontal">

		  <div class="form-group">
		    <label for="branch_id" class="col-sm-1 control-label">Branch</label>
		    <div class="col-sm-10">      
		    	<select name="branch_id" id="branch_id" multiple="multiple">			
		  		</select>
		    </div>
		  </div>
  

		  <div class="form-group">
		    <div class="col-sm-offset-1 col-sm-10">
		      <button type="submit" class="btn btn-default" id="btn-submit">Search</button>
		    </div>		   
		  </div>
		</form>

		<table id="table-result" class="table table-bordered table-striped table-condensed">
			<thead style="background-color:#0066CC; color:#ffffff;" id="table-header">				
			</thead>	
			<tbody id="table-rows">
			</tbody>
		<table>
		
 	</div>


</div>



<script src="jquery/date_range.js"></script>
<script src="jquery/cb_branch.js"></script>
<!--<script src="jquery/cb_zero_val.js"></script>-->
<script src="jquery/custom.js"></script>


<script>


    $(document).ready(function() {

    	/*
    	scope.date_range.on_change(function(){    		
    	});
		*/

    	scope.cb_branch.get_cabangs();
    	//scope.cb_zero_val.init();

    	scope.submit.on_submit(function(){
    		query();
    	});


		function query(){
				
			/*
			var from_date = scope.date_range.get_from_date();
			var to_date = scope.date_range.get_to_date();

			if ( from_date == '' || to_date == '') {
				alert("Invalid date parameter!");
				return;
			}
			*/

			var my_url = 'svc/inv_stock_current_svc.php?';
			var cab_id = scope.cb_branch.get_cab_id();					
			//var zero_id = scope.cb_zero_val.get_zero_id();

			//my_url = my_url + 'cab_id=' + cab_id + '&from_date=' + from_date + '&to_date=' + to_date;
			my_url = my_url + 'cab_id=' + cab_id;

			scope.loading.on_loading(true);

			$.ajax({
				url: my_url,
				method:'get',
				dataType:'json',
				success : function(response) {

					if( response.status == true) {							


						var str_header = '<tr>';
						str_header += '<td rowspan="2" valign="middle" style="vertical-align: middle;">SKU</td>'
						str_header += '<td rowspan="2" valign="middle" style="vertical-align: middle;">Product</td>'
						str_header += '<td rowspan="2" valign="middle" style="vertical-align: middle;">Category</td>'
						str_header += '<td class="text-center">Total</td>'

						var selected_branches = scope.cb_branch.selected_branches2;
						for(var i=0;i<selected_branches.length;i++) {							
							var branch_name = selected_branches[i].split("|")[2];
							str_header += '<td colspan="2" class="text-center">' + branch_name + '</td>'
						}						
						str_header += '</tr>';

						str_header += '<tr>';
						str_header += '<td class="text-center">Qty.</td>';
						//str_header += '<td class="text-right">Amount (Qty x Current SellPrice)</td>';


						var total = [];
						for(var i=0;i<selected_branches.length;i++) {							
							var branch_name = selected_branches[i].split("|")[2];
							
							str_header += '<td class="text-center">Qty.</td>';
							//str_header += '<td class="text-right">Amount (Qty x Current SellPrice)</td>';
							str_header += '<td class="text-right">Last Updated</td>';

							total[branch_name] = [];
							total[branch_name]['qty'] = 0;
							total[branch_name]['amount'] = 0;
						}						


						total['all_branches'] = [];
						total['all_branches']['qty'] = 0;
						total['all_branches']['amount'] = 0;						
						str_header += '</tr>';

						$('#table-header').empty();
						$('#table-header').html(str_header);



						var str ='';
						for(var i=0;i<response.data.length;i++) {

							if (i%2==1) {
								css = 'class="info"';
							}
							else {
								css = '';
							}

							var row = response.data[i];

							var str_row='';
							str_row +='<tr ' + css + '>';
							str_row +='<td>' + row['sku'] + '</td>';
							str_row +='<td>' + row['product'] + '</td>';
							str_row +='<td>' + row['category'] + '</td>';
							str_row +='<td class="text-center">{{total_qty}}</td>';
							//str_row +='<td class="text-right">{{total_amount}}</td>';

							var total_qty = 0;
							var total_amount = 0;

							for(var cabang in row['cabang']) {

								var data = row['cabang'][cabang][0];
								if(data) {

									str_row +='<td class="text-center">' + Number(data['qty']).format_int() + '</td>';
									//str_row +='<td class="text-right">' + Number(data['amount']).format_decimal() + '</td>';
									str_row +='<td class="text-right">' + data['last_update'] + '</td>';

									total[cabang]['qty'] += Number(data['qty']);
									total[cabang]['amount'] += Number(data['amount']);

									total_qty +=  Number(data['qty']);
									total_amount +=  Number(data['amount']);

								}
								
							}


							total['all_branches']['qty'] += total_qty;
							total['all_branches']['amount'] += total_amount;

							str_row +='</tr>';
							str_row = str_row.replace("{{total_qty}}", Number(total_qty).format_int());
							str_row = str_row.replace("{{total_amount}}", Number(total_amount).format_decimal());
							str+=str_row;

						}


						var str_footer = '<tr style="background-color:#0066CC; color:#ffffff;">';	
						str_footer += '<td></td>';
						str_footer += '<td></td>';
						str_footer += '<td></td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['qty']).format_int() + '</td>';
						//str_footer += '<td class="text-right">' + Number(total['all_branches']['amount']).format_decimal() + '</td>';							


						for(var i=0;i<selected_branches.length;i++) {			

							var branch_name = selected_branches[i].split("|")[2];							

							str_footer += '<td class="text-center">' + Number(total[branch_name]['qty']).format_int() + '</td>';
							//str_footer += '<td class="text-right">' + Number(total[branch_name]['amount']).format_decimal() + '</td>';							
							str_footer += '<td></td>';

						}						
						str_footer += '</tr>';

						str += str_footer;


						$('#table-rows').empty();
	  					$('#table-rows').append(str);

	  					

	  					scope.loading.on_loading(false);

					}
					else {
						scope.loading.on_loading(false);
						alert(response.data);						
					}

				}	

			});

	  	}


  	});


</script>


<?php include_once('footer.php'); ?>
