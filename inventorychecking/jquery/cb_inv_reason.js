var scope = this.scope || {};

(function(){

	scope.cb_inv_reason = {

		selected_reasons : [],

  		init : function () {

  			var element = '#reason';
  			var that = this;

  			var data = [
          { 'id':'3', 'name':'adjustment-in'},
          { 'id':'-5', 'name':'adjustment-out'},
          { 'id':'-3', 'name':'break-out'},
          { 'id':'1000', 'name':'crossing-out'},
          { 'id':'4', 'name':'movement-in'},
          { 'id':'-4', 'name':'movement-out'},
  				{ 'id':'1', 'name':'purchase-in'},
  				{ 'id':'2', 'name':'refund-in'},
          { 'id':'-2', 'name':'refund-out'},
          { 'id':'-1', 'name':'sales-out'}
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
  						
    					var reasons = $('#reason option:selected');
    					that.selected_reasons = [];

    	        $(reasons).each(function(index, branch){
    		        	var b= $(this).val();  			        	
    		          that.selected_reasons.push(b);						            
    	        });  

  				}

  			});	

  		},

  		get_reason_id : function() {


  			if (this.selected_reasons.length>0) {

  				var str = '';
  				for(var i=0;i<this.selected_reasons.length;i++) {
  					str+=this.selected_reasons[i] + '|';
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


  		