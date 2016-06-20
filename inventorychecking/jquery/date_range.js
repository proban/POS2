var scope = this.scope || {};

(function(){

	scope.date_range = {

		on_change : function(p_on_change) {

			$( "#from_date" ).change(function() {	  			
				p_on_change();
			});

			$( "#to_date" ).change(function() {
	  			p_on_change();
			});	

		},

		get_from_date: function() {
			return $('#from_date').val();
		},

		get_to_date: function() {
			return $('#to_date').val();
		}


	}

})();


  		