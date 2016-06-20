var scope = this.scope || {};

(function(){

	scope.cb_bool = {

		selected_bool : [],

		selected_bool2 : [],

		all_branches : [],

  		init : function (pelement, p_on_change) {

  			var element = pelement;
  			var that = this;
			
			var data = [];
			data[0] = { id:1, 'name':'TRUE'};
			data[1] = {id:0, 'name':'FALSE'};
			
			for(var i=0;i<data.length;i++) {

				row = data[i];
				$(element).append('<option value="' + row['id'] + '">' + row['name'] + '</option>');
			}

			$(element).multiselect('destroy');

			$(element).multiselect({ 
				includeSelectAllOption: true,
				enableFiltering: true, 
				enableCaseInsensitiveFiltering: true,
				onChange: function(option, checked) {
					
					var branches = $(element + ' option:selected');
					that.selected_bool = [];
					that.selected_bool2 = [];

					$(branches).each(function(index, branch){

						var b= $(this).val();
						b1 = b.split("|");
						that.selected_bool.push(b1[0]);						            
						that.selected_bool2.push(b);

					});

					if(p_on_change) {
						p_on_change();
					}
					

				}
			});

  		},

  		get_selected_values : function() {


  			if (this.selected_bool.length>0) {

  				var str = '';
  				for(var i=0;i<this.selected_bool.length;i++) {
  					str+=this.selected_bool[i] + '|';
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


  		