$(function () {       
    $('#sendRequest').click(function(){        
        $.ajax({
            url: "contributions.php?user="+$("#user").val()
        }).done(function ( data ) {
            $("#result").html(data);
            var values = [],
			labels = [];
			$("tr").each(function () {
				values.push(parseInt($("td", this).text(), 10));
				labels.push($("th", this).text());
			});
            $("#result").fadeIn(500);
        });    
    });    
});