var submissions = [];
var hqFiles = [];
var sooundPlayer = new SooundPlayer();

function hqFile(opt){
	var status = opt.status ? opt.status : "pending";
	var cont = $('<a class="hqFile-cont" href="downloadHQFile/'+opt.id+'"><div class="hqFile-status '+status+'">'+status+'</div><div class="hqFile-img '+status+'"></div><div class="hqFile-name">'+(opt.name+'.'+opt.extension)+'</div></a>');
	opt.cont.append(cont);
}

function getPeople(type, callback){
	$.post('projectOwnerPeople', {type: type}).done(function(data){
		var response = $.parseJSON(data);
		$('#profiles').empty().fadeOut();

		if(type === "team"){
			$('#invite-team-cont').css('display', 'inline-block');
			var emails = "";
			var show = false;
			if(response.content.length>0){
				for(var i=0; i<response.content.length; i++){
					emails += '<div class="teamEmail accepted pull-left"><span class="icon-accepted pull-left"></span><div class="pull-left">'+response.content[i].email+'</div><div class="removeTeam" data-email="'+response.content[i].email+'"><span class="icon-remove pull-left"></span><span class="text">Remove</span></div></div>';
				}
				show = true;
			}
			if(response.pendingTeam.length > 0){
				for(var i=0; i<response.pendingTeam.length; i++){
					emails += '<div class="teamEmail pending pull-left"><span class="icon-pending pull-left"></span><div class="pull-left">'+response.pendingTeam[i]+'</div><div class="removeTeam" data-email="'+response.pendingTeam[i]+'"><span class="icon-remove pull-left"></span><span class="text">Remove</span></div></div>';
				}
				show = true;
			}

			if(show){
				var emailsCont = $(".invitedUsers");
				emailsCont.html(emails).addClass("show");
				emailsCont.next().addClass("addMore");
				emailsCont.parent().addClass("noPadding");
			}

		}
		//console.log(response);
		for(var i=0; i<response.content.length; i++){
			var user = $('<div id="u'+response.content[i].publicId+'" class="user-container '+((i+1)%4==0 ? 'last' : '')+'"><img src="'+response.content[i].picture+'" class="user-picture"><div class="user-name">'+response.content[i].name+'</div></div>');
			$('#profiles').append( user );
		}
		$('#profiles').fadeIn(200);

		if(callback)
			callback();
	});
}

function getTopSubmissions(){
	$.post('topSubmissions').done(function(data){
		var response = $.parseJSON(data);

		//Make a small player for each top submission
		for(var i=0; i<response.content.length; i++){
			var sub = response.content[i];
			var topRatedSubmission = $('<div class="top-rated-submission"></div>');
			$('#top-rated-submissions').append(topRatedSubmission);
			sooundPlayer.addSong({
				id: sub.id,
				userRating: sub.userRating,
				avgRating: sub.avgRating,
				songURL: sub.songURL,
				waveURL: sub.waveURL,
				title: sub.title,
				artist: sub.artist,
				artistUrl: sub.artistUrl,
				commentsOn: {
					waveform: false,
					team: false
				}
			}, topRatedSubmission);
		}

	});
}

function getSubmissions(type, callback){
	for( var i=0; i<submissions.length; i++) //Remove all submissions
		submissions[i].remove();

	submissions = [];
	$.post('projectOwnerSubmissions', {type: type}).done(function(data){
		$('#players').empty().fadeOut();
		var response = $.parseJSON(data);
		//console.log(response);

		if(response.content.length === 0){
			$('#players').append('<div class="no-results-cont"><span class="none-available">NONE AVAILABLE</span></div>');
		}

		for(var i=0; i<response.content.length; i++){
			var submission = new Submission({
				role: response.content[i].role,
				publicId: response.content[i].publicId,
				cont: $('#players'),
				sooundPlayer: sooundPlayer,
				submitable: false,
				commentsOn: {
					waveform: true, //Only if this user is owner or submitter
					team: true  //Only if this user is a team member or owner
				},
				revisions: response.content[i].revisions
			});
			submission.listenForRevisions();
			submissions.push(submission);
		}
		$('#players').fadeIn(200);

		if(callback)
			callback();
	});
}

function showDesc($this, type){
    var pos = extractNumber($this.attr("id"));

    if(checkURL($this.val()))
        $("#"+type+"_description_"+pos).slideDown();
    else
        $("#"+type+"_description_"+pos).slideUp();
}

function Reference(opt){
    var config = opt;
    var self = this;

    this.setOption = function(key, value){
        config[key] = value;
    };

    this.draw = function(){
        if( config.hasOwnProperty('container') ){
            config.sooundPlayer.addSong({
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

function embedReference(referenceLink, container){
    var regex = /(\?v=|\&v=|\/\d\/|\/embed\/|\/v\/|\.be\/)([a-zA-Z0-9\-\_]+)/;
    var regexyoutubeurl = referenceLink.match(regex);
    var html = "";
    if (regexyoutubeurl) 
    {
         container.html("<iframe width=\"480\" height=\"350\" src=\"https://www.youtube.com/embed/"+regexyoutubeurl[2]+"\" frameborder=\"0\" allowfullscreen></iframe>");
    }
    else{
        $.getJSON('https://soundcloud.com/oembed?callback=?',
            {format: 'js', url: referenceLink, iframe: true, maxwidth: '480', maxheight: '340'},
            function(data) {
                container.html(data['html']);
            }
        )
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var chart = window.chart = new EasyPieChart(document.querySelector('.chart'), {
        barColor:'#ebebeb',
        trackColor:'#787878',
        scaleColor: false,
        lineWidth: 10,
        lineCap:'square',
        size: 135,
        onStart: function(from, to) {
            $({countNum: $('.chart').data("days-total")}).animate({countNum: $('.chart').data("days-left")}, {
              duration: 1500,
              easing:'swing',
              step: function() {
                $('.chart .percent').text(Math.floor(this.countNum));
              },
              complete: function() {
                $('.chart .percent').text(this.countNum);
              }
            });
        }
    });
});
function createReference(pos){
    pos = pos + 1;
    var contStart = '<div class="reference-container reference pull-left">';
    var newLink='<label class="title gray">LINK: </label>'+
        '<div class="highlight-input input-provide-details">'+
        '<input type="text" name="project_reference['+pos+'][link]" id="link_preference_'+pos+'" class="link_preference" placeholder="Please enter a link or upload a file"  onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'Please enter a link or upload a file\'"/>'+
        '<div class="project_input_img gray-button upload_reference project-reference-clickable'+pos+'" id="project-reference-dropzone'+pos+'" style="width:35px !important;">'+
            '<img class="upload_logo project-reference-clickable'+pos+'" src="/bundles/sooundapp/css/images/upload_img.png">'+
        '</div>'+
        '<input type="hidden" id="references_hasfile'+pos+'" name="project_reference['+pos+'][hasFile]" value="0">'+
    '</div>';

    var newDesc='<div id="ref_description_'+pos+'" style="display:none" class="desc_container">'+
                    '<div class="link-box-arrow"></div>'+
                    '<div class="link-box">'+
                        '<div class="link-description">'+
                            '<textarea onblur="this.placeholder = \'Point out the things you like in this reference that could help create your sound\'" onfocus="this.placeholder = \'\'" placeholder="Point out the things you like in this reference that could help create your sound" rows="5" class="reference_text" id="reference_text_'+pos+'" name="project_reference['+pos+'][desc]"></textarea>'+
                        '</div>'+
                    '</div>'+
                '</div>';
    var separator='<div class="separator" style="margin: 10px 0 10px 0;"></div>';
    var contEnd = '</div>';
    var html = separator+contStart+newLink+newDesc+contEnd;
    return html
}

function createProjectFile(pos){
    pos = pos + 1;
    var contStart = '<div class="reference-container reference pull-left">';
    var newLink='<label class="title gray">LINK: </label>'+
        '<div class="highlight-input input-provide-details">'+
        '<input type="text" name="project_files['+pos+'][link]" id="link_files_'+pos+'" class="link_project_file" placeholder="i.e. http://myLinkProjectFile"  onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'i.e. http://myLinkProjectFile\'"/>'+
        '<div class="project_input_img gray-button upload_project_file project-file-clickable'+pos+'" id="project-file-dropzone'+pos+'" style="width:35px !important;">'+
            '<img class="upload_logo project-file-clickable'+pos+'" src="/bundles/sooundapp/css/images/upload_img.png">'+
        '</div>'+
        '<input type="hidden" id="files_hasfile'+pos+'" name="project_files['+pos+'][hasFile]" value="0">'+
    '</div>';

    var newDesc='<div id="file_description_'+pos+'" style="display:none" class="desc_container">'+
                    '<div class="link-box-arrow"></div>'+
                    '<div class="link-box">'+
                        '<div class="link-description">'+
                            '<textarea onblur="this.placeholder = \'Point out the things you like in this reference that could help create your sound\'" onfocus="this.placeholder = \'\'" placeholder="Point out the things you like in this reference that could help create your sound" rows="5" class="file_text" id="file_text_'+pos+'" name="project_files['+pos+'][desc]"></textarea>'+
                        '</div>'+
                    '</div>'+
                '</div>';
    var separator='<div class="separator" style="margin: 10px 0 10px 0;"></div>';
    var contEnd = '</div>';
    var html = separator+contStart+newLink+newDesc+contEnd;
    return html;
}
function projectFileZone(pos){
    var projectFilesZone = new Dropzone("#project-file-dropzone"+pos, {

        url: "uploadFile",
        maxFilesize: 5, //MB
        uploadMultiple: false,
        clickable: '.project-file-clickable'+pos,
        init: function(){
            this.on("addedfile", function(file) { 
                $("#link_project_file_"+pos).val(file.name).attr("readonly","true");
                $("#file_text_"+pos).slideDown();
                $("#files_hasfile"+pos).val("1");
            });
            this.on("error", function(file, message){
            });
            this.on("success", function(file, data){
                //$('#upload-progress-status').text("completed");
                var response = $.parseJSON(data);
                console.log(response);
                if(response.msg === "ok"){
                }
                
            });
        }
    });
}
function projectReferenceZone(pos){
    var projectFilesZone = new Dropzone("#project-reference-dropzone"+pos, {

        url: "uploadReference",
        maxFilesize: 5, //MB
        uploadMultiple: false,
        acceptedFiles: ".wav,.m4a,.mp3,.aac",
        clickable: '.project-reference-clickable'+pos,
        init: function(){
            this.on("addedfile", function(file) { 
                $("#link_preference_"+pos).val(file.name).attr("readonly","true");
                $("#ref_description_"+pos).slideDown();
                $("#references_hasfile"+pos).val("1");
            });
            this.on("error", function(file, message){
            });
            this.on("success", function(file, data){
                //$('#upload-progress-status').text("completed");
                var response = $.parseJSON(data);
                console.log(response);
                if(response.msg === "ok"){
                }
                
            });
        }
    });
}
$(function(){
	$('#project-submenu a').last().simpleTooltip({shift: 'sw',tip: 'Area for exchanging files after selecting a winner'});

	projectReferenceZone(1);
    projectFileZone(1);
    $(".references").on("keyup", ".link_preference" ,function(){
        showDesc($(this), 'ref');
    });

    $(".project_files").on("keyup", ".link_project_file" ,function(){
        showDesc($(this), 'file');
    });

    $("#add-reference").on("click", function(){
    	var flag = 0;
        $('.reference_text, .link_preference').each(function(){
            if (!isEmpty(this)){
                $(this).attr('placeholder','Complete this field to add one or more references.');
                $(this).attr('style','border: solid #E74C3C !important');
                flag++;
            }else{
                $(this).removeAttr('style');
            }
        });

        if(flag == 0){
        	var pos = extractNumber($(".link_preference").last().attr("id"));
        	$(".references .reference-container").last().after(createReference(pos));
        	projectReferenceZone(pos+1);
        }

    });

    $("#add-project-file").on("click", function(){
        var flag = 0;
        $('.file_text, .link_project_file').each(function(){
            if (!isEmpty(this)){
                $(this).attr('placeholder','Complete this field to add one or more files.');
                $(this).attr('style','border: solid #E74C3C !important');
                flag++;
            }else{
                $(this).removeAttr('style');
            }
        });

        if(flag == 0){
            var pos = extractNumber($(".link_project_file").last().attr("id"));
            $(".project_files .reference-container").last().after(createProjectFile(pos));
            projectFileZone(pos+1);
        }

    });

    $('html').click(function() {
        $('.project_properties_1, .project_properties_2, .project_properties_3, .project_properties_4, .project_properties_5').removeClass('active');
    });

    $('.project_properties_1, .project_properties_2, .project_properties_3, .project_properties_4, .project_properties_5').click(function(event){
        event.stopPropagation();
    });

    $(".detail-container").on("focus", ".tagit .ui-autocomplete-input", function(){
    	$(this).parent().parent().attr("style","border-color:#ebebeb !important");
    })

    $(".detail-container").on("blur", ".tagit .ui-autocomplete-input", function(){
    	$(this).parent().parent().attr("style","");
    })

	//getTopSubmissions();
    _genres = [
            'Bop', 'CCM', 'Dub', 'EDM', 'Emo', 'IDM', 'Pop', 'R&B', 'Rap', 'Ska', 'Acid', 'Data', 'Enka', 'Folk', 'Funk', 'Jazz', 'Punk', 'Rock', 'Soul', 'Surf', 'Trap', 'Anime', 'Asian', 'Blues', 'Cajun', 'Chant', 'Crunk', 'Dance', 'Disco', 'Drone', 'Games', 'Gypsy', 'House', 'Indie', 'J-Pop', 'J-Ska', 'Japan', 'K-Pop', 'Latin', 'Lo-Fi', 'Metal', 'Noise', 'Opera', 'Piano', 'Polka', 'Ragga', 'Salsa', 'Samba', 'Swing', 'Vocal', 'World', 'Bounce', 'Celtic', 'Choral', 'Comedy', 'Disney', 'Easter', 'Europe', 'France', 'French', 'Fusion', 'Gabber', 'Garage', 'German', 'Gospel', 'Grunge', 'Hi-NRG', 'J-Punk', 'J-Rock', 'Jungle', 'Lounge', 'Motown', 'Nature', 'Poetry', 'Praise', 'Raíces', 'Reggae', 'Techno', 'Tejano', 'Thrash', 'Trance', 'Travel', 'Zydeco', 'African', 'Ambient', 'Baroque', 'Chicano', 'Country', 'Doo Wop', 'Dubstep', 'Electro', 'Erotica', 'Gangsta', 'Grinder', 'Healing', 'Hip Hop', 'Holiday', 'J-Synth', 'Karaoke', 'Klezmer', 'Latino ', 'Mexican', 'Minimal', 'New Age', 'New Mex', 'Norteno', 'Novelty', 'Oceania', 'Qawwali', 'Ragtime', 'Spanish', 'Stories', 'Strings', 'Tex-Mex', '3rd Wave', 'Afro-Pop', 'Art Rock', 'Art-Folk', 'Ballroom', 'Big Band', 'Big Beat', 'Brit Pop', 'Broadway', 'Chanukah', 'Conjunto', 'Darkwave', 'Exercise', 'Flamenco', 'Hard Bop', 'Hardcore', 'Hawaiian', 'Illbient', 'Medieval', 'Merengue', 'Musicals', 'Neo Soul', 'New Wave', 'Politics', 'Pop Punk', 'Pop Rock', 'Ranchero', 'Romantic', 'Shoegaze', 'Teen Pop', 'Trip Hop', 'Tropical', 'Acid Jazz', 'Afro-Beat', 'Americana', 'Australia', 'Blue Note', 'Bluegrass', 'Breakbeat', 'Caribbean', 'Christmas', 'Classical', 'Dancehall', 'Didjeridu', 'Dixieland', 'Downtempo', 'Dream Pop', 'Ensembles', 'Eurodance', 'Folk Rock', 'Funk Rock', 'Glam Rock', 'Goth Rock', 'Halloween', 'Hard Rock', 'Indie Pop', 'Jam Bands', 'Kayokyoku', 'Latin Pop', 'Latin Rap', 'Lullabies', 'Portugese', 'Post Punk', 'Power Pop', 'Prog Rock', 'Québécois', 'Queercore', 'Rap Metal', 'Reggaeton', 'Religious', 'Soft Rock', 'Standards', 'Steampunk', 'Surf Rock', 'Trad Jazz', 'Vocal Pop', 'Worldbeat', 'Acid House', 'Adult Film', 'Afro-Cuban', 'Arena Rock', 'Audio Book', 'Avant Jazz', 'Avant Rock', 'Barbershop', 'Blues Rock', 'Bossa Nova', 'Brazillian', 'Club Dance', 'Deep House', 'Electronic', 'Ethio-jazz', 'Fairytales', 'Film Score', 'French Pop', 'German Pop', 'Hair Metal', 'Hard Dance', 'Honky Tonk', 'Horrorcore', 'Indian Pop', 'Indie Rock', 'Industrial', 'Latin Rock', 'Love Songs', 'Meditation', 'Minimalism', 'Mood Music', 'Orchestral', 'Pop Latino', 'Pop Vocals', 'Rave Music', 'Relaxation', 'Riot Grrrl', 'Rockabilly', 'Roots Rock', 'Sing-Along', 'Soundtrack', 'Vocal Jazz', 'Alternative', 'Avant-Garde', 'Black Metal', 'Celtic Folk', 'Dark Techno', 'Death Metal', 'Delta Blues', 'Dirty South', 'Drum & Bass', 'Early Music', 'Electronica', 'Foreign Rap', 'Gangsta Rap', 'General Pop', 'General R&B', 'General Rap', 'German Folk', 'Happy House', 'Hard Trance', 'Heavy Metal', 'Jazz Vocals', 'Latin Jazz ', 'Middle East', 'Progressive', 'Psychedelic', 'Quiet Storm', 'Renaissance', 'Rock & Roll', 'Rock Steady', 'Ska Revival', 'Smooth Jazz', 'Speed Metal', 'Spoken Word', 'Tech Trance', 'Turntablism', 'Acoustic Pop', 'Applications', 'Bachelor Pad', 'Bass Assault', 'Classic Rock', 'College Rock', 'Contemporary', 'European Pop', 'Experimental', 'General Data', 'General Folk', 'General Jazz', 'General Punk', 'General Rock', 'Gothic Metal', 'Hardcore Rap', 'Instrumental', 'Irish Celtic', 'Jackin House', 'Japanese Pop', 'New Acoustic', 'Roots Reggae', 'Scandinavian', 'South Africa', 'Southern Rap', 'Thanksgiving', 'Tribal House', 'World Fusion', 'Central Asian', 'Chamber Music', 'Chicago Blues', 'Christian Pop', 'Christian Rap', 'Classic Blues', 'Country Blues', 'Freestyle Rap', 'General Blues', 'General Books', 'General Dance', 'General House', 'General Latin', 'General Metal', 'General World', 'Hardcore Punk', 'Impressionist', 'Japanese Enka', 'Japanese Folk', 'Japanese Jazz', 'Japanese Rock', 'Marching Band', 'North America', 'Short Stories', 'South America', 'Southern Rock', 'Swing Revival', 'TV Soundtrack', 'Wedding Music', 'Acoustic Blues', 'Ambient Trance', 'Big Band Swing', 'Christian Rock', 'Christmas: Pop', 'Christmas: R&B', 'Country Gospel', 'Crossover Jazz', 'Detroit Techno', 'Drinking Songs', 'East Coast Rap', 'Easy Listening', 'Electric Blues', 'Foreign Cinema', 'General Celtic', 'General Reggae', 'General Spoken', 'General Techno', 'General Trance', 'Hardcore Metal', 'High Classical', 'Japanese Blues', 'Jewish/Israeli', 'Old School Rap', 'Original Score', 'Outlaw Country', 'Standup Comedy', 'Unclassifiable', 'West Coast Rap', 'Alternative Rap', 'Ambient New Age', 'Christian Metal', 'Christmas: Jazz', 'Christmas: Rock', 'Film Soundtrack', 'General Country', 'General Hip Hop', 'General Holiday', 'General New Age', 'Hardcore Techno', 'Japanese Fusion', 'Mainstream Jazz', 'Native American', 'Old School Punk', 'Renaissance Era', 'Southern Gospel', 'Traditional Pop', 'Underground Rap', 'Urban Crossover', 'West Coast Jazz', 'Alternative Folk', 'Alternative Punk', 'Alternative Rock', 'Avant-Garde Jazz', 'British Invasion', 'Children’s Music', 'Classical Guitar', 'Classical Indian', 'Contemporary R&B', 'Eastern European', 'Electronic Rock ', 'Industrial Dance', 'Japanese Karaoke', 'Meditation Music', 'New Orleans Jazz', 'Progressive Rock', 'Psychedelic Rock', 'Television Score', 'Traditional Folk', 'Western European', 'Adult Alternative', 'Alternative Metal', 'Baladas y Boleros', 'Christmas: Modern', 'Classic Christian', 'Contemporary Folk', 'Contemporary Jazz', 'Experimental Rock', 'General Christian', 'General Classical', 'General Religious', 'Instrumental Rock', 'Operating Systems', 'Progressive House', 'Progressive Metal', 'Regional Mexicano', 'Singer-Songwriter', 'Adult Contemporary', 'American Trad Rock', 'Christmas: Classic', 'Contemporary Blues', 'Contemporary Latin', 'General Electronic', 'General Industrial', 'General Soundtrack', 'Japanese Classical', 'Modern Composition', 'Old School Hip Hop', 'Straight Edge Punk', 'Traditional Celtic', 'Traditional Gospel', 'Alternative Country', 'Ambient Electronica', 'Classical Crossover', 'Contemporary Celtic', 'Contemporary Gospel', 'Environmental Music', 'General Alternative', 'Hardcore Industrial', 'Indian Subcontinent', 'Traditional Country', 'Christmas: Classical', 'Christmas: Religious', 'Conjunto Progressive', 'Contemporary Country', 'Aboriginal Australian', 'Christmas: Children’s', 'Old School Industrial', 'Television Soundtrack', 'Traditional Bluegrass', 'Contemporary Bluegrass', 'General Easy Listening', 'General Unclassifiable', 'South/Central American', 'Japanese Unclassifiable', 'Minimalist Experimental', 'General Children’s Music', 'Japanese General Soundtrack', 'Japanese Traditional (Minzoku)', 'Japanese Children’s Song (Doyo)'
        ];
	$("#invite-team-cont").on({
	    mouseenter: function (e) {
	    	$(this).find(".removeTeam").addClass("show");
	    },
	    mouseleave: function (e) {
	    	$(this).find(".removeTeam").removeClass("show");
	    }		
	}, ".teamEmail");

    $("#invite-team-cont").on("click", ".removeTeam", function(){
    	$this = $(this);
    	var type = $this.parent().hasClass("pending") ? 'pending' : 'accepted';
    	$.post(removeTeamURL, {email: $this.data("email"), type: type}, function(resp, textStatus, xhr) {
    		if(resp.msg=='ok'){
    			$this.parent().remove();
    		}
    	},'json');
    });
    var changedValues = [];
    setTimeout(function() {
        $("#projectForm input, #projectForm textarea").on("keydown change", function(e){
        	if($.inArray($(this).attr("name"), changedValues)==-1){
        		changedValues.push($(this).attr("name"));
        	}
        	if(changedValues.length>0)
        		$("#save-count").text(changedValues.length).addClass("show");
        });
    }, 1000);  

    if(userProjectRole == 'project owner'){
		var projectPicZone = new Dropzone("#userfile-dropzone", {
	        url: "uploadPic",
	        previewsContainer: '#project-upload-preview',
	        maxFilesize: 5, //MB
	        acceptedFiles: ".jpg,.jpeg,.png,.gif",
	        clickable: '.dz-clickable-pic',
	        init: function(){
	            this.on("error", function(file, message){ 
	                if( message === "You can't upload files of this type."){
	                    console.log(message);
	                }
	                else
	                    $('#userfile-errors').text(message);
	            });
	            this.on("success", function(file, data){
	                var response = $.parseJSON(data);
	                if(response.msg === "ok"){
	                	$('#project-img').attr('src', response.picture);
	                }
	                
	            });
	        }
	    });
    }

    $('#all-good-button').click(function(e){
    	$.post('acceptFiles').done(function(response){
    		if(response === "ok"){
    			$('#project-content').text("You've accepted and payed for the high quality files. If you haven't already, download them by clicking on the files below.");
    			$('#feedback-container').remove();
    			console.log("Files Accepted, creative payed");
    		}
    		else {
    			console.log('error: '+response);
    		}
    	});
    });

    $('#something-wrong-button').click(function(e){
    	if( $('#feedback-complaint').css('display') === 'none'){
	    	$('#feedback-complaint').css('display', 'inline-block');
	    	$.scrollTo('#feedback-complaint', 400, {'easing': 'easeInOutQuad'});
	    	$('#send-complaint-button').click(function(e){
	    		var val = $('#feedback-text').val();
	    		if(val != ""){
	    			$.post('sendComplaint', {'complaint': val}).done(function(data){
	    				console.log("complaint sent");
	    				$('#send-complaint-button').text('UPDATE COMPLAINT');
	    			});
	    		}
	    	});
	    }
    });

	$('#project-edit').on("click", function(e){
		$(".project-details").addClass("editing");
		$('.project-edit').addClass("show");
		$('.project-nonedit').hide();
		$('#userfile-dropzone').addClass();
		$("#project-submenu a[href='#details']").trigger("click");
		$("#project-submenu a:not(.active,.passive)").addClass("passive editState");
	});

	$("#project-save-cancel").on("click", function(e){
		$(".project-details").removeClass("editing");
		$('.project-edit').removeClass("show");
		$('.project-nonedit').show();
		$('#userfile-dropzone').removeClass("show");
		$(".editState").removeClass("passive");
	});

	$("#project-save").on("click", function(e){
		if(changedValues.length>0){
			$("#projectForm").ajaxSubmit({
                dataType: 'json',
				success: function(resp){
					if(resp.msg=="ok"){
						changedValues = [];
						$("#save-count").removeClass("show");
						$("#project-save-cancel").trigger("click");
                        $("#project-title").text(resp.title);
                        $("#project-des").text(resp.desc);
                        $("#details").html(resp.html);
					}
				}
			});
		}
		return false;
	});

/*
	$('.project-edit').blur(function(e){
		var before = $(e.target).prev();
		var val = $(e.target).val();

		if(val != before.text() ){
			if(val != "")
				before.text(val);
			else if(before.attr('id') === "project-title")
				before.text("Untitled Project");
			else if(before.attr('id') === "project-des")
				before.text("No description available...");

			$.post('saveField', {'field': before.attr('id'), 'val': before.text()});
		}
	});
*/
	$('#project-photo').hover(function(){
		if($(".project-details").hasClass("editing"))
			$('#userfile-dropzone').css('visibility', 'visible').addClass("show");
	}, function(){
		if($(".project-details").hasClass("editing"))
			$('#userfile-dropzone').css('visibility', 'hidden').removeClass("show");
	});

	uniqueCheckboxes("custom-checkbox_input");

	//Tab button states
	$(".project-sort-nav-menu").on("click", function(e){
		if($(this).hasClass("inactive")){
			//Make active and change to this category
			$('.project-sort-nav-menu').not(this).removeClass('active').addClass('inactive');
			$(this).removeClass('inactive').addClass('active');

			//Launch the appropriate ajax request for submissions
			var section = $(this).text().toLowerCase();

			var url = $.url();
			//console.log(url.attr('protocol')+'://'+url.attr('host')+url.attr('path')+'?tab='+section);
			window.history.replaceState({tab: section, sub: url.param('sub'), comment: url.param('comment'), user: url.param('user')}, "", '?tab='+section);
			//window.history.pushState(/*{tab: section, sub: url.param('sub'), user: url.param('user')}*/null, "", '?tab='+section);

			$("#invite-team-cont").css('display', 'none');

			if(section === "new" || section === "rated" || section === "not rated"){
				$('#players').fadeOut(400);
				getSubmissions(section);
			}
			else if(section == "exchange"){
				$("#project-submenu a[href='#exchange']").trigger("click");
			}
			else{
				$('#profiles').fadeOut(400);
				getPeople(section);
			}
		}
	});

	$("#project-submenu > a").on("click", function(e){
		if(!$(this).hasClass("passive") && !$(this).hasClass("active")){
			$(".tab-content.active").fadeOut(400);
			$("#project-submenu > a.active").removeClass('active');
			$(".tab-content.active").removeClass("active");

			var tab = $(this).attr("href");
			$(this).addClass("active");
			$(tab).addClass("active").fadeIn(200);

			var section = $(this).text().toLowerCase();
			var url = $.url();
			window.history.replaceState({tab: section, sub: url.param('sub'), comment: url.param('comment'), user: url.param('user')}, "", '?tab='+section);
			if(tab=='exchange')
				document.title = document.title.replace('Submissions','Exchange');
			$(tab).find(".project-sort-nav-menu:first").trigger("click");
		}
		return false;
	});

	var tab = $.url().param('tab');
	if(tab === "new" || tab === "rated" || tab === "not-rated"){
		$("#project-submenu a[href='#submissions']").trigger("click");
		$("#submissions .active").removeClass("active").addClass("inactive");
		$('#tab-'+tab).removeClass('inactive').addClass('active');
		var sub = $.url().param('sub');
		var comment = $.url().param('comment');
		var fun = false;
		if(sub){
			fun = function(){//Scroll To sub
				$.scrollTo('#s'+sub);
				if(comment){
					switch(comment.charAt(0))
					{
						case 'w':
							$("#"+comment).trigger("click");
						break;
						case 't':
						break;
					}
				}		
			};
		}
		getSubmissions(tab, fun);

	}
	else if(tab == "team" || tab == "creatives" || tab == "followers"){
		$("#project-submenu a[href='#people']").trigger("click");
		$("#people .active").removeClass("active").addClass("inactive");
		$('#tab-'+tab).removeClass('inactive').addClass('active');
		var user = $.url().param('user');
		var fun = false;
		if(user){
			fun = function(){//Scroll to user
				$.scrollTo('#u'+user);
			};
		}
		getPeople(tab, fun);
	}
	else if(tab == "exchange"){
		$("#project-submenu a[href='#exchange']").trigger("click");
	}
	else if(tab == "submissions"){
		$('#tab-new').removeClass('inactive').addClass('active');
		getSubmissions("new");
		$("#project-submenu a[href='#submissions']").trigger("click");
	}
	else{ //Default state
		getSubmissions("new");
	}

});
