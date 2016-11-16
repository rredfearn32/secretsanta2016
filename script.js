var app = {

	eventListener: function() {
		$("input[type=radio]").on("click", function(event){
			$("button").removeClass("ghost");
		});

		$("form").on("submit", function(event){
			event.preventDefault();

			var nameChosen = $(event.target).serialize().replace("recipient=", "");
			var type = "POST";
			var url = "/get-secret-santa.php";
			var data = {"name":nameChosen};

			$.ajax({
				type: type,
				url: url,
				data: data,
				success: function(result){
					console.log(result);
				}
			});
		});
	},

	init: function() {
		this.eventListener();
	}

};

app.init();
