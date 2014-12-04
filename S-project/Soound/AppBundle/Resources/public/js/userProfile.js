var sm;
var openProjects = [];
var closedProjects = [];
var followingProjects = [];
var memberOfProjects = [];

var pageCredits = {
	'credits': {
		'credits': [],
		'onPage': 1,
		'index': 0
	},
	'winner': {
		'credits': [],
		'onPage': 1,
		'index': 0
	}
};

var pageProjects = {
	'open': {
		'projects': [], //All the projects loaded for this section
		'onPage': 3, //The number of projects displayed on screen at any point in time
		'index': 0 //The index in projects[] of the first displayed project on the page
	},
	'closed': {
		'projects': [],
		'onPage': 3,
		'index': 0
	},
	'following': {
		'projects': [],
		'onPage': 3,
		'index': 0
	},
	'memberOf': {
		'projects': [],
		'onPage': 3,
		'index': 0
	}
};

String.prototype.capitalize = function(){ return this.charAt(0).toUpperCase() + this.slice(1); }

function Credit(opt){
	var config = opt;
	var self = this;

	this.setOption = function(key, value){
		config[key] = value;
	};

	this.draw = function(){
		if( config.hasOwnProperty('container') ){
			sm.addSong({
				id: config.id,
            	songURL: config.audioURL,
                waveURL: config.waveURL,
            	title: config.title,
            	duration: config.duration,
            	commentsOn : {
            		waveform: false,
            		team: false
            	}
			}, config.container);

			//Now add in the credit specific details
			var detailsCont = $('<div class="credit-details-container"></div>');
			var rolesCont = $('<div class="credit-roles-container"><span class="credit-roles-tag"></span>Credit:<span class="credit-details-text">'+config.roles+'</span></div>');
			var descrCont = $('<div class="credit-description-container"><span class="credit-description-tag"></span>Short Description:<span class="credit-details-text">'+config.description+'</span></div>');

			detailsCont.append(rolesCont, descrCont);

			config.container.append(detailsCont);

			if(config.container.css('display') === "none")
				config.container.show(400);
		}
	}
}

function createProjectHTML(opt, type){
	//Capitalize each genre since they are stored in lowercase
	var g = "";
	for(var i=0; i<opt.genre.length; i++){
		opt.genre[i] = opt.genre[i].capitalize();
		g += '<span class="browse-container_project_tag">'+opt.genre[i]+( (i+1 < opt.genre.length) ? ',' : '' )+'</span>';
	}

	return '<div class="browse-container_project '+type+'"><div class="browse-container_project_header"><span class="browse-container_project_title">'+opt.type+'<span class="label">Project</span></span><span class="browse-container_project_budget">$'+opt.budget+'<span class="label">Budget</span></span></div><div class="browse-container_project_pic"><img src="'+opt.picture+'"></div><div class="browse-container_project_name">'+opt.title+'</div><div class="browse-container_project_tags"><div class="icon--small icon--tag"></div>'+g+'</div><div class="browse-container_project_footer"><div class="project-stat entries"><div class="icon--small icon--cd"></div>'+opt.numEntries+'<span class="label">Entries</span></div><div class="project-stat deadline"><div class="icon--small icon--calendar"></div>'+parseInt(opt.daysLeft)+'<span class="label">days</span></div><div class="project-stat watchers"><div class="icon--small icon--pwatchers"></div>'+opt.followers+'<span class="label">Followers</span></div></div></div>';
}

function addProjectListeners(project, details){
	project.click(function(e){
		$.post(/*'profile/*/'project', {'projectID': details.id}).done(function(data){
			window.location = $.parseJSON(data).url;
		});
		//viewProject(details);
	});
}

function getProjects(howMany, section, display){
	var details = pageProjects[section];
	$.post(/*'profile/'+*/section, {
		'howMany': howMany,
		'offset': details.projects.length,
		'publicId': profilePublicId
	}).done( function(data){
		var response = $.parseJSON(data);

		//Add all the projects returned to the projects array
		for(var i=0; i<response.content.length; i++)
			details.projects.push( response.content[i] );

		if(display){
			if( details.projects.length)
				$('.browse-container.'+section).empty();
			for(var i=0; i<details.projects.length && i<details.onPage; i++){
				var proj = createProjectHTML(details.projects[i], section);
				proj = $(proj);
				addProjectListeners(proj, details.projects[i]);
				$('.browse-container.'+section).append(proj);
			}
		}
	});
}

function getCredits(howMany, section, display){
	var details = pageCredits[section];
	$.post(/*'profile/'+*/section, {
		'howMany': howMany,
		'offset': details.credits.length,
		'publicId': profilePublicId
	}).done( function(data){
		var response = $.parseJSON(data);
	
		//Add all the credits returned to the credits array
		for(var i=0; i<response.content.length; i++)
			details.credits.push( response.content[i] );

		if(display){
			if(response.content.length)
				$('.browse-container.'+section).empty();
			for (var i=0; i<response.content.length && i<details.onPage; i++) {

				var cred = new Credit(details.credits[i]);
				var credCont = $('<div class="credit-container"></div>');
				$('.browse-container.'+section).append(credCont)
				cred.setOption( "container", credCont );
				cred.draw();

				details.credits.push( cred );
			}
		}
	});
}

$(function(){

	//Section More Projects Arrows
	$('.section-title').find('.browse_projects_left_btn').click(function(e){
		//Move projects to the right...

		$($(this)[0].nextElementSibling).css('visibility', 'visible').animate({'opacity': 1}, 200);

		var name = $(this).data("section");
		var section = pageProjects[name];

		//Get onPage more projects for this section if necessary
		if(section.index + section.onPage*2 >= section.projects.length){
			getProjects(section.onPage, name, false);
		}

		//Animate out the current section
		/*
		$('.browse-container.'+name).children().animate({'width': 0, 'opacity': 0}, 200, function(e){
			$(this).remove();
		});
		*/
		$('.browse-container.'+name).empty();
		setTimeout(function(e){
			//Create the html/event listeners for the next section, then animate in
			section.index += section.onPage;
			for(var i=0; i<section.onPage; i++){
				var project = createProjectHTML( section.projects[i+section.index], name );
				project = $(project);
				addProjectListeners(project, section.projects[i+section.index] );
				$('.browse-container.'+name).append(project);
				
			}
		}, 0);
	});

	$('.section-title').find('.browse_projects_right_btn').click(function(e){
		//Move projects to the left...

		var name = $(this).data("section");
		var section = pageProjects[name];

		//Only go left if there are more projects to load left...
		if(section.index-section.onPage > -1){
			$('.browse-container.'+name).empty();
	
			setTimeout(function(e){
				//Create the html/event listeners for the next section, then animate in
				section.index -= section.onPage;
				for(var i=0; i<section.onPage; i++){
					var project = createProjectHTML( section.projects[i+section.index], name );
					project = $(project);
					addProjectListeners(project, section.projects[i+section.index] );
					$('.browse-container.'+name).append(project);
				}
			}, 0);
		}
		if(section.index-section.onPage == 0 ){
			$(this).animate({'opacity': 0}, 200, function(e){
				$(this).css('visibility', 'hidden');
			});
		}
	});

	sm = new SooundPlayer();
	
	//Get of this user's credits (Soound and Uploaded)
	for(var val in pageCredits)
		getCredits(pageCredits[val].onPage*2, val, true);

	//Get all relevant projects
	for(var val in pageProjects){
		getProjects(pageProjects[val].onPage*2, val, true);
	}

	$('#profile-share-button').click(function(e){
		//Display modal with url to user's profile
		$('.overlay').show();
		$('#popup-link').select();
	});

	$('#popup-share-btn').click(function(e){
		$('#popup-link').select();
		if(window.clipboardData && clipboardData.setData){
			clipboardData.setData('text', $('#popup-link').val());
		}
	});

	$('.overlay').click(function(e){
		$(this).hide();
	});

	$('.popup').click(function(e){
		e.preventDefault();
		return false;
	});

	if(!(window.clipboardData && clipboardData.setData) )
		$('#popup-share-btn').text('SELECT LINK');
	
	//Walkthrough
	if(Walkthrough){
		Walkthrough.add({
			modal: true,
			title: 'Welcome to your profile page!',
			text: 'Here you can post credits for your past work.',
			delay: 1000,
			fadeIn: 400
		}).start();
	}
})