<?php include_once('header.php'); ?>


<div class="panel panel-default">

	
	<div class="panel-heading">
		<h4>Inventory Stock Current - Stock Diary Differences</h4>
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

		<!--<div id="loading" style="display:none;">Loading...</div>-->

		<table id="table-result" class="table table-bordered table-striped table-condensed">
			<thead style="background-color:#0066CC; color:#ffffff;">
				<tr>
					<th>No</th>
					<th>Branch Name</th>
					<th>SKU</th>
					<th>Product Name</th>
					<th>Stock Current</th>
					<th>Stock Diary</th>
					<th>Stock Different</th>
					<!--<th>Current Sell Price</th>-->

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
				
			var from_date = scope.date_range.get_from_date();
			var to_date = scope.date_range.get_to_date();

			if ( from_date == '' || to_date == '') {
				alert("Invalid date parameter!");
				return;
			}


			var my_url = 'svc/inv_stock_diff_svc.php?';
			var cab_id = scope.cb_branch.get_cab_id();			
			my_url = my_url + 'cab_id=' + cab_id;

			scope.loading.on_loading(true);

			$('#table-rows').empty();


			$.ajax({
				url: my_url,
				method:'get',
				dataType :'json',
				success : function(d) {

					if( d.status == true) {							
						

						var total = [];
						total['stockcurrent'] = Number(0);
						total['stockdiary'] = Number(0);
						total['stock_diff'] = Number(0);						

						var str = '';
						for(var i=0;i<d.data.length;i++) {

							row = d.data[i];							
							if (i%2==1) {
								css = 'class="success"';
							}
							else {
								css = '';
							}

							str += '<tr ' + css + '>';
							str += '<td>' + (i+1) + '</td>';
							str += '<td>' + row['branch_name'] + '</td>';
							str += '<td>' + row['sku'] + '</td>';
							str += '<td>' + row['product_name'] + '</td>';

							str += '<td class="text-center">' + Number(row['stockcurrent']).format_int() + '</td>';
							str += '<td class="text-center">' + Number(row['stockdiary']).format_int() + '</td>';
							str += '<td class="text-center">' + Number(row['stock_diff']).format_int() + '</td>';
							//str += '<td class="text-right">' + Number(row['pricesell']).format_decimal() + '</td>';
							str +='</tr>';

							total['stockcurrent'] = total['stockcurrent'] + Number(row['stockcurrent']);
							total['stockdiary'] =  total['stockdiary'] + Number(row['stockdiary']);
							total['stock_diff'] = total['stock_diff'] + Number(row['stock_diff']);							

						}


						//footer summary
						str2 = '<tr style="background-color:#0066CC; color:#ffffff;">' +
							'<td></td> ' +
							'<td></td> ' +
							'<td></td> ' +
							'<td></td> ' +
							'<td class="text-center">' + Number(total['stockcurrent']).format_int() + '</td> ' +
							'<td class="text-center">' + Number(total['stockdiary']).format_int() + '</td> ' +
							'<td class="text-center">' + Number(total['stock_diff']).format_int() + '</td> ' +							
							//'<td></td> ' +							
							'</tr>';

						str += str2;
	  					
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
