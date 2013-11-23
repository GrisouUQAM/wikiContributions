function sendForm() {
	var sent = false;
	var us = document.getElementById("user").value;
	var ur = document.getElementById("url").value;
	var loader = document.getElementById("queryloading");
        
    loader.innerHTML = "<img src='images/miniloading.gif' />"
	
	$.get("contributions.php", { user: us, wiki: ur}, function (data) {
		loader.innerHTML = "";
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
function sendFormIntervention() {

	var sent = false;
	var us = document.getElementById("spellcheckinput").value;
	var utilisateur=document.getElementById("user").value;
	var ur = document.getElementById("url").value;
	var loader = document.getElementById("queryloading");
    loader.innerHTML = "<img src='images/miniloading.gif' />"
	
	$.get("articles.php", { spellcheckinput: us, wiki: ur,user:utilisateur}, function (data) {
	
		loader.innerHTML = "";
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
