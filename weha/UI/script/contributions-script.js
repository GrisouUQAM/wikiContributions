function sendForm() {
	var sent = false;
	var us = document.getElementById("user").value;
	var ur = document.getElementById("url").value;
	
	
	$.get("contributions.php", { user: us, wiki: ur}, function (data) {
		$("#result").html(data);
            var values = [],
			labels = [];
			$("tr").each(function () {
				values.push(parseInt($("td", this).text(), 10));
				labels.push($("th", this).text());
			});
            $("#result").fadeIn(500);
		} );
return sent;
}