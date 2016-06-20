<?php include_once('header.php'); ?>

<?php
	
	$to_date = date("Y-m-d");
	$date1 = new DateTime($to_date);	
	$date1->sub(DateInterval::createFromDateString('7 days'));		
	$from_date = $date1->format('Y-m-d');

?>


<div class="panel panel-default" id="panel-stock-details">

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
					<th>Stock Date</th>
					<th data-field="branch" data-sortable="true">Branch </th>					
					<th>SKU</th>
					<th>Product</th>
					<th class="text-center">Qty</th>
					<th>Reason</th>										
					<th>PO</th>	
					<th>Supplier</th>	
					<th>User</th>	
			  	</tr>	
			</thead>	
			<tbody id="table-rows">
			</tbody>
		</table>

	</div>

</div>




<div class="modal fade" id="panel-products" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      	<div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        	<h4 class="modal-title" id="myModalLabel">Products</h4>
      	</div>

      	<div class="modal-body">

      		<div class="form-group">			    
			    <div class="col-sm-12" style="padding-left:0;padding-right:0">    
				    <div class="input-group">
				      <input type="text" class="form-control" id="q-products" placeholder="Search">
				      <span class="input-group-btn">
				        <button class="btn btn-default" id="btn-search-products" type="button"><span class="glyphicon glyphicon-search"></span></button>
				      </span>
				    </div>  
			    </div>
		  	</div>

			<table id="table-product" class="table table-bordered table-striped table-condensed">
				<thead style="background-color:#0066CC; color:#ffffff;">
					<tr>
						<th></th>					
						<th>SKU</th>
						<th>Product Name</th>					
						<th>Category</th>					
				  	</tr>	
				</thead>	
				<tbody id="table-product-rows">
				</tbody>
			</table>

		</div>

	</div>

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


			var my_url = 'svc/inv_stock_details_new_svc.php?';
			var cab_id = scope.cb_branch.get_cab_id();			
			var reason_id = scope.cb_inv_reason.get_reason_id();
			var sku = '&sku=' + $('#sku').val();
			
			my_url = my_url + 'cab_id=' + cab_id + '&from_date=' + from_date + '&to_date=' + to_date + '&reason_id=' + reason_id + sku;

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
							'appuser'
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



	  	function load_products(on_select) {

	  		var my_url = 'svc/product_svc.php?';
	  		var cab_id = 'cab_id=' +  scope.cb_branch.get_cab_id();			
	  		my_url = my_url + cab_id;

	  		$('#table-product-rows').empty();

	  		$.ajax({

	  			url: my_url,
				method:'get',
				dataType:'json',
				success : function(d) {


					var str='';
					for(var i=0;i<d.data.length;i++) {
						var data = d.data[i];

						str += '<tr>';
						str += '<td><button class="btn btn-info btn-select-product" data-sku="' + data['sku'] + '">Select</button></td>';
						str += '<td>' + data['sku'] + '</td>';
						str += '<td>' + data['product'] + '</td>';
						str += '<td>' + data['category'] + '</td>';						
						str += '</tr>';
					}

					$('#table-product-rows').append(str);
					$('#panel-product').show();		

					$('.btn-select-product').on('click', function(e){
						var sku = $(this).attr('data-sku');						

						if(on_select) {
							on_select(sku);
						}
					});

				}

	  		});

	  	}


	  	function find_sku(sku, new_sku) {

	  		var found = false;
	  		var k = sku.split(",");
	  		for(var i=0;i<k.length;i++) {
	  			if(k[i] == new_sku) {

	  				found = true;
	  				break;
	  			}
	  		}
	  		return found;

	  	}


	  	$('#btn-sku').on('click', function(e){
			load_products(function(new_sku){

				var sku = $('#sku').val().trim();
				if(sku.length ==0) {
					sku = new_sku;	
				}
				else {
					if(!find_sku(sku, new_sku)) {
						sku = sku + ',' + new_sku;							
					}					
				}
				$('#sku').val(sku);			

			});
	  	});


		function searchTable(inputVal)
		{
			var table = $('#table-product-rows');

			table.find('tr').each(function(index, row)
			{
				var allCells = $(row).find('td');
				if(allCells.length > 0)
				{
					var found = false;
					allCells.each(function(index, td)
					{
						var regExp = new RegExp(inputVal, 'i');
						if(regExp.test($(td).text()))
						{
							found = true;
							return false;
						}
					});
					if(found == true)$(row).show();else $(row).hide();
				}
			});
		}

		$('#q-products').keyup(function()
		{
			searchTable($(this).val());
		});


		$('#btn-search-products').keyup(function()
		{
			searchTable($(this).val());
		});


  	});


</script>


<?php include_once('footer.php'); ?>
