(function($){$(function() {

	function progress(percent) {
		$element = $('#progressBar');
	    var progressBarWidth = parseInt(percent) * $element.width() / 100;
    	$element.find('div').animate({ width: progressBarWidth }, 100).html(parseInt(percent) + "%&nbsp;");
	}

	function import_user(i, n) {
		var start_time = (new Date).getTime();
	    $.ajax({
			type: "GET",
			url: '/import_profiles/' + i.toString(),
			beforeSend: function( xhr ) {
				xhr.setRequestHeader('X-Requested-With', {
					toString: function(){
						return '';
					}
				});
          	},
			timeout: 180000,
			success: function (data) {
				var end_time = (new Date).getTime();
				var log = parseInt((end_time - start_time) / 100).toString() + ' c: Import profile with uid ' + data['id'] + ' - ' + data['result'];
				console.log(log);
				$('#showlogs').append(log + '\n'); 
				start_time = end_time;
				progress((i+1)*100/n);
	            if (i+1 <= n) import_user(i+1, n);
			},
			error: function (xhr, status, error) {
				console.log(xhr.responseText, '/import_profiles/' + i.toString());
				if (i <= n) import_user(i, n);
			}
		});
	}

	$('#start_import').click(function(event) {
		var n = parseInt($(this).data().count);
		import_user(1, n);
		event.preventDefault();
	});

 })})(jQuery);
