<?php
include_once('defines.php');
include_once(__ROOT__.'/header.php');

?>

<div class="panel panel-default">

	<div class="panel-heading">
		<h4>Limbah Edit</h4>
	</div>

  	<div class="panel-body">
		
		<form class="form-horizontal">


		  <div class="form-group">		  	
		    <label for="code" class="col-sm-1 control-label">Category</label>
		    <div class="col-sm-10">
			<input type="hidden" class="form-control inline" name="id" id="id" placeholder="Id" value="<?php echo $_GET['id']; ?>">	
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
		    <label for="enabled" class="col-sm-1 control-label">Qty</label>
		    <div class="col-sm-2">		
		    	<input type="number" class="form-control inline" name="qty" id="qty" placeholder="Qty">				
		    </div>
		  </div>

		  <div class="form-group">		  	
		    <label for="code" class="col-sm-1 control-label">Unit ID</label>
		    <div class="col-sm-10">
		    	<select name="unit_id" id="unit_id" class="form-control">
					<option value="">[select Unit]</option>
		    		<option value="Pcs">Pcs</option>
		    		<option value="Ml">Ml</option>		    		
		    	</select>	
		    </div>
		  </div>


		  <div class="form-group">		  	
		    <label for="enabled" class="col-sm-1 control-label">Description</label>
		    <div class="col-sm-10">		
		    	<input type="text" class="form-control inline" name="description" id="description" placeholder="Keterangan">				
		    </div>
		  </div>

		  <div class="form-group">		  	
		    <label for="user" class="col-sm-1 control-label">User</label>
		    <div class="col-sm-10">
		    	<select name="user" id="user" class="form-control">
		    	</select>	
		    </div>
		  </div>

		  <div class="form-group">
		    <div class="col-sm-offset-1 col-sm-10">

		    	<input type="hidden" name="id" id="id" />
		      <button type="submit" class="btn btn-default" id="btn-save">Save</button>
		      <button type="button" class="btn btn-default" id="btn-cancel">Cancel</button>
		    </div>		   
		  </div>


		</form>


 	</div>

</div>


<script src="jquery/custom.js"></script>

<script>


    $(document).ready(function() {

		function get_users() {
			
			var my_url = 'svc/get_users_svc.php';	

			$.ajax({				
				url: my_url,
				method:'get',
				dataType : 'json',
				success : function(d) {
					
					if( d.status == true) {

						var str ='';
						$('#user').empty();
						$('#user').append('<option value="">[select Users]</option>');
						for(var i=0;i<d.data.length;i++) {
							var data = d.data[i];
							$('#user').append('<option value="' + data['name'] + '">' + data['name'] + '</option>');
						}
	  					
					}
					else {						
						alert(d.data);						
					}

				}	

			});

    	}


    	get_users();
		
		
    	function load_data() {

    		var action='action=single';
			var id = '&id=' + $('#id').val();

			var my_url = 'svc/master_limbah_svc.php?';						
			my_url = my_url + action;	
			my_url = my_url + id;

			$.ajax({				
				url: my_url,
				method:'get',
				dataType : 'json',
				success : function(d) {
					
					if( d.status == true) {		
											
						$('#category').val(d.data['category']);
						$('#qty').val(d.data['qty']);
						$('#user').val(d.data['user']);		
						$('#description').val(d.data['description']);
						$('#unit_id').val(d.data['unit_id']);
						$('#id').val(d.data['id']);

	  					scope.loading.on_loading(false);
	  					
					}
					else {

						scope.loading.on_loading(false);
						alert(d.data);						
					}

				}	

			});

    	}

    	load_data();

    	$('#btn-save').click(function(e){
    		e.preventDefault();    		
    		save();
    	});

    	$('#btn-cancel').click(function(e){
    		e.preventDefault();    		
    		window.location.href='limbah.php';
    	});



		function save(){
			
			var action='action=update';
			var id = '&id=' + $('#id').val();				
			var category = '&category=' + $('#category').val();
			var qty = '&qty=' + $('#qty').val();
			var unit_id = '&unit_id=' + $('#unit_id').val();
			var user = '&user=' + $('#user').val();
			var description = '&description=' + $('#description').val();

			
			var my_url = 'svc/master_limbah_svc.php?';						

			my_url = my_url + action;	
			my_url = my_url + id;			
			my_url = my_url + category;
			my_url = my_url + qty;
			my_url = my_url + unit_id;
			my_url = my_url + user;
			my_url = my_url + description;

			scope.loading.on_loading(true);


			$.ajax({				
				url: my_url,
				method:'get',
				dataType : 'json',
				success : function(d) {
					
					if( d.status == true) {							
						alert(d.data);						
	  					scope.loading.on_loading(false);
	  					window.location.href='limbah.php';
					}
					else {
						scope.loading.on_loading(false);
						alert(d.data);						
					}

				}	

			});

	  	}


  	});


</script>


<?php include_once('footer.php'); ?>


