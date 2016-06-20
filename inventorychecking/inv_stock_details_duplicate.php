<?php include_once('header.php'); ?>

<?php
	
	$to_date = date("Y-m-d");
	$date1 = new DateTime($to_date);	
	$date1->sub(DateInterval::createFromDateString('7 days'));		
	$from_date = $date1->format('Y-m-d');

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Inventory Stock Details (Diff Sales/Stok)</h4>
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

		<div id="message"></div>
		<!--<div id="loading" style="display:none;">Loading...</div>-->

		<table id="table-result" class="table table-bordered table-striped table-condensed">
			<thead style="background-color:#0066CC; color:#ffffff;">
				<tr>
					<th></th>					
					<th>DateNew</th>
					<th>Location</th>					

					<th>Ticket</th>
					<th>Product</th>

					<th>SKU</th>
					<th>Product Name</th>

					<th>Ticket No</th>

					<th>Sales Count</th>
					<th>Sales Units</th>

					<th>Stock Count</th>
					<th>Stock Units</th>					
			  	</tr>	
			</thead>	
			<tbody id="table-rows">
			</tbody>
		<table>

	</div>

</div>

<div id="pin-dialog" title="Insert PIN ">
    <input type="password" size="25"  id="pin-dialog-text"/>
    <a href="#" class="btn btn-default" id="pin-dialog-enter">Enter</a>
</div>


<script src="jquery/date_range.js"></script>
<script src="jquery/cb_branch.js"></script>
<script src="jquery/cb_category.js"></script>
<script src="jquery/custom.js"></script>
<script src="jquery/moment.min.js"></script>
<script src="jquery/mypin.min.js"></script>

<script>


    $(document).ready(function() {

    	$( "#pin-dialog" ).dialog({
    		autoOpen: false
    	});


    	function fix_sales() {


    	}

    	function fix_stock() {


    		//$rows =$model->exec($var['branchcode'], $var['datenew'], $var['ticketid'], $var['product'], $var['sales_units']);
			var branchcode = 'branchcode=' +  current_row['branchcode'];
			var datenew = '&datenew=' + current_row['datenew'];
			var ticketid = '&ticket=' + current_row['ticket'];
			var product = '&product=' + current_row['product'];
			var sales_units = '&sales_units=' + current_row['sales_units'];
			var stock_units = '&stock_units=' + current_row['stock_units'];

    		var my_url = 'svc/fix_stock_svc.php?';
    		my_url =  my_url + branchcode;
    		my_url =  my_url + datenew;
    		my_url =  my_url + ticketid;
    		my_url =  my_url + product;
    		my_url =  my_url + sales_units;
    		my_url =  my_url + stock_units;


    		$.ajax({
				url: my_url,
				method:'get',
				dataType : 'json',
				success : function(response) {
					
					if( response.status == true) {						
						
						//alert('Berhasil');
						$('#message').val('sudah berhasil!');
						query();

					}
					else {		
						$('#message').val(response.data);							
											
					}

					//$(btn).prop('disabled', false);

				}	

			});

    	}


    	$('#pin-dialog-enter').click(function(e){

    		e.preventDefault();

    		var val = $('#pin-dialog-text').val();
    		var pin = calc_pin();    		

    		if(pin == val) {

				if(confirm("Yakin mau fix?")) {

					$('#pin-dialog').dialog('close');
					
					if( current_row['sales_units'] > current_row['stock_units']) {
						fix_stock();
					}
					else if( current_row['sales_units'] < current_row['stock_units']) {
						//fix_sales();
					}

				}

			}
			else {
				alert('Incorrect PIN Bung!')
			}

    	});

    	scope.date_range.on_change(function(){    		
    	});

    	scope.cb_branch.get_cabangs();

    	scope.submit.on_submit(function(){
    		query();
    	});

    	var current_row;


    	/*
    	function fix_me(branch_id, ticket, product, btn) {

    		$(btn).prop('disabled', true);
    		var my_url = 'svc/fix_duplicate_data_svc.php?branch_id='+ branch_id + '&ticket=' + ticket + '&product=' + product;
    		$.ajax({
				url: my_url,
				method:'get',
				dataType : 'json',
				success : function(response) {
					
					if( response.status == true) {							
						
						alert('Berhasil');
						query();

					}
					else {									
						alert(response.data);						
					}

					$(btn).prop('disabled', false);

				}	

			});

    	}*/



		function query(){
				

			var my_url = 'svc/inv_stock_details_duplicate_svc.php?';
			var cab_id = scope.cb_branch.get_cab_id();						
			var sku = '&sku=' + $('#sku').val();
			
			my_url = my_url + 'cab_id=' + cab_id + sku;

			scope.loading.on_loading(true);
			
			$('#table-rows').empty();

			$.ajax({
				url: my_url,
				method:'get',
				dataType : 'json',
				success : function(response) {
					
					if( response.status == true) {							
						
						var fields = [
							'datenew',
							'branchcode',
							'ticket',
							'product',
							'sales_cnt',
							'sales_units',
							'stock_cnt',
							'stock_units'
						];

						var str = '';
						var css = '';

						for(var i=0;i<response.data.length;i++) {

							var row = response.data[i];							

							if (i%2==1) {
								css = 'class="success"';
							}
							else {
								css = '';
							}

							str += '<tr ' + css + '>';
							str += '<td class="text-center"><button class="btn btn-danger btn-fix"'

							str += ' data-datenew="' + row['datenew'] + '"';
							str += ' data-branchcode="' + row['branchcode'] + '"';
							str += ' data-ticket="' + row['ticket'] + '"';
							str += ' data-product="' + row['product'] + '"';
							str += ' data-sku="' + row['sku'] + '"';
							str += ' data-product_name="' + row['product_name'] + '"';
							str += ' data-ticketid="' + row['ticketid'] + '"';
							str += ' data-sales_cnt="' + row['sales_cnt'] + '"';
							str += ' data-sales_units="' + row['sales_units'] + '"';
							str += ' data-stock_cnt="' + row['stock_cnt'] + '"';
							str += ' data-stock_units="' + row['stock_units'] + '"';

							str += '>Fix me</button></td>';	


							str += '<td class="text-center">' + row['datenew'] + '</td>';	
							str += '<td class="text-center">' + row['branchcode'] + '</td>';	
							
							str += '<td class="text-center">' + row['ticket'] + '</td>';	
							str += '<td class="text-center">' + row['product'] + '</td>';	

							str += '<td class="text-center">' + row['sku'] + '</td>';	
							str += '<td class="text-center">' + row['product_name'] + '</td>';	
							str += '<td class="text-center">' + row['ticketid'] + '</td>';	

							str += '<td class="text-center">' + row['sales_cnt'] + '</td>';	
							str += '<td class="text-center">' + row['sales_units'] + '</td>';	
							str += '<td class="text-center">' + row['stock_cnt'] + '</td>';	
							str += '<td class="text-center">' + row['stock_units'] + '</td>';	
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
							'<td></td> ' +									
							'<td></td> ' +									
							'</tr>';							

						str += str2;					

	  					
	  					$('#table-rows').append(str);


	  					$('.btn-fix').on('click', function(){

	  					
	  						current_row = [];
	  						current_row['datenew'] = $(this).attr('data-datenew');
	  						current_row['branchcode'] = $(this).attr('data-branchcode');

	  						current_row['ticket'] = $(this).attr('data-ticket');
	  						current_row['product'] = $(this).attr('data-product');
	  						current_row['sku'] = $(this).attr('data-sku');
	  						current_row['ticketid'] = $(this).attr('data-ticketid');

	  						current_row['sales_cnt'] = $(this).attr('data-sales_cnt');
	  						current_row['sales_units'] = $(this).attr('data-sales_units');
	  						current_row['stock_cnt'] = $(this).attr('data-stock_cnt');
	  						current_row['stock_units'] = $(this).attr('data-stock_units');
	  						
	  						$('#pin-dialog-text').val('');

	  						$('#pin-dialog').dialog('open');

	  					});

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

