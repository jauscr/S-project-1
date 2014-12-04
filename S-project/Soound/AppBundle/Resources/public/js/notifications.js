var notifications = [];

function Notification(config){

	var color = "#2ECC71";
	if(config.type === "cd")
		color = "#F39C12";
	else if(config.type === "remove")
		color = "#E74C3C";

	var cont = $('<div class="notification-cont"></div');
	var icon = $('<div class="activity-icon white"></div>');

	var content = '<div class="notification-content">';
	for(var i=0; i<config.content.length; i++){
		var ref = config.content[i].ref;
		var text = config.content[i].text;

		if(ref){
			content += '<a class="notification-link" href="'+ref.link+'">'+ref.name+'</a>';
		}
		if(text){
			content += ' '+text;
		}
	}
	content += '</div>';
	content = $(content);
	//var msg = $('<div class="notification-content">'+config.msg+'</div>');

	cont.css('background-color', color).append(icon, content);

	this.draw = function(){
		config.cont.append(cont);
		cont.css('opacity', 0);
		cont.animate({'opacity': 1}, 400, function(){
			
			setTimeout(function(){
				cont.animate({'opacity': 0}, 400, function(){
					config.cont.empty();
					notifications.shift();
					if(notifications.length > 0)
						notifications[0].draw();
				});

			}, 3000);
		});
	};

	if(notifications.length < 1)
		this.draw();

	notifications.push(this);

	$('.activity-count').text( parseInt($('.activity-count').text()) + 1);

	if( !$('.activity-count').hasClass('unread') ){
		$('.activity-count').addClass('unread');
		$('.icon.icon-activity').addClass('unread');
	}

	var html = '<li><div class="oo-ui-activity-item">';
	var done = false;
    for(var i=0; i<config.content.length; i++){
        if (config.content[i].ref.picture != 'undefined' && done === false){
            html += '<a class="activity-picture" href="'+config.content[i].ref.link+'"><img src="'+config.content[i].ref.picture+'"/></a>';
            done = true;
        }
    }
    if (done === false){
        html+='<div class="activity-icon not-read envelope"></div>';
    }

    html+='<div class="activity-content">';

	for(var i=0; i<config.content.length; i++){
		var ref = config.content[i].ref;
		var text = config.content[i].text;

		if(ref)
			html += '<span class="activity-link"><a class="'+(i > 0 ? 'inner' : 'first')+'" href="'+ref.link+'">'+ref.name+'</a></span>';
		if(text)
			html += '<span class="activity-text '+(i > 0 ? 'inner' : 'first')+'">'+text+'</span>';
	}
	html += '</div></div></li>';

	$('#header-activity-list').prepend(html);
	
}