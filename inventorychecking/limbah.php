<?php
include_once('defines.php');
include_once(__ROOT__.'/header.php');
include_once(__ROOT__.'/util/date_util.php');


$date_range = date_util::date_period();
?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Limbah</h4>
	</div>

  	<div class="panel-body">
		
		<form class="form-horizontal">

		  <div class="form-group">
		    <label for="from_date" class="col-sm-1 control-label">Periode Input</label>
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
		    <label for="category" class="col-sm-1 control-label">Category</label>
		    <div class="col-sm-10">
		    	<select name="category" id="category" class="form-control">
				<option value="">[select Category]</option>
		    		<option value="Tire">Tire</option>
		    		<option value="Tube">Tube</option>
		    		<option value="Accu">Accu</option>		    		
		    		<option value="Oli">Oli</option>
		    		<option value="Kanvas">Kanvas</option>
		    	</select>
		    </div>
		  </div>

		  	
		  
		  <div class="form-group">
		    <div class="col-sm-offset-1 col-sm-10">
		      <button type="submit" class="btn btn-default" id="btn-submit">Search</button>
		    </div>		   
		  </div>
		</form>


		<div class="form-group" style="margin-bottom:10px;">

			<button class="btn btn-default" id="btn-new">Add New</button>			
			
		</div>

		<table id="table-result" class="table table-bordered table-striped table-condensed">
			<thead style="background-color:#0066CC; color:#ffffff;" id="table-header">				
				<tr>
					<th></th>
					<th></th>
					<th>Input Date</th>
					<th>Location</th>					
					<th>Category</th>
					<th>Qty</th>
					<th>Satuan/Unit</th>
					<th>Description</th>
					<th>User</th>					
					
				</tr>
			</thead>	

			<tbody id="table-rows">
			</tbody>

		</table>

 	</div>

</div>

<script src="jquery/cb_bool.js"></script>
<script src="jquery/custom.js"></script>
<script src="jquery/date_range.js"></script>




<script>


    $(document).ready(function() {

    	
	    	    

    	$('#btn-new').click(function(e) {
    		e.preventDefault();
    		window.location.href ='limbah_new.php';
    	});

    	scope.submit.on_submit(function(){
    		query();
    	});

    	function delete_(id) {


    		var action='action=delete';
			var id = '&id=' + id;
			var my_url = 'svc/master_limbah_svc.php?';						

			my_url = my_url + action;	
			my_url = my_url + id;			

    		$.ajax({				
				url: my_url,
				method:'get',
				dataType : 'json',
				success : function(r) {

					if(r.status == true) {						
						alert(r.data);
						query();
					}
					else {
						alert(r.data);
					}

				}
			});

    	}

	



		function query(){			
		    		
			var action='action=list';
			var from_date = '&from_date=' + $('#from_date').val();
			var to_date = '&to_date=' + $('#to_date').val();

			if ( from_date == '' || to_date == '') {
				alert("Invalid date parameter!");
				return;
			}

			var category = '&category=' + $('#category').val();
			var my_url = 'svc/master_limbah_svc.php?';						

			my_url = my_url + action;	
			my_url = my_url + from_date;	
			my_url = my_url + to_date;
			my_url = my_url + category;

			scope.loading.on_loading(true);

			$('#table-rows').empty();

			$.ajax({				
				url: my_url,
				method:'get',
				dataType : 'json',
				success : function(d) {
					
					if( d.status == true) {							

						var str ='';
						for(var i=0;i<d.data.length;i++) {

							if (i%2==1) {
								css = 'class="info"';
							}
							else {
								css = '';
							}

							var row = d.data[i];
							str='';
							str +='<tr ' + css + '>';
							str +='<td width="50"><button class="btn btn-default btn-edit" data-id="' + row['id'] + '">Edit</button></td>';
							str +='<td width="50"><button class="btn btn-default btn-delete" data-id="' + row['id'] + '">Delete</button></td>';
							str +='<td>' + row['input_date'] + '</td>';
							str +='<td>' + row['locations'] + '</td>';									
							str +='<td>' + row['category'] + '</td>';
							str +='<td>' + row['qty'] + '</td>';
							str +='<td>' + row['unit_id'] + '</td>';
							str +='<td>' + row['description'] + '</td>';
							str +='<td>' + row['user'] + '</td>';

							
							str +='</tr>';
							$('#table-rows').append(str);

						}							  					


						$('.btn-edit').click(function(e) {
							e.preventDefault();
							var id = $(this).attr('data-id');
							window.location.href='limbah_edit.php?id=' + id;

						});


						$('.btn-delete').click(function(e){

							e.preventDefault();
							if(confirm("Do you really want to delete?")) {
								var id = $(this).attr('data-id');
								delete_(id);
							}							

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


