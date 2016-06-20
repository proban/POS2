var scope = this.scope || {};

(function(){

	scope.loading = {

		on_loading : function (val){

			if(val) {
				$('#btn-submit').text("Loading...");
				$('#btn-submit').addClass("btn-warning");
			}
			else {
				$('#btn-submit').text("Search");	
				$('#btn-submit').removeClass("btn-warning");
			}

		}		
	}


	scope.submit =  {

		on_submit : function(p_on_submit) {

			$('#btn-submit').click(function(e){

				e.preventDefault();

				p_on_submit();

			});
		}

	}


	Number.prototype.format_decimal = function(places, symbol, thousand, decimal) {

		places = (places? places : 2);
		symbol = (symbol? symbol : '');
		thousand = (thousand? thousand : ',');
		decimal = (decimal? decimal : '.');


		places = !isNaN(places = Math.abs(places)) ? places : 2;
		symbol = symbol !== undefined ? symbol : "$";
		thousand = thousand || ",";
		decimal = decimal || ".";
		var number = this, 
		    negative = number < 0 ? "-" : "",
		    i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
		    j = (j = i.length) > 3 ? j % 3 : 0;
		return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
	};


	Number.prototype.format_decimal2 = function(places, symbol, thousand, decimal) {

		places = (places? places : 0);
		symbol = (symbol? symbol : '');
		thousand = (thousand? thousand : ',');
		decimal = (decimal? decimal : '.');


		places = !isNaN(places = Math.abs(places)) ? places : 0;
		symbol = symbol !== undefined ? symbol : "$";
		thousand = thousand || ",";
		decimal = decimal || ".";
		var number = this, 
		    negative = number < 0 ? "-" : "",
		    i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
		    j = (j = i.length) > 3 ? j % 3 : 0;
		return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
	};



	Number.prototype.format_int = function(places, symbol, thousand, decimal) {

		places = (places? places : 0);
		symbol = (symbol? symbol : '');
		thousand = (thousand? thousand : ',');
		decimal = (decimal? decimal : '.');


		places = !isNaN(places = Math.abs(places)) ? places : 2;
		symbol = symbol !== undefined ? symbol : "$";
		thousand = thousand || ",";
		decimal = decimal || ".";
		var number = this, 
		    negative = number < 0 ? "-" : "",
		    i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
		    j = (j = i.length) > 3 ? j % 3 : 0;
		return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
	};

	String.prototype.replaceAll = function (find, replace) {
	    var str = this;
	    return str.replace(new RegExp(find, 'g'), replace);
	};

})();