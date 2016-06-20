var scope = this.scope || {};

(function(){

	scope.cb_branch = {

		selected_branches : [],

		selected_branches2 : [],

		all_branches : [],

		get_data_cabangs : function() {

			var that = this;

			$.ajax({				
				url: 'svc/cabang_svc.php',
				method:'get',
				dataType:'json',
				success : function(d) {			

					if(d.status == true) {
						that.all_branches = d.data;	
						//console.log(that.all_branches);
					}

				}
			});

		},

  		get_cabangs : function (p_on_change) {

  			var element = '#branch_id';
  			var that = this;

			$.ajax({				
				url: 'svc/cabang_svc.php',
				method:'get',
				success : function(d) {			

					response = JSON.parse(d);					
					
					if( response.status == true) {

						that.all_branches = response.data;

						$(element).empty();					

						for(var i=0;i<response.data.length;i++) {

							row = response.data[i];
							$(element).append('<option value="' + row['id'] + '|' + row['database_name'] + '|' + row['name'] + '">' + row['name'] + '</option>');
						}

						$(element).multiselect('destroy');


						$(element).multiselect({ 
							includeSelectAllOption: true,
							enableFiltering: true, 
							enableCaseInsensitiveFiltering: true,
							onChange: function(option, checked) {
               					
               					var branches = $('#branch_id option:selected');
               					that.selected_branches = [];
               					that.selected_branches2 = [];

						        $(branches).each(function(index, branch){

						        	var b= $(this).val();
						        	b1 = b.split("|");
						            that.selected_branches.push(b1[0]);						            
						            that.selected_branches2.push(b);

						        });

						        if(p_on_change) {
						        	p_on_change();
						        }
						        

            				}
						});

					}
					else {						
						alert(response.data);
					}

				}		

			});			

  		},

  		get_cab_id : function() {


  			if (this.selected_branches.length>0) {

  				var str = '';

  				for(var i=0;i<this.selected_branches.length;i++) {
  					str+=this.selected_branches[i] + '|';
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

  		,get_branch_name : function (branch_id) {

  			for(var i=0;i<this.all_branches.length;i++) {
  				if(this.all_branches[i]['id'] == branch_id) {
  					return this.all_branches[i]['name'];
  				}
  			}
  			return branch_id;
  		}


  		,get_branch_id : function (branch_name) {

  			for(var i=0;i<this.all_branches.length;i++) {
  				if(this.all_branches[i]['name'] == branch_name) {
  					return this.all_branches[i]['id'];
  				}
  			}
  			return '';
  		}



	}

})();


  		