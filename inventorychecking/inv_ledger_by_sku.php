<?php
include_once('defines.php');
include_once(__ROOT__.'/header.php');
include_once(__ROOT__.'/util/date_util.php');

$date_range = date_util::date_period();

?>


<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Inventory Legder By SKU</h4>
	</div>

  	<div class="panel-body">
		
		<form class="form-horizontal">

		  
		  <div class="form-group">		  	
		    <label for="from_date" class="col-sm-1 control-label">Period</label>
		    <div class="col-sm-10">
		    	<div class="form-group">
			      	<div class="col-lg-2">			          
			        	<input type="date" class="form-control inline" name="from_date" id="from_date" placeholder="From Date" value="<?php echo $date_range[1]; ?>">			        
			      	</div> 
			      	<div class="col-lg-2">			          
		          		<input type="date" class="form-control inline" name="to_date" id="to_date" placeholder="To Date" value="<?php echo $date_range[0]; ?>">
		        	</div>
			    </div>
		    </div>
		  </div>
		  

		  <div class="form-group">
		    <label for="branch_id" class="col-sm-1 control-label">Branch</label>
		    <div class="col-sm-10">      
		    	<select name="branch_id" id="branch_id" multiple="multiple">			
		  		</select>
		    </div>		    
		  </div>


		  <div class="form-group">
		    <label for="sku" class="col-sm-1 control-label">SKU</label>
		    <div class="col-sm-4">      
		    	<input type="text" id="sku" name="sku" class="form-control" placeholder="e.g. 111ASP1100018"/>
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
		</table>
		
 	</div>


</div>




<div class="modal fade modal-inv-details" tabindex="-1" role="dialog" style="display:none;">

  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Inventory Details</h4>
      </div>

      <div class="modal-body inv-details">        	

      		<table class="table table-bordered table-striped table-condensed">
				<thead class="btn-primary">
					<tr>
						<th>#</th>
						<th>Stock Date</th>
						<th>Branch</th>					
						<th>SKU</th>
						<th>Product</th>
						<th class="text-center">Qty</th>
						<th>Reason</th>										
						<th>PO</th>	
						<th>Supplier</th>	
						<th>User</th>	
				  	</tr>	
				</thead>	

				<tbody>					
				</tbody>

			</table>

			<div>
				<p ><strong class="inv-details-info">Begin : 0 , Transaction : 0,  End : 0</strong></p>					
			</div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>        
      </div>


    </div>

  </div>

</div>



<script src="jquery/date_range.js"></script>
<script src="jquery/cb_branch.js"></script>
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
				
			
			var from_date = scope.date_range.get_from_date();
			var to_date = scope.date_range.get_to_date();

			if ( from_date == '' || to_date == '') {
				alert("Invalid date parameter!");
				return;
			}
			

			var my_url = 'svc/inv_ledger_by_sku_svc.php?';
			var cab_id = scope.cb_branch.get_cab_id();					
			var sku = '&sku=' + $('#sku').val();			

			my_url = my_url + 'cab_id=' + cab_id + '&from_date=' + from_date + '&to_date=' + to_date + sku;
			

			$('#table-header').empty();
			$('#table-rows').empty();


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
						str_header += '<td colspan="9" class="text-center">Total</td>'

						var selected_branches = scope.cb_branch.selected_branches2;
						for(var i=0;i<selected_branches.length;i++) {							
							var branch_name = selected_branches[i].split("|")[2];
							str_header += '<td colspan="9" class="text-center">' + branch_name + '</td>'
						}						
						str_header += '</tr>';

						str_header += '<tr>';
						str_header += '<td class="text-center">Begin</td>';
						str_header += '<td class="text-center">Purchase</td>';
						str_header += '<td class="text-center">Move-in</td>';
						str_header += '<td class="text-center">Move-out</td>';
						str_header += '<td class="text-center">Sales</td>';
						str_header += '<td class="text-center">Sales Return</td>';
						str_header += '<td class="text-center">Adjustment-in</td>';
						str_header += '<td class="text-center">Adjustment-out</td>';
						//str_header += '<td class="text-center">Total Transaction</td>';
						str_header += '<td class="text-center">End</td>';


						var total = [];
						for(var i=0;i<selected_branches.length;i++) {							
							var branch_name = selected_branches[i].split("|")[2];
							
							str_header += '<td class="text-center">Begin</td>';
							str_header += '<td class="text-center">Purchase</td>';
							str_header += '<td class="text-center">Move-in</td>';
							str_header += '<td class="text-center">Move-out</td>';
							str_header += '<td class="text-center">Sales</td>';
							str_header += '<td class="text-center">Sales Return</td>';
							str_header += '<td class="text-center">Adjustment-in</td>';
							str_header += '<td class="text-center">Adjustment-out</td>';
							//str_header += '<td class="text-center">Total Transaction</td>';
							str_header += '<td class="text-center">End</td>';


							total[branch_name] = [];
							total[branch_name]['begin'] = 0;
							total[branch_name]['purchase'] = 0;
							total[branch_name]['move-in'] = 0;
							total[branch_name]['move-out'] = 0;
							total[branch_name]['sales'] = 0;
							total[branch_name]['sales-return'] = 0;
							total[branch_name]['adjustment-in'] = 0;
							total[branch_name]['adjustment-out'] = 0;
							total[branch_name]['total-stock'] = 0;
							total[branch_name]['end'] = 0;

						}						


						total['all_branches'] = [];
						total['all_branches']['begin'] = 0;
						total['all_branches']['purchase'] = 0;
						total['all_branches']['move-in'] = 0;						
						total['all_branches']['move-out'] = 0;						
						total['all_branches']['sales'] = 0;						
						total['all_branches']['sales-return'] = 0;						
						total['all_branches']['adjustment-in'] = 0;						
						total['all_branches']['adjustment-out'] = 0;						
						total['all_branches']['end'] = 0;

						str_header += '</tr>';

						
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
							str_row +='<td class="text-center">{{total_begin}}</td>';
							str_row +='<td class="text-center"><a href="#" class="inv-sum" data-begin="{{total_begin}}" data-sku="' + row['sku'] + '" data-branch="' + cab_id + '" data-transaction="1" data-startdate="' + from_date + '" data-endate="' + to_date + '">{{total_purchase}}</a></td>';
							str_row +='<td class="text-center"><a href="#" class="inv-sum" data-begin="{{total_begin}}" data-sku="' + row['sku'] + '" data-branch="' + cab_id + '" data-transaction="4" data-startdate="' + from_date + '" data-endate="' + to_date + '">{{total_move_in}}</a></td>';
							str_row +='<td class="text-center"><a href="#" class="inv-sum" data-begin="{{total_begin}}" data-sku="' + row['sku'] + '" data-branch="' + cab_id + '" data-transaction="-4" data-startdate="' + from_date + '" data-endate="' + to_date + '">{{total_move_out}}</a></td>';
							str_row +='<td class="text-center"><a href="#" class="inv-sum" data-begin="{{total_begin}}" data-sku="' + row['sku'] + '" data-branch="' + cab_id + '" data-transaction="-1" data-startdate="' + from_date + '" data-endate="' + to_date + '">{{total_sales}}</a></td>';
							str_row +='<td class="text-center"><a href="#" class="inv-sum" data-begin="{{total_begin}}" data-sku="' + row['sku'] + '" data-branch="' + cab_id + '" data-transaction="2" data-startdate="' + from_date + '" data-endate="' + to_date + '">{{total_sales_return}}</a></td>';
							str_row +='<td class="text-center"><a href="#" class="inv-sum" data-begin="{{total_begin}}" data-sku="' + row['sku'] + '" data-branch="' + cab_id + '" data-transaction="3" data-startdate="' + from_date + '" data-endate="' + to_date + '">{{total_adjustment_in}}</a></td>';
							str_row +='<td class="text-center"><a href="#" class="inv-sum" data-begin="{{total_begin}}" data-sku="' + row['sku'] + '" data-branch="' + cab_id + '" data-transaction="-5" data-startdate="' + from_date + '" data-endate="' + to_date + '">{{total_adjustment_out}}</a></td>';							
							str_row +='<td class="text-center"><a href="#" class="inv-sum" data-begin="{{total_begin}}" data-sku="' + row['sku'] + '" data-branch="' + cab_id + '" data-transaction="" data-startdate="' + from_date + '" data-endate="' + to_date + '">{{total_end}}</a></td>';


							total['begin'] = 0;
							total['purchase'] = 0;
							total['move-in'] = 0;
							total['move-out'] = 0;
							total['sales'] = 0;
							total['sales-return'] = 0;
							total['adjustment-in'] = 0;
							total['adjustment-out'] = 0;
							total['total-stock'] = 0;
							total['end'] = 0;


							for(var cabang in row['cabang']) {

								//console.log(row['cabang']);
								var data = row['cabang'][cabang];
								
								
								
								if(data) {

									/*
									var data = [
							          	{ 'id':'3', 'name':'adjustment-in'},
							          	{ 'id':'-5', 'name':'adjustment-out'},
							          	{ 'id':'-3', 'name':'break-out'},
							          	{ 'id':'1000', 'name':'crossing-out'},
							          	{ 'id':'4', 'name':'movement-in'},
							          	{ 'id':'-4', 'name':'movement-out'},
							  			{ 'id':'1', 'name':'purchase-in'},
							  			{ 'id':'2', 'name':'refund-in'},
							          	{ 'id':'-2', 'name':'refund-out'},
							          	{ 'id':'-1', 'name':'sales-out'}
							  		];

									*/									
									str_row +='<td class="text-center">' + Number(data['begin']).format_int() + '</td>';
									str_row +='<td class="text-center"><a href="#" data-begin="' + data['begin'] + '" class="inv-sum" data-sku="' + row['sku'] + '" data-branch="' + cabang + '" data-transaction="1" data-startdate="' + from_date + '" data-endate="' + to_date + '">' + Number(data['purchase']).format_int() + '</a></td>';
									str_row +='<td class="text-center"><a href="#" data-begin="' + data['begin'] + '" class="inv-sum" data-sku="' + row['sku'] + '" data-branch="' + cabang + '" data-transaction="4" data-startdate="' + from_date + '" data-endate="' + to_date + '">' + Number(data['move-in']).format_int() + '</a></td>';
									str_row +='<td class="text-center"><a href="#" data-begin="' + data['begin'] + '" class="inv-sum" data-sku="' + row['sku'] + '" data-branch="' + cabang + '" data-transaction="-4" data-startdate="' + from_date + '" data-endate="' + to_date + '">' + Number(data['move-out']).format_int() + '</a></td>';
									str_row +='<td class="text-center"><a href="#" data-begin="' + data['begin'] + '" class="inv-sum" data-sku="' + row['sku'] + '" data-branch="' + cabang + '" data-transaction="-1" data-startdate="' + from_date + '" data-endate="' + to_date + '">' + Number(data['sales']).format_int() + '</a></td>';
									str_row +='<td class="text-center"><a href="#" data-begin="' + data['begin'] + '" class="inv-sum" data-sku="' + row['sku'] + '" data-branch="' + cabang + '" data-transaction="2" data-startdate="' + from_date + '" data-endate="' + to_date + '">' + Number(data['sales-return']).format_int() + '</a></td>';
									str_row +='<td class="text-center"><a href="#" data-begin="' + data['begin'] + '" class="inv-sum" data-sku="' + row['sku'] + '" data-branch="' + cabang + '" data-transaction="3" data-startdate="' + from_date + '" data-endate="' + to_date + '">' + Number(data['adjustment-in']).format_int() + '</a></td>';
									str_row +='<td class="text-center"><a href="#" data-begin="' + data['begin'] + '" class="inv-sum" data-sku="' + row['sku'] + '" data-branch="' + cabang + '" data-transaction="-5" data-startdate="' + from_date + '" data-endate="' + to_date + '">' + Number(data['adjustment-out']).format_int() + '</a></td>';									
									str_row +='<td class="text-center"><a href="#" data-begin="' + data['begin'] + '" class="inv-sum" data-sku="' + row['sku'] + '" data-branch="' + cabang + '" data-transaction="" data-startdate="' + from_date + '" data-endate="' + to_date + '">' + Number(data['end']).format_int() + '</a></td>';



									total[cabang]['begin'] += Number(data['begin']);
									total[cabang]['purchase'] += Number(data['purchase']);
									total[cabang]['move-in'] += Number(data['move-in']);
									total[cabang]['move-out'] += Number(data['move-out']);
									total[cabang]['sales'] += Number(data['sales']);
									total[cabang]['sales-return'] += Number(data['sales-return']);
									total[cabang]['adjustment-in'] += Number(data['adjustment-in']);
									total[cabang]['adjustment-out'] += Number(data['adjustment-out']);
									total[cabang]['total-stock'] += Number(data['total-stock']);
									total[cabang]['end'] += Number(data['end']);


									total['begin'] +=  Number(data['begin']);
									total['purchase'] +=  Number(data['purchase']);
									total['move-in'] +=  Number(data['move-in']);
									total['move-out'] +=  Number(data['move-out']);
									total['sales'] +=  Number(data['sales']);
									total['sales-return'] +=  Number(data['sales-return']);
									total['adjustment-in'] +=  Number(data['adjustment-in']);
									total['adjustment-out'] +=  Number(data['adjustment-out']);
									total['total-stock'] +=  Number(data['total-stock']);
									total['end'] +=  Number(data['end']);
									

								}
								
							}


							total['all_branches']['begin'] += total['begin'];
							total['all_branches']['purchase'] += total['purchase'];
							total['all_branches']['move-in'] += total['move-in'];
							total['all_branches']['move-out'] += total['move-out'];
							total['all_branches']['sales'] += total['sales'];
							total['all_branches']['sales-return'] += total['sales-return'];
							total['all_branches']['adjustment-in'] += total['adjustment-in'];
							total['all_branches']['adjustment-out'] += total['adjustment-out'];
							total['all_branches']['total-stock'] += total['total-stock'];
							total['all_branches']['end'] += total['end'];


							str_row +='</tr>';
							str_row = str_row.replaceAll("{{total_begin}}", Number(total['begin']).format_int());
							str_row = str_row.replace("{{total_purchase}}", Number(total['purchase']).format_int());
							str_row = str_row.replace("{{total_move_in}}", Number(total['move-in']).format_int());
							str_row = str_row.replace("{{total_move_out}}", Number(total['move-out']).format_int());
							str_row = str_row.replace("{{total_sales}}", Number(total['sales']).format_int());
							str_row = str_row.replace("{{total_sales_return}}", Number(total['sales-return']).format_int());
							str_row = str_row.replace("{{total_adjustment_in}}", Number(total['adjustment_in']).format_int());
							str_row = str_row.replace("{{total_adjustment_out}}", Number(total['adjustment_out']).format_int());
							//str_row = str_row.replace("{{total_stock}}", Number(total['total-stock']).format_int());
							str_row = str_row.replace("{{total_end}}", Number(total['end']).format_int());

							str+=str_row;


						}


						var str_footer = '<tr style="background-color:#0066CC; color:#ffffff;">';	
						str_footer += '<td></td>';
						str_footer += '<td></td>';
						str_footer += '<td></td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['begin']).format_int() + '</td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['purchase']).format_int() + '</td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['move-in']).format_int() + '</td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['move-out']).format_int() + '</td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['sales']).format_int() + '</td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['sales-return']).format_int() + '</td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['adjustment-in']).format_int() + '</td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['adjustment-out']).format_int() + '</td>';
						//str_footer += '<td class="text-center">' + Number(total['all_branches']['total-stock']).format_int() + '</td>';
						str_footer += '<td class="text-center">' + Number(total['all_branches']['end']).format_int() + '</td>';



						for(var i=0;i<selected_branches.length;i++) {			

							var branch_name = selected_branches[i].split("|")[2];							

							str_footer += '<td class="text-center">' + Number(total[branch_name]['begin']).format_int() + '</td>';
							str_footer += '<td class="text-center">' + Number(total[branch_name]['purchase']).format_int() + '</td>';
							str_footer += '<td class="text-center">' + Number(total[branch_name]['move-in']).format_int() + '</td>';
							str_footer += '<td class="text-center">' + Number(total[branch_name]['move-out']).format_int() + '</td>';
							str_footer += '<td class="text-center">' + Number(total[branch_name]['sales']).format_int() + '</td>';
							str_footer += '<td class="text-center">' + Number(total[branch_name]['sales-return']).format_int() + '</td>';
							str_footer += '<td class="text-center">' + Number(total[branch_name]['adjustment-in']).format_int() + '</td>';
							str_footer += '<td class="text-center">' + Number(total[branch_name]['adjustment-out']).format_int() + '</td>';
							//str_footer += '<td class="text-center">' + Number(total[branch_name]['total-stock']).format_int() + '</td>';
							str_footer += '<td class="text-center">' + Number(total[branch_name]['end']).format_int() + '</td>';							
							

						}						

						str_footer += '</tr>';
						str += str_footer;						
	  					$('#table-rows').append(str);

	  					$('.inv-sum').click(function(e){
	  						e.preventDefault();
	  						get_details(this);
	  					})

	  					scope.loading.on_loading(false);



					}
					else {
						scope.loading.on_loading(false);
						alert(response.data);						
					}

				}	

			});

	  	}



	  	function get_details($this) {

	  		var branch_name = $($this).attr('data-branch');

	  		var pos = branch_name.indexOf("|");
	  		//console.log(pos);
	  		var branch_id = "";
	  		if( pos > -1) {
	  			branch_id = branch_name;
	  			//console.log('masuk ke-1: ' + branch_id);
	  		}
	  		else {	  			
	  			branch_id = scope.cb_branch.get_branch_id(branch_name);
	  			if( branch_id == '') {
	  				branch_id = branch_name;
	  			}
	  			//console.log('masuk ke-2: ' + branch_id);
	  		}
	  		

	  		var data_begin = $($this).attr('data-begin');


	  		var pdata = {
	  			reason_id : $($this).attr('data-transaction'),
	  			from_date : $($this).attr('data-startdate'),
	  			to_date : $($this).attr('data-endate'),
	  			cab_id : branch_id,
	  			sku : $($this).attr('data-sku')
	  		};	 		


	  		
	  		var body = $('.inv-details').find('tbody');
	  		$(body).empty();

	  		$.ajax({
	  			method :'get',
	  			data : pdata,
	  			dataType :'json',
	  			url : 'svc/inv_stock_details_new_svc.php',
	  			success : function(response) {

	  				if(response.status) {

	  					var str = '';
	  					var total = 0;

	  					for(var i=0;i<response.data.length;i++) {

	  						var data = response.data[i];

	  						str += '<tr>';
							str += '<td>' + (i+1) + '</td>';
							str += '<td>' + data['stock_date'] + '</td>';
							str += '<td>' + data['branch'] + '</td>';
							str += '<td>' + data['sku'] + '</td>';
							str += '<td>' + data['product'] + '</td>';
							str += '<td class="text-center">' + data['qty'] + '</td>';
							str += '<td>' + data['reason'] + '</td>';
							str += '<td>' + data['no_po'] + '</td>';
							str += '<td>' + data['supplier_name'] + '</td>';
							str += '<td>' + data['appuser'] + '</td>';
						  	str += '</tr>';

						  	total += Number(data['qty']);
	  					}  					
	  					

	  					str += '<tr class="btn-primary">';
							str += '<td></td>';
							str += '<td></td>';
							str += '<td></td>';
							str += '<td></td>';
							str += '<td></td>';
							str += '<td class="text-center">' + Number(total).format_int() + '</td>';
							str += '<td></td>';
							str += '<td></td>';
							str += '<td></td>';
							str += '<td></td>';
						  	str += '</tr>';

	  					//console.log(str);

	  					var data_end = Number(data_begin) + Number(total);
	  					$('.inv-details-info').html("Begin : " + data_begin + ", Transaction : " + total + ",  End : " + data_end);
	  					

	  					$(body).append(str);
	  					$('.modal-inv-details').modal('show');

	  				}

	  			}
	  		});

	  	}

  	});


</script>


<?php include_once('footer.php'); ?>





