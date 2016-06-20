var scope = this.scope || {};

(function(){

	scope.date_range_new = function(from_element, to_element) {

		this.from_element = from_element;
		this.to_element = to_element;

		this.on_change = function(p_on_change) {

			$(this.from_element).change(function() {	  			

				if(p_on_change) {
					p_on_change();	
				}

			});

			$(this.to_element).change(function() {

	  			if(p_on_change) {
					p_on_change();						
				}

			});	

		};

		this.get_from_date = function() {
			return $(this.from_element).val();
		};

		this.get_to_date = function() {
			return $(this.to_element).val();
		};


	}

})();


  		