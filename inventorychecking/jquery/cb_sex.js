var scope = this.scope || {};

(function(){

	scope.cb_sex = {

		selected_sexs : [],

		all_sexs : [],

  		get_data : function (p_on_change) {

  			var element = '#sex_id';
  			var that = this;

			response = {};
			response.data = [];
			response.data[0] = {id:'laki-laki',name:'Laki-Laki'};
			response.data[1] = {id:'perempuan',name:'Perempuan'};


			for(var i=0;i<response.data.length;i++) {

				row = response.data[i];
				$(element).append('<option value="' + row['id'] + '">' + row['name'] + '</option>');

			}


			$(element).multiselect('destroy');
			$(element).multiselect({ 
				includeSelectAllOption: true,
				enableFiltering: true, 
				enableCaseInsensitiveFiltering: true,
				onChange: function(option, checked) {
   					
   					var sexs = $('#sex_id option:selected');
   					that.selected_sexs = [];
   					that.selected_sexs2 = [];

			        $(sexs).each(function(index, sex){

			        	var b= $(this).val();			        				            
			            that.selected_sexs.push(b);

			        });

			        if(p_on_change) {
			        	p_on_change();
			        }			        

				}
			});
	

  		},

  		get_sex_id : function() {

  			if (this.selected_sexs.length>0) {

  				var str = '';

  				for(var i=0;i<this.selected_sexs.length;i++) {
  					str+=this.selected_sexs[i] + '|';
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

  		,get_sex_name : function (sex_id) {

  			for(var i=0;i<this.all_sexs.length;i++) {
  				if(this.all_sexs[i]['id'] == sex_id) {
  					return this.all_sexs[i]['name'];
  				}
  			}
  			return sex_id;
  		}


	}

})();


  		