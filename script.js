var app = {

	eventListener: function() {
		$(".name").on("click", function(event){
			var target = $(event.target);
			target.siblings(".name").removeClass("chosen");
			target.addClass("chosen");
			$("#choose-name").removeClass("ghost");
		});

		$("#choose-name").off("click").on("click", function(event){
			event.preventDefault();

			var nameChosen = $(".name.chosen").attr("data-name");
			var type = "POST";
			var url = "/get-secret-santa.php";
			var data = {"name":nameChosen};

			$.ajax({
				type: type,
				url: url,
				data: data,
				success: function(result){
					alert("You are the secret santa for... " + result);
				}
			});
		});
	},

	init: function() {
		this.eventListener();
	}

};

app.init();
