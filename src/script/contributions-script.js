function sendForm() {

    var sent = false;
    var us = $("#user").val();
    var ur = $("#url").val();
    
    $('#queryloading').append('<img class="isloading" src="images/miniloading.gif" />');
    
    $.get("contributions.php", {user: us, wiki: ur}, function(data) {
	$('.isloading').remove();
	$("#result").html(data);
	var values = [];
	var labels = [];
	$("tr").each(function() {
	    values.push(parseInt($("td", this).text(), 10));
	    labels.push($("th", this).text());
	});
	$("#result").fadeIn(500);
    });
    return sent;
}