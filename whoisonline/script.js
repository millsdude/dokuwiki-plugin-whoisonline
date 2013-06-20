jQuery(document).ready(function(){
	var panel = jQuery('.WIO_onlineWidget .WIO_panel');
	var timeout,timeout2;

	jQuery('.WIO_onlineWidget').hover(
		function(){
			clearTimeout(timeout);
			timeout = setTimeout(function(){panel.trigger('open');},500);
		},
		function(){
			clearTimeout(timeout);
			timeout = setTimeout(function(){panel.trigger('close');},500);
			clearTimeout(timeout2);
			timeout2 = setTimeout(function(){loaded=false;},10000);
		}
	);
	var loaded=false;	// A flag which prevents multiple ajax calls to geodata.php;	
	panel.bind('open',function(){
		panel.slideDown(function(){
			if(!loaded)
			{
				panel.load(DOKU_BASE+'lib/plugins/whoisonline/ajax.php');
				loaded=true;
			}
		});
	}).bind('close',function(){
		panel.slideUp();
	});
	
});