// Browse Projects JS
//
// Patrick Teague 6/14/13
var sm;//player
//List of genres arranged by length (as an optimization)
var _genres = [
	'Bop', 'CCM', 'Dub', 'EDM', 'Emo', 'IDM', 'Pop', 'R&B', 'Rap', 'Ska', 'Acid', 'Data', 'Enka', 'Folk', 'Funk', 'Jazz', 'Punk', 'Rock', 'Soul', 'Surf', 'Trap', 'Anime', 'Asian', 'Blues', 'Cajun', 'Chant', 'Crunk', 'Dance', 'Disco', 'Drone', 'Games', 'Gypsy', 'House', 'Indie', 'J-Pop', 'J-Ska', 'Japan', 'K-Pop', 'Latin', 'Lo-Fi', 'Metal', 'Noise', 'Opera', 'Piano', 'Polka', 'Ragga', 'Salsa', 'Samba', 'Swing', 'Vocal', 'World', 'Bounce', 'Celtic', 'Choral', 'Comedy', 'Disney', 'Easter', 'Europe', 'France', 'French', 'Fusion', 'Gabber', 'Garage', 'German', 'Gospel', 'Grunge', 'Hi-NRG', 'J-Punk', 'J-Rock', 'Jungle', 'Lounge', 'Motown', 'Nature', 'Poetry', 'Praise', 'Raíces', 'Reggae', 'Techno', 'Tejano', 'Thrash', 'Trance', 'Travel', 'Zydeco', 'African', 'Ambient', 'Baroque', 'Chicano', 'Country', 'Doo Wop', 'Dubstep', 'Electro', 'Erotica', 'Gangsta', 'Grinder', 'Healing', 'Hip Hop', 'Holiday', 'J-Synth', 'Karaoke', 'Klezmer', 'Latino ', 'Mexican', 'Minimal', 'New Age', 'New Mex', 'Norteno', 'Novelty', 'Oceania', 'Qawwali', 'Ragtime', 'Spanish', 'Stories', 'Strings', 'Tex-Mex', '3rd Wave', 'Afro-Pop', 'Art Rock', 'Art-Folk', 'Ballroom', 'Big Band', 'Big Beat', 'Brit Pop', 'Broadway', 'Chanukah', 'Conjunto', 'Darkwave', 'Exercise', 'Flamenco', 'Hard Bop', 'Hardcore', 'Hawaiian', 'Illbient', 'Medieval', 'Merengue', 'Musicals', 'Neo Soul', 'New Wave', 'Politics', 'Pop Punk', 'Pop Rock', 'Ranchero', 'Romantic', 'Shoegaze', 'Teen Pop', 'Trip Hop', 'Tropical', 'Acid Jazz', 'Afro-Beat', 'Americana', 'Australia', 'Blue Note', 'Bluegrass', 'Breakbeat', 'Caribbean', 'Christmas', 'Classical', 'Dancehall', 'Didjeridu', 'Dixieland', 'Downtempo', 'Dream Pop', 'Ensembles', 'Eurodance', 'Folk Rock', 'Funk Rock', 'Glam Rock', 'Goth Rock', 'Halloween', 'Hard Rock', 'Indie Pop', 'Jam Bands', 'Kayokyoku', 'Latin Pop', 'Latin Rap', 'Lullabies', 'Portugese', 'Post Punk', 'Power Pop', 'Prog Rock', 'Québécois', 'Queercore', 'Rap Metal', 'Reggaeton', 'Religious', 'Soft Rock', 'Standards', 'Steampunk', 'Surf Rock', 'Trad Jazz', 'Vocal Pop', 'Worldbeat', 'Acid House', 'Adult Film', 'Afro-Cuban', 'Arena Rock', 'Audio Book', 'Avant Jazz', 'Avant Rock', 'Barbershop', 'Blues Rock', 'Bossa Nova', 'Brazillian', 'Club Dance', 'Deep House', 'Electronic', 'Ethio-jazz', 'Fairytales', 'Film Score', 'French Pop', 'German Pop', 'Hair Metal', 'Hard Dance', 'Honky Tonk', 'Horrorcore', 'Indian Pop', 'Indie Rock', 'Industrial', 'Latin Rock', 'Love Songs', 'Meditation', 'Minimalism', 'Mood Music', 'Orchestral', 'Pop Latino', 'Pop Vocals', 'Rave Music', 'Relaxation', 'Riot Grrrl', 'Rockabilly', 'Roots Rock', 'Sing-Along', 'Soundtrack', 'Vocal Jazz', 'Alternative', 'Avant-Garde', 'Black Metal', 'Celtic Folk', 'Dark Techno', 'Death Metal', 'Delta Blues', 'Dirty South', 'Drum & Bass', 'Early Music', 'Electronica', 'Foreign Rap', 'Gangsta Rap', 'General Pop', 'General R&B', 'General Rap', 'German Folk', 'Happy House', 'Hard Trance', 'Heavy Metal', 'Jazz Vocals', 'Latin Jazz ', 'Middle East', 'Progressive', 'Psychedelic', 'Quiet Storm', 'Renaissance', 'Rock & Roll', 'Rock Steady', 'Ska Revival', 'Smooth Jazz', 'Speed Metal', 'Spoken Word', 'Tech Trance', 'Turntablism', 'Acoustic Pop', 'Applications', 'Bachelor Pad', 'Bass Assault', 'Classic Rock', 'College Rock', 'Contemporary', 'European Pop', 'Experimental', 'General Data', 'General Folk', 'General Jazz', 'General Punk', 'General Rock', 'Gothic Metal', 'Hardcore Rap', 'Instrumental', 'Irish Celtic', 'Jackin House', 'Japanese Pop', 'New Acoustic', 'Roots Reggae', 'Scandinavian', 'South Africa', 'Southern Rap', 'Thanksgiving', 'Tribal House', 'World Fusion', 'Central Asian', 'Chamber Music', 'Chicago Blues', 'Christian Pop', 'Christian Rap', 'Classic Blues', 'Country Blues', 'Freestyle Rap', 'General Blues', 'General Books', 'General Dance', 'General House', 'General Latin', 'General Metal', 'General World', 'Hardcore Punk', 'Impressionist', 'Japanese Enka', 'Japanese Folk', 'Japanese Jazz', 'Japanese Rock', 'Marching Band', 'North America', 'Short Stories', 'South America', 'Southern Rock', 'Swing Revival', 'TV Soundtrack', 'Wedding Music', 'Acoustic Blues', 'Ambient Trance', 'Big Band Swing', 'Christian Rock', 'Christmas: Pop', 'Christmas: R&B', 'Country Gospel', 'Crossover Jazz', 'Detroit Techno', 'Drinking Songs', 'East Coast Rap', 'Easy Listening', 'Electric Blues', 'Foreign Cinema', 'General Celtic', 'General Reggae', 'General Spoken', 'General Techno', 'General Trance', 'Hardcore Metal', 'High Classical', 'Japanese Blues', 'Jewish/Israeli', 'Old School Rap', 'Original Score', 'Outlaw Country', 'Standup Comedy', 'Unclassifiable', 'West Coast Rap', 'Alternative Rap', 'Ambient New Age', 'Christian Metal', 'Christmas: Jazz', 'Christmas: Rock', 'Film Soundtrack', 'General Country', 'General Hip Hop', 'General Holiday', 'General New Age', 'Hardcore Techno', 'Japanese Fusion', 'Mainstream Jazz', 'Native American', 'Old School Punk', 'Renaissance Era', 'Southern Gospel', 'Traditional Pop', 'Underground Rap', 'Urban Crossover', 'West Coast Jazz', 'Alternative Folk', 'Alternative Punk', 'Alternative Rock', 'Avant-Garde Jazz', 'British Invasion', 'Children’s Music', 'Classical Guitar', 'Classical Indian', 'Contemporary R&B', 'Eastern European', 'Electronic Rock ', 'Industrial Dance', 'Japanese Karaoke', 'Meditation Music', 'New Orleans Jazz', 'Progressive Rock', 'Psychedelic Rock', 'Television Score', 'Traditional Folk', 'Western European', 'Adult Alternative', 'Alternative Metal', 'Baladas y Boleros', 'Christmas: Modern', 'Classic Christian', 'Contemporary Folk', 'Contemporary Jazz', 'Experimental Rock', 'General Christian', 'General Classical', 'General Religious', 'Instrumental Rock', 'Operating Systems', 'Progressive House', 'Progressive Metal', 'Regional Mexicano', 'Singer-Songwriter', 'Adult Contemporary', 'American Trad Rock', 'Christmas: Classic', 'Contemporary Blues', 'Contemporary Latin', 'General Electronic', 'General Industrial', 'General Soundtrack', 'Japanese Classical', 'Modern Composition', 'Old School Hip Hop', 'Straight Edge Punk', 'Traditional Celtic', 'Traditional Gospel', 'Alternative Country', 'Ambient Electronica', 'Classical Crossover', 'Contemporary Celtic', 'Contemporary Gospel', 'Environmental Music', 'General Alternative', 'Hardcore Industrial', 'Indian Subcontinent', 'Traditional Country', 'Christmas: Classical', 'Christmas: Religious', 'Conjunto Progressive', 'Contemporary Country', 'Aboriginal Australian', 'Christmas: Children’s', 'Old School Industrial', 'Television Soundtrack', 'Traditional Bluegrass', 'Contemporary Bluegrass', 'General Easy Listening', 'General Unclassifiable', 'South/Central American', 'Japanese Unclassifiable', 'Minimalist Experimental', 'General Children’s Music', 'Japanese General Soundtrack', 'Japanese Traditional (Minzoku)', 'Japanese Children’s Song (Doyo)'
	];

var minBudget = 0;
var projectTypes = [];
var projectGenres = [];

//var currentProject = null;
var featuredProjects = [];
var endingProjects = [];
var popularProjects = [];
var moreProjects = [];

var pageProjects = {
	'featured': {
		'shown': true,
		'projects': [], //All the projects loaded for this section
		'onPage': 2, //The number of projects displayed on screen at any point in time
		'index': 0, //The index in projects[] of the first displayed project on the page
		'offset': 0
	},
	'ending': {
		'shown': true,
		'projects': [],
		'onPage': 3,
		'index': 0,
		'offset': 0
	},
	'popular': {
		'shown': true,
		'projects': [],
		'onPage': 3,
		'index': 0,
		'offset': 0
	},
	'more': {
		'shown': true,
		'projects': [],
		'onPage': 6,
		'index': 0,
		'offset': 0
	}
};

var projectRefs = {
	'index': 0,
	'refs': [
		{
			'img': 'images/user_thumb1.jpg',
			'desc': 'Chill Asian dude...'
		},
		{
			'img': 'images/user_thumb2.jpg',
			'desc': 'Indie mustache girl..'
		},
		{
			'img': 'images/you_tube.png',
			'desc': 'Angry purple shirted man...'
		}
	]
};
var currentReference;

String.prototype.capitalize = function(){
	return this.charAt(0).toUpperCase() + this.slice(1);
}
function Reference(opt){
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
		}
	}
}
function addGenreFilter(genre){
	//First check to make sure genre is not already in list of filters
	if(projectGenres.indexOf(genre.toLowerCase()) < 0){
		projectGenres.push(genre.toLowerCase());
		applyFilters();

		var gf = $('<div class="sidebar_result"><span class="sidebar_result_text">'+genre+'</span><span class="sidebar_result_remove">X</span></div>');

		$('.sidebar_results').append(gf);

		gf.find('.sidebar_result_remove').click(function(e){
			projectGenres.splice(projectGenres.indexOf(genre.toLowerCase()), 1);
			applyFilters();

			gf.animate({'opacity': 0}, 200, function(e){
				gf.remove();
			});
		});
	}
}

function createProjectHTML(opt, type){
	//Capitalize each genre since they are stored in lowercase
	var g = "";
	for(var i=0; i<opt.genre.length; i++){
		opt.genre[i] = opt.genre[i].capitalize();
		g += '<span class="browse-container_project_tag">'+opt.genre[i]+( (i+1 < opt.genre.length) ? ',' : '' )+'</span>';
	}
	//return '<div class="browse-container_project '+type+'"><div class="browse-container_project_header"><span class="browse-container_project_title">'+opt.type+'<span class="label">Project</span></span><span class="browse-container_project_budget">$'+opt.budget+'<span class="label">Budget</span></span></div><div class="browse-container_project_pic"><img src="'+opt.picture+'"></div><div class="browse-container_project_name">'+opt.title+'</div><div class="browse-container_project_tags"><div class="icon--small icon--tag"></div>'+g+'</div><div class="browse-container_project_footer"><div class="project-stat entries"><div class="icon--small icon--cd"></div>'+opt.numEntries+'<span class="label">Entries</span></div><div class="project-stat deadline"><div class="icon--small icon--calendar"></div>'+parseInt(opt.daysLeft)+'<span class="label">days</span></div><div class="project-stat watchers"><div class="icon--small icon--pwatchers"></div>'+opt.followers.length+'<span class="label">Followers</span></div></div></div>';
	return '<div class="browse-container_project '+type+'"><div class="browse-container_project_header"><span class="browse-container_project_title">'+opt.type+'<span class="label">Project</span></span><span class="browse-container_project_budget">$'+opt.budget+'<span class="label">Budget</span></span></div><div class="browse-container_project_pic"><div class="browse-container_project_pic_hover"><span></span>VIEW PROJECT DETAILS</div><img src="'+opt.picture+'"></div><div class="browse-container_project_name">'+opt.title+'</div><div class="browse-container_project_tags"><div class="icon--small icon--tag"></div>'+g+'</div><div class="browse-container_project_footer"><div class="project-stat entries"><div class="icon--small icon--cd"></div>'+opt.numEntries+'<span class="label">Entries</span></div><div class="project-stat deadline" title="Deadline in '+parseInt(opt.daysLeft)+' days"><div class="icon--small icon--calendar"></div>'+parseInt(opt.daysLeft)+'<span class="label">days</span></div><div class="project-stat watchers"><div class="icon--small icon--pwatchers"></div>'+opt.followers.length+'<span class="label">Followers</span></div></div></div>';
}

function getProjects(howMany, section, display, callback){
	//Ajax get projects...
	var details = pageProjects[section];
	var genres = $("#filter_genre").val() !== "" ? $("#filter_genre").val().toLowerCase().split(",") : [];
	$.post('/browse/'+section, { 
		'minBudget': minBudget, 
		'projectTypes[]': projectTypes, 
		'projectGenres[]': genres, 
		'numProjects': howMany,
		'offset': (howMany!=0) ? details.offset : 0
	}).done( function(data){
		var response = $.parseJSON(data);
		
		//if there are no results or first query result is less than onPage count then hide the viewAll and right arrow
		if(response.content.sizeof==0 || (details.offset==0 && response.content.sizeof < howMany)){
			$('.main-content_section.'+section).find(".browse_view-all-btn").addClass("hidden").hide();
			$('.main-content_section.'+section).find(".browse_projects_left_btn").addClass("passive");
		}

		if(display){
			$('.browse-container.'+section).html(response.content.html);
			$('.browse-container.'+section+' .project-stat').each(function(index, el) {
				$(this).simpleTooltip({shift: 'sw', tip: $(this).attr("data-tooltip")});
			});
		}

		if(callback)
			callback(response.content.sizeof);
	}); 
}

function embedReference(referenceLink, container){
    var regex = /(\?v=|\&v=|\/\d\/|\/embed\/|\/v\/|\.be\/)([a-zA-Z0-9\-\_]+)/;
    var regexyoutubeurl = referenceLink.match(regex);
    var html = "";
    if (regexyoutubeurl) 
    {
         container.html("<iframe width=\"240\" height=\"170\" src=\"https://www.youtube.com/embed/"+regexyoutubeurl[2]+"\" frameborder=\"0\" allowfullscreen></iframe>");
    }
    else{
		$.getJSON('https://soundcloud.com/oembed?callback=?',
		    {format: 'js', url: referenceLink, iframe: true, maxwidth: '240', maxheight: '170'},
		    function(data) {
		    	container.html(data['html']);
		    }
		)
    }
}

function showReference(){
	currentReference = projectRefs.refs[projectRefs.index];
	if(currentReference.isAudio){
		var refer = new Reference({
			container: $(".tablet_player"),
			id: currentReference.id,
        	audioURL: currentReference.audioURL,
            waveURL: currentReference.waveURL,
        	title: currentReference.title,
        	duration: currentReference.duration,
        	commentsOn : {
        		waveform: false,
        		team: false
        	}
		});
		refer.draw();
		$(".tablet_player").append("<div>"+currentReference.description+"</div>")
		$(".tablet_info_container").hide();
		$(".tablet_player").fadeIn();
	}
	else if(currentReference.extension){
		$('.tablet_pic--second').html('<a href="/uploads/references/'+currentReference.id+'.'+currentReference.extension+'">Download File</a>')
		$('.tablet_info--second').find('.tablet_description').text(currentReference.description);
	}
	else{
		embedReference(currentReference.link, $('.tablet_pic--second'))
		$('.tablet_info--second').find('.tablet_description').text(currentReference.description);
	}
}

function viewProject(project){
	//Ajax get more details about this project...
	$.post('/browse/viewProject', {publicId: project.id}).done(function(data){
		//currentProject = project;
		var response = $.parseJSON(data);
		project = response.content;

		var role = response.content.role;

		var p = $('.popup');
		//Title should be updated with the project title
		document.title = 'Browse Projects - '+project.title+' | Soound';

		//Top Left info
			p.find('.tablet_pic').css('background-image', 'url('+project.picture+')');
			p.find('.tablet_title_new').text(project.title);
			p.find('.tablet_description').first().text(project.description);

		//Sidebar info
			p.find('.tab-second-containe--avatar-name').html('<a href="'+project.posterLink+'">'+project.posterName+'</a>');
			p.find('.tab-second-containe--avatar-pic').css('background-image', 'url('+project.posterPic+')');

			p.find('.tab-second-containe.budget').html('$'+project.budget+' <span class="tab-second-containe---sub-bugdet">budget</span>');
			p.find('.tab-second-containe.submission').html(project.numEntries+' <span class="tab-second-containe---sub-bugdet">submissions</span>');
			p.find('.tab-second-containe.deadline').html(parseInt(project.daysLeft)+' Days <span class="tab-second-containe---sub-bugdet">deadline</span>');

		//Bottom References
			var ref = p.find('.tablet_references');
			projectRefs = {
				'index': 0,
				'refs': project.references
			};

			if(projectRefs.refs.length > 0){
				showReference()
				ref.find('.left_btn_container').css({
					'opacity': 1,
					'visibility': 'visible'
				});
			}
			else {
				ref.find('.tablet_description').text("");
				ref.find('.tablet_pic--second').html("");
				ref.find('.left_btn_container').css({
					'opacity': 0,
					'visibility': 'hidden'
				});
			}
			ref.find('.right_btn_container').css({
				'opacity': 0,
				'visibility': 'hidden'
			});

			projectRefs.index = 0;

		//Sidebar Buttons
		$('#overlay-buttons').empty();
		if( role === "team"){ //Append the go to project page button
			$(".tablet_label").remove();
			var view = $('<a class="tab-second-containe join-project" href="submissions/'+project.id+'">MANAGE PROJECT</a>');
			view.css({'padding-left': 10, 'padding-right': 10});
			$('#overlay-buttons').append(view);
		}
		else{
			if( role === "none" ){
				var follow = $('<a id="follow-button" class="tab-second-containe watch-report">FOLLOW PROJECT</a>');
				$('#overlay-buttons').append(follow);

				follow.click(function(e){
					followProject(); //Follow this project
				});
			} else if( role === "follower"){ //Append the following icon
				var unfollow = $('<a id="unfollow-button" class="tab-second-containe watch-report">UNFOLLOW</a>');
				$('#overlay-buttons').append(unfollow);

				unfollow.click(function(e){
					unfollowProject(); //Unfollow this project
				});
			}
			if( role === "none" || role === "follower" ){ //Append the join project button
				var join = $('<a class="tab-second-containe join-project">JOIN PROJECT</a>');
				$('#overlay-buttons').append(join);

				join.click(function(e){ //Show the join project NDA form
					$('#join-project-nda').css('visibility', 'visible').animate({'opacity': 1},400);
				});
			}
			else if (role == "member"){ //Append the submissions button (for members)
				var submit = $('<a class="tab-second-containe join-project" href="submit/'+project.id+'">SUBMISSIONS</a>');
				$('#overlay-buttons').append(submit);
				$(".tablet_label").remove();
			}
		}

		$('.overlay').fadeIn(200);

	}); 
}

function applyFilters(){
	//Whenever you change the filters, reset the indexes of each section
	$.each(pageProjects, function(type, props){
		props.index = 0;
		props.projects = [];
	});

	$('.browse_projects_right_btn').addClass("passive");

	getProjects(pageProjects.featured.onPage, 'featured', true);
	getProjects(pageProjects.ending.onPage, 'ending', true);
	getProjects(pageProjects.popular.onPage, 'popular', true);
	getProjects(pageProjects.more.onPage, 'more', true);
} 

function joinProject(){
	$.post('/browse/join').done(function(data){
		var url = $.parseJSON(data).url;
		window.location = url; //submitURL
	});
}

function followProject(){
	$.post('/browse/follow');
	$('#follow-button').remove();
	var unfollow = $('<a id="unfollow-button" class="tab-second-containe watch-report">UNFOLLOW</a>');
	$('#overlay-buttons').prepend(unfollow);

	unfollow.click(function(e){
		unfollowProject(); //Unfollow this project
	});
}

function unfollowProject(){
	$.post('/browse/unfollow');
	$('#unfollow-button').remove();
	var follow = $('<a id="follow-button" class="tab-second-containe watch-report">FOLLOW PROJECT</a>');
	$('#overlay-buttons').prepend(follow);

	follow.click(function(e){
		followProject(); //Follow this project
	});
}

$(function(){
	sm = new SooundPlayer();
	applyFilters();

	//Project Listeners
	$(".browse_projects").on("click", ".browse-container_project", function(e){
		viewProject({id: $(this).data("id")});
	});
	
	$(".browse_projects").on({
	    mouseenter: function (e) {
	        $(this).parent().find(".browse-container_project_pic_hover").css({'visibility':'visible'});
	    },
	    mouseleave: function (e) {
	        $(this).parent().find(".browse-container_project_pic_hover").css({'visibility':'hidden'});
	    }		
	},".browse-container_project_name");

	$(".browse_projects").on("click", ".browse-container_project_tag", function(e){
		e.preventDefault();
		addGenreFilter($(this).text().trim().replace(',', ''));
		return false;
	});
	//End Project Listeners

	//Categories Selector
	$('.sidebar_types').children().click(function(e){
		if($(this).hasClass('sidebar_active')) 
			return false;

		$('.sidebar_types a').removeClass('sidebar_active');
		$(this).addClass('sidebar_active');

		switch( $(this).text().toLowerCase() ){
			case 'all':
				$('.main-content_section').fadeIn(400);
				$(".browse_view-all-btn.active").not(".hidden").removeClass("active").trigger("click").show();
				break;
			case 'featured':
				$('.featured').fadeIn(400);
				$(".browse_view-all-btn[data-section='featured']").not(".hidden").addClass("active").trigger("click").hide();
				$('.main-content_section').not('.featured').fadeOut(400);
				break;
			case 'ending soon':
				$('.ending').fadeIn(400);
				$(".browse_view-all-btn[data-section='ending']").not(".hidden").addClass("active").trigger("click").hide();
				$('.main-content_section').not('.ending').fadeOut(400);
				break;
			case 'popular':
				$('.popular').fadeIn(400);
				$(".browse_view-all-btn[data-section='popular']").not(".hidden").addClass("active").trigger("click").hide();
				$('.main-content_section').not('.popular').fadeOut(400);
				break;
			case 'more':
				$('.more').fadeIn(400);
				$(".browse_view-all-btn[data-section='more']").not(".hidden").addClass("active").trigger("click").hide();
				$('.main-content_section').not('.more').fadeOut(400);
				break;
		}

	});
	//End Categories Selector

	//Budget Slider
	$('.sidebar_slider_handle').mousedown(function(e){
		e.preventDefault();
		var max = 2400;
		var handle = $(this);
		var parent = handle.parent();
		var mousemove = function(e){
			var left = e.pageX - parent.offset().left - handle.width()/2;
			
			if(left < -handle.width()/2 ) //left limit
				left = -handle.width()/2;
			else if( left > parent.width()-handle.width()/2 ) //right limit
				left = parent.width()-handle.width()/2;
			
			handle.css({ 'left': left });

			var val = Math.round( max*( (handle.offset().left + handle.width()/2) - parent.offset().left)/parent.width());
			$('.sidebar_slider_value').text('$'+val);
		};

		var mouseup = function(e){
			minBudget = Math.round( max*( (handle.offset().left + handle.width()/2) - parent.offset().left)/parent.width());
			applyFilters();
			$('body').unbind('mousemove', mousemove);
			$('body').unbind('mouseup', mouseup)
		};

		$('body').bind('mousemove', mousemove);
		$('body').bind('mouseup', mouseup);
	});
	//End Budget Slider

	//Project Type Checkbox
	$('.sidebar_checkbox').find('.custom-checkbox_input').click(function(e){
		label = $('label[for="'+this.id+'"]').text();
		label = (label=='Complete Song' ? 'completesongs' : label);
		if( !this.checked )
			projectTypes.splice(projectTypes.indexOf( label.toLowerCase() ), 1);
		else
			projectTypes.push(label.toLowerCase());
		applyFilters();
	});
	//End Project Type Checkbox
	
    $("#filter_genre").tagit({
        availableTags: _genres,
        allowSpaces: true,
        autocomplete: {delay: 0, minLength: 2},
        placeholderText: 'Add genre',
        beforeTagAdded: function(event, ui) {
            if($.inArray(ui.tagLabel, _genres)==-1) return false;
        },
        afterTagAdded: function(){
        	$(".genre_filters").html($(".sidebar_genre_filter .tagit li.tagit-choice").clone(true,true).attr("tabindex","1"));
        	applyFilters();
        }
    });
    $(".genre_filters").on("click", ".tagit-close", function(){
    	$(this).parent().remove();
    	applyFilters();
    });

    $(".genre_filters").on("keydown", ".tagit-choice", function(event){
    	if(event.keyCode==8 || event.keyCode==127){
    		$(this).find(".tagit-close").trigger("click");
    		return false;
    	}
    });
    tagsDeco();

	//Genre Search Box
	$('.sidebar_searchbox').keyup(function(e){
		var limit = 5; //Max search results
		var results = [];
		var search = $(this).val();
		var side = $('.sidebar_search_results');
		side.empty();

		if(search != ""){
			var searchExp = new RegExp(search, 'ig');
			var genre = "";
			for(var i=0; i<genres.length && results.length < limit; i++){
				genre = genres[i];
				
				if( genre.match(searchExp) )
					results.push(genre);
			}
			

			for(var i=0; i<results.length; i++){
				var result = $('<li class="sidebar_search_result"><div class="sidebar_search_result_left"></div><div class="sidebar_search_result_text">'+results[i]+'</div></li>');
				side.append(result);
				if(i+1 < results.length){
					var spacer = $('<li class="sidebar_search_spacer"></li>');
					side.append(spacer);
				}

				result.click(function(e){
					//console.log("Add Genre Filter, "+$(this).text());

					$('.sidebar_searchbox').val("");
					addGenreFilter($(this).text());
				});
			}
		}
	});
	
	$('.sidebar_searchbox').focus(function(e){
		$('.sidebar_search_results').css('visibility', 'visible').animate({'opacity': 1}, 200);
		if($(this).val() != "")
			$(this).keyup();
	});

	$('.sidebar_searchbox').blur(function(e){
		setTimeout(function(){
			$('.sidebar_search_results').animate({'opacity': 0}, 200, function(e){
				$(this).empty().css('visibility', 'hidden');
			});
		}, 100);
	});
	//End Genre Search Box

	//View All Button
	$(".browse_view-all-btn").on("click", function(){
		$this = $(this);
		var name = $(this).data("section");
		if(!$this.hasClass("all")){
			$(".main-content_section."+name).find(".btn-container").hide();
			getProjects(0, name, true);
			$this.text("VIEW LESS").addClass("all");
		}
		else{
			var section = pageProjects[name];
			$(".main-content_section."+name).find(".btn-container").show();
			getProjects(section.onPage, name, true);
			$this.text("VIEW ALL").removeClass("all");
		}


		return false;
	});

	//Section More Projects Arrows
	$('.section-title').find('.browse_projects_left_btn').click(function(e){
		//Move projects to the right...
		$this = $(this);
		if($this.hasClass("passive"))
			return;

		$this.next().removeClass("passive");

		var name = $this.data("section");
		var section = pageProjects[name];

		//offset for new records
		section.offset+=section.onPage;

		//if there are no more record button should become passive
		callback = function(sizeof){
			if(sizeof = 0 || sizeof < section.onPage)
				$this.addClass("passive");
		};

		getProjects(section.onPage, name, true, callback);
	});

	$('.section-title').find('.browse_projects_right_btn').click(function(e){
		//Move projects to the left...
		$this = $(this);
		if($(this).hasClass("passive"))
			return;

		$(this).prev().removeClass("passive");

		var name = $(this).data("section");
		var section = pageProjects[name];

		//offset for new records
		section.offset-=section.onPage;

		//if its the first page button should be passive
		if(section.offset==0)
			$this.addClass("passive");

		getProjects(section.onPage, name, true);
	});

	//Project Browse through References Arrow Listeners
	$('.popup').find('.left_btn_container').click(function(e){
		//Go right (not left.. because vuzum made it this way...)
		e.preventDefault();
		$(".tablet_info_container").show();
		$(".tablet_player").html("").hide();

		$($(this)[0].nextElementSibling).css('visibility', 'visible').animate({'opacity': 1}, 200);

		if(projectRefs.index + 1 < projectRefs.refs.length){
			projectRefs.index++;
			showReference();
		}
		if(projectRefs.index + 1 == projectRefs.refs.length){
			$(this).animate({'opacity': 0}, 200, function(e){
				$(this).css('visibility', 'hidden');
			});
			$('.popup').find('.right_btn_container').animate({'opacity': 1}, 200, function(e){
				$(this).css('visibility', 'visible');
			});
		}
	});

	$('.popup').find('.right_btn_container').click(function(e){
		//Go left (not right.. because vuzum made it this way...)
		e.preventDefault();
		$(".tablet_info_container").show();
		$(".tablet_player").html("").hide();
		
		$($(this)[0].previousElementSibling).css('visibility', 'visible').animate({'opacity': 1}, 200);

		if(projectRefs.index - 1 > -1){
			projectRefs.index--;
			showReference();
		}

		if(projectRefs.index == 0 ){
			$(this).animate({'opacity': 0}, 200, function(e){
				$(this).css('visibility', 'hidden');
			});
			$('.popup').find('.left_btn_container').animate({'opacity': 1}, 200, function(e){
				$(this).css('visibility', 'visible');
			});
		}
	});
	//End Project Browse through References Arrow Listeners


	//NDA form listeners
	$('#nda-accept').click(function(e){
		$('#join-project-nda').animate({'opacity': 0}, 400, function(){$(this).css('visibility', 'hidden');});
		//Join current project
		joinProject();
	});

	$('#nda-decline').click(function(e){
		$('#join-project-nda').animate({'opacity': 0}, 400, function(){$(this).css('visibility', 'hidden');});
	});
	//End View Project Listeners


	$('.project-info_description a').click(function(){
		$(this).addClass('active');
	});

	$('.overlay').bind('click', function(e){
		if( e.target === this ){
			$(this).fadeOut(200, function(e){
				$(this).find('#join-project-nda').css({'visibility': 'hidden', 'opacity': 0});
			});
		}
	});

	$('.sidebar_types a:first').addClass("sidebar_active");
});