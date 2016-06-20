var scope = this.scope || {};

(function(){

	scope.cb_category = {

		selected_cats : [],		

  		get_categories : function(from_date, to_date, cab_id, p_on_change) {
  			
			if ( !from_date|| !to_date) {
				alert("Invalid date parameter!");
				return;
			}

  			var my_url = 'svc/category_by_transaction_svc.php?from_date=' + from_date + '&to_date=' + to_date;

  			if (cab_id != '') {
  				my_url = my_url + '&cab_id=' + cab_id;
  			}

  			var that = this;

  			this.selected_cats = [];
  			
			$.ajax({				
				url: my_url,
				method:'get',
				success : function(d) {		

					response = JSON.parse(d);

					if( response.status == true) {
						$('#cat_id').empty();
						//$('#cat_id').append('<option value="">[All Categories]</option>');

						for(var i=0;i<response.data.length;i++) {
							row = response.data[i];
							$('#cat_id').append('<option value="' + row['id'] + '">' + row['name'] + '</option>');
						}

						$('#cat_id').multiselect('destroy');

						$('#cat_id').multiselect({ 

							includeSelectAllOption: true,
							enableFiltering: true, 
							enableCaseInsensitiveFiltering: true,
							onChange: function(option, checked) {
               					
               					var cats = $('#cat_id option:selected');
               					that.selected_cats = [];

						        $(cats).each(function(index, cat){
						        	var c = $(this).val();						        	
						            that.selected_cats.push(c);		
						        });

						        //console.log(selected_cats);
						        
						        if(p_on_change) {
						        	p_on_change();
						        }
						        

            				},
						});

					}
					else {
						alert(response.data);
					}
				}	

			});

  		},


  		get_cat_id : function() {

  			
  			if (this.selected_cats.length>0) {
  				var str = '';
  				for(var i=0;i<this.selected_cats.length;i++) {
  					str+=this.selected_cats[i] + '|';
  				}
  				if (str.length>0) {
  					str = str.substr(0, str.length-1);
  				}
  				return str;
  			}
  			else {
  				return '';
  			}

  		}

  		


	}

})();

  		