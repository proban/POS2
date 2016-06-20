<?php include_once('header.php'); ?>

<?php
	
	$to_date = date("Y-m-d");
	$date1 = new DateTime($to_date);	
	$date1->sub(DateInterval::createFromDateString('7 days'));		
	$from_date = $date1->format('Y-m-d');

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Inventory Stock Details (Minus Stock)</h4>
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
		    <label for="reason" class="col-sm-1 control-label">SKU</label>
		    <div class="col-sm-10"> 
		     	<div class="input-group">
			      <input type="text" class="form-control" id="sku" placeholder="e.g. 111FDR2100016, 111FDR2100017">
			      <span class="input-group-btn">
			        <button class="btn btn-default" id="btn-sku" type="button" data-toggle="modal" data-target="#panel-products"><span class="glyphicon glyphicon-search"></span></button>
			      </span>
			    </div>
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
					<th>Location</th>
					<th>Location Name</th>					
					<th>Product ID</th>
					<th>SKU</th>
					<th>Product</th>
					<th class="text-center">Units</th>					
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
<script src="jquery/custom.js"></script>

<script>


    $(document).ready(function() {

    	scope.date_range.on_change(function(){    		
    	});

    	scope.cb_branch.get_cabangs();

    	scope.submit.on_submit(function(){
    		query();
    	});


		function query(){
				

			var my_url = 'svc/inv_stock_details_minusstock_svc.php?';
			var cab_id = scope.cb_branch.get_cab_id();						
			var sku = '&sku=' + $('#sku').val();
			
			my_url = my_url + 'cab_id=' + cab_id + sku;

			scope.loading.on_loading(true);
			$('#table-rows').empty();

			$.ajax({
				url: my_url,
				method:'get',
				success : function(d) {

					response = JSON.parse(d);
					if( response.status == true) {							
						
						var fields = [
							'location',
							'location_name',
							'product_id',
							'sku',
							'product',
							'units'
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
								if(ff == 'units') {
									str += '<td class="text-center">' + Number(row[ff]).format_int() + '</td>';	
									total_qty += Number(row[ff]);
								}	
								else if(ff=='product_id') {
									str += '<td><a target="_blank" href="inv_stock_details_all.php?product_id=' + row['product_id'] + '&location=' + row['location'] + '">' + row[ff] + '</a></td>';	
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
							'<td></td> ' +		
							'<td class="text-center">' + Number(total_qty).format_int() + '</td> ' +							
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
