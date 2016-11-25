var app = {

	eventListener: function() {
		$(".name").on("click", function(event){
			var target = $(event.target);
			target.siblings(".name").removeClass("chosen").addClass("not-chosen");
			target.removeClass("not-chosen").addClass("chosen");
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
					var resultText = "<p>You are the secret santa for...</p><p class='result-name'>" + result + "</p>" +
					"<a href='mailto:?subject=Secret Santa 2016&body=You are the secret santa for " + result + "' target='_blank'>Email To Myself</a>";

					$("ul").slideUp();
					$("#choose-name").addClass("result").html(resultText).off("click");
				}
			});
		});
	},

	init: function() {
		this.eventListener();
	}

};

app.init();
