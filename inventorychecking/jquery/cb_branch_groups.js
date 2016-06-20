var scope = this.scope || {};

(function(){

	scope.cb_branch_groups = {

		selected_branches : [],

		all_branches : [
			{ id: '1', name:'Branches (OPEN)' },
			{ id: '2', name:'Branches (CLOSED)' },
			{ id: '3', name:'Franchisee (OPEN)' },
			{ id: '4', name:'Franchisee (CLOSED)' },
			{ id: '5', name:'HQ (OPEN)' },
			{ id: '6', name:'HQ (CLOSED)' }
		],

  		get_cabangs : function () {

  			var element = '#branch_id';
  			var that = this;

			$(element).empty();					
			for(var i=0;i<this.all_branches.length;i++) {

				row = this.all_branches[i];
				$(element).append('<option value="' + row['id']  + '">' + row['name'] + '</option>');
			}

			$(element).multiselect('destroy');

			$(element).multiselect({ 

				includeSelectAllOption: true,
				enableFiltering: true, 
				enableCaseInsensitiveFiltering: true,
				onChange: function(option, checked) {
   					
   					var branches = $('#branch_id option:selected');
   					that.selected_branches = [];

			        $(branches).each(function(index, branch) {

			        	var b= $(this).val();			        	
			            that.selected_branches.push(b);						            			            

			        });

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

  		,

  		get_branch_name : function (branch_id) {
  			for(var i=0;i<this.all_branches.length;i++) {
  				if(this.all_branches[i]['id'] == branch_id) {
  					return this.all_branches[i]['name'];
  				}
  			}
  			return branch_id;
  		}


	}

})();


  		