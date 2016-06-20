<?php
if(!isset($_GET['product_id']) || !isset($_GET['location'])) {
	echo "invalid product_id or location";
}
?>


<?php include_once('header.php'); ?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Inventory Stock Details (All)</h4>
	</div>

  	<div class="panel-body">
		

		<input type="hidden" id="product_id" value="<?php echo $_GET["product_id"]; ?>">
		<input type="hidden" id="location"  value="<?php echo $_GET["location"]; ?>">

		<table id="table-result" class="table table-bordered table-striped table-condensed">
			<thead style="background-color:#0066CC; color:#ffffff;">
				<tr>
					<th>#</th>
					<th>Stock Date</th>
					<th>Location</th>					
					<th>Location-Name</th>
					<th>Product ID</th>
					<th>SKU</th>
					<th>Product</th>					
					<th class="text-center">Units</th>					
					<th>Reason</th>
					<th>No PO</th>					
					<th>Supplier Name</th>					
					<th>User</th>					
			  	</tr>	
			</thead>	
			<tbody id="table-rows">
			</tbody>
		<table>

	</div>

</div>

<script src="jquery/custom.js"></script>

<script>


    $(document).ready(function() {


		function query(){				
			
			var my_url = 'svc/inv_stock_details_all_svc.php?';

			var cab_id = 'cab_id=' + $('#location').val();			
			var product_id = '&product_id=' + $('#product_id').val();
			
			my_url = my_url + cab_id + product_id;

			scope.loading.on_loading(true);
			$('#table-rows').empty();

			$.ajax({
				url: my_url,
				method:'get',
				success : function(d) {

					response = JSON.parse(d);
					if( response.status == true) {							
						
						var fields = [
							'stock_date',
							'location',
							'location_name',
							'product_id',
							'sku',
							'product',
							'units',
							'reason',
							'no_po',
							'supplier_name',
							'appuser',
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
							'<td>Total</td> ' +							
							'<td class="text-center">' + Number(total_qty).format_int() + '</td> ' +							
							'<td></td> ' +		
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


	  	query();

  	});


</script>


<?php include_once('footer.php'); ?>
