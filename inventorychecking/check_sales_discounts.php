<?php
include_once('defines.php');
include_once(__ROOT__.'/header.php');
include_once(__ROOT__.'/util/date_util.php');

$date_range = date_util::date_period();

?>


<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Check Sales Discounts</h4>
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
		    <div class="col-sm-offset-1 col-sm-10">
		      <button type="submit" class="btn btn-default" id="btn-submit">Search</button>
		    </div>		   
		  </div>
		  
		</form>

 	</div>

</div>


<script src="jquery/date_range.js"></script>
<script src="jquery/cb_branch.js"></script>
<script src="jquery/custom.js"></script>


<script>


    $(document).ready(function() {
    	
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
			

			var my_url = 'svc/check_sales_discounts_svc.php?';
			var cab_id = scope.cb_branch.get_cab_id();										

			my_url = my_url + 'cab_id=' + cab_id + '&from_date=' + from_date + '&to_date=' + to_date;

			$('#table-header').empty();
			$('#table-rows').empty();


			scope.loading.on_loading(true);

			$.ajax({
				url: my_url,
				method:'get',
				dataType:'json',
				success : function(response) {

					if( response.status == true) {							

						console.log(response);
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





