<?php include_once('header.php'); ?>

<?php
	
	$to_date = date("Y-m-d");
	$date1 = new DateTime($to_date);	
	$date1->sub(DateInterval::createFromDateString('7 days'));		
	$from_date = $date1->format('Y-m-d');

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Inventory Stock Details</h4>
	</div>

  	<div class="panel-body">
		
		<form class="form-horizontal">

		  <div class="form-group">
		    <label for="from_date" class="col-sm-1 control-label">Period</label>
		    <div class="col-sm-10">  

		    	<div class="form-group">
			      	<div class="col-lg-2">			          
			        	<input type="date" class="form-control inline" name="from_date" id="from_date" placeholder="From Date" value="<?php echo $from_date; ?>">			        
			      	</div> 
			      	<div class="col-lg-2">			          
		          		<input type="date" class="form-control inline" name="to_date" id="to_date" placeholder="To Date" value="<?php echo $to_date; ?>">
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
		    <label for="reason" class="col-sm-1 control-label">Reason</label>
		    <div class="col-sm-10">      
		    	<select name="reason" id="reason" multiple="multiple">			
		  		</select>
		    </div>
		  </div>



		  <div class="form-group">
		    <div class="col-sm-offset-1 col-sm-10">
		      <button type="submit" class="btn btn-default" id="btn-submit">Search</button>
		    </div>		   
		  </div>
		  
		</form>

		<!--<div id="loading" style="display:none;">Loading...</div>-->

		<table id="table-result" class="table table-bordered table-striped table-condensed">
			<thead style="background-color:#0066CC; color:#ffffff;">
				<tr>
					<th>#</th>
					<th>Stock Date</th>
					<th data-field="branch" data-sortable="true">Branch </th>					
					<th>SKU</th>
					<th>Product</th>
					<th class="text-center">Qty</th>
					<th>Reason</th>										
					<th>PO</th>	
					<th>Supplier</th>	
			  	</tr>	
			</thead>	
			<tbody id="table-rows">
			</tbody>
		<table>

	</div>

</div>



<script src="jquery/date_range.js"></script>
<script src="jquery/cb_branch.js"></script>
<script src="jquery/cb_category.js"></script>
<script src="jquery/cb_inv_reason.js"></script>
<script src="jquery/custom.js"></script>

<script>


    $(document).ready(function() {

    	scope.date_range.on_change(function(){    		
    	});

    	scope.cb_branch.get_cabangs();

    	scope.cb_inv_reason.init();  

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


			var my_url = 'svc/inv_stock_details_svc.php?';
			var cab_id = scope.cb_branch.get_cab_id();			
			var reason_id = scope.cb_inv_reason.get_reason_id();

			
			my_url = my_url + 'cab_id=' + cab_id + '&from_date=' + from_date + '&to_date=' + to_date + '&reason_id=' + reason_id;

			scope.loading.on_loading(true);

			$.ajax({
				url: my_url,
				method:'get',
				success : function(d) {

					response = JSON.parse(d);
					if( response.status == true) {							
						
						var fields = [
							'stock_date',
							'branch',
							'sku',
							'product',
							'qty',
							'reason',
							'no_po',
							'supplier_name',
						];

						
						var total_qty = 0;
						var str = '';

						for(var i=0;i<response.data.length;i++) {

							row = response.data[i];							

							if (i%2==1) {
								css = 'class="success"';
							}
							else {
								css = '';
							}


							str += '<tr ' + css + '>';
							str += '<td>' + (i+1) + '</td>';

							for (f in fields) {

								ff = fields[f];																
								if(ff == 'qty') {
									str += '<td class="text-center">' + Number(row[ff]).format_int() + '</td>';	
									total_qty += Number(row[ff]);
								}																
								else {
									str += '<td>' + row[ff] + '</td>';	
								}
							}	

							str +='</tr>';

						}


						//footer summary
						str2 = '<tr style="background-color:#0066CC; color:#ffffff;">' +
							'<td></td> ' +
							'<td></td> ' +
							'<td></td> ' +
							'<td></td> ' +
							'<td></td> ' +
							'<td class="text-center">' + Number(total_qty).format_int() + '</td> ' +
							'<td></td> ' +
							'<td></td> ' +
							'<td></td> ' +
							'</tr>';
							

						str += str2;					

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
