var scope = this.scope || {};

(function(){

	scope.cb_zero_val = {

		selected_zero : [],

		init : function () {

			var element = '#zero-val';
			var that = this;

			var data = [
        { 'id':'1', 'name':'non-zero'},
        { 'id':'0', 'name':'zero'}
			];

      $(element).empty();   

			for(var i=0;i<data.length;i++) {
				row = data[i]; 
        var str = '<option value="' + row['id'] + '">' + row['name'] + '</option>';                   
				$(element).append(str);          
			}

			$(element).multiselect('destroy');

			$(element).multiselect({ 
				includeSelectAllOption: true,
				enableFiltering: true, 
				enableCaseInsensitiveFiltering: true,
				onChange: function(option, checked) {
						
  					var reasons = $('#zero-val option:selected');
  					that.selected_zero = [];

  	        $(reasons).each(function(index, branch){
  		        	var b= $(this).val();  			        	
  		          that.selected_zero.push(b);						            
  	        });  

				}

			});	

		},

		get_zero_id : function() {


			if (this.selected_zero.length>0) {

				var str = '';
				for(var i=0;i<this.selected_zero.length;i++) {
					str+=this.selected_zero[i] + '|';
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


  		