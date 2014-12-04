var submissions = [];
var hqFiles = [];
var winningSubmission;
var sm;

function hqFile(opt){
	var status = opt.status ? opt.status : "pending";
	var cont = $('<div class="hqFile-cont"><div class="hqFile-status '+status+'">'+status+'</div><div class="hqFile-img"></div><div class="hqFile-name">'+(opt.name+'.'+opt.extension)+'</div></div>');
	opt.cont.append(cont);
}

function getUserProjectSubmissions(callback){
	$.post(/*'submit/*/'getSubmissions').done(function(data){
		var response = $.parseJSON(data);
		//console.log(response);
		for(var i=0; i<response.content.length; i++){
			submissions.push(new Submission({
				role: response.content[i].role,
				publicId: response.content[i].publicId,
				cont: $('#players'),
				sooundPlayer: sm,
				submitable: true,
				top: true,
				commentsOn: {
					waveform: true, //Only if this user is owner or submitter
					team: false  //Only if this user is a team member or owner
				},
				revisions: response.content[i].revisions
			}));
		}

		if(callback)
			callback();
	});
}

function getWinningSubmission(){
	$.post('winningSubmission').done(function(data){
		var response = $.parseJSON(data);

		winningSubmission = new Submission({
			role: "team",
			publicId: response.publicId,
			cont: $('#winning-submission'),
			sooundPlayer: sm,
			submitable: false,
			top: false,
			commentsOn: {
				waveform: true,
				team: false
			},
			revisions: response.revisions
		});
	});
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
$(function(){
	$('#project-submenu a').last().simpleTooltip({shift: 'sw',tip: 'Area for exchanging files upon winning the project'});
	sm = new SooundPlayer();

    $(".project-details .container:not('.references')").each(function(){
        if($(this).find(".detail-container").length == 0)
            $(this).remove();
    });

	Dropzone.options.userfile = {
		maxFilesize: 10, //MB
		acceptedFiles: ".wav,.m4a,.mp3,.aac",
		clickable: '.dz-clickable',
		init: function(){
			this.on("error", function(file, message){
				if( message === "You can't upload files of this type."){
					var uft = $('#userfile-accepted-types');
					//Turn file types text red and shake it
					uft.css('color', '#e74c3c')
						.animate({'padding-left': 200}, 200, 'easeInOutQuad', function(){
							uft.animate({'padding-left': 0, 'padding-right': 200}, 300, 'easeInOutQuad', function(){
								uft.animate({'padding-right': 0}, 150, 'easeOutQuad', function(){
									setTimeout(function(){
										uft.css('color', '#CCC');
									}, 1500);
								});
							});
						});
				}
				else
					$('#userfile-errors').text(message);
			});
			this.on("sending", function(file){
				//console.log("sending file");
				$('#upload-progress-cont').show(100);
				$('#upload-progress-title').text(file.name);
				$('#upload-progress-status').text("uploading");
				$('#upload-progress-action').css('visibility', 'visible');
				$('#upload-progress-current').css('opacity', 1);
				window.onbeforeunload = function(e){
				        return 'There are uploading files, are you sure you want to leave?';
				}

				//Now animate in the details editor
			});
			this.on("uploadprogress", function(file, progress){
				var upc = $('#upload-progress-current');
				progress = Math.floor(progress);
				upc.width(progress+"%");

				if(progress === 100)
					$('#upload-progress-status').text("processing");
			});
			this.on("success", function(file, data){
				window.onbeforeunload = null
				$('#upload-progress-status').text("completed");
				var response = $.parseJSON(data);
				submissions.push(new Submission({
					//id: response.content.submissionID,
					publicId: response.content.publicId,
					cont: $('#players'),
					sooundPlayer: sm,
					submitable: true,
					top: true,
					role: 'song owner',
					commentsOn: {
						waveform: true, //Only if this user is owner or submitter
						team: false  //Only if this user is a team member or owner
					},
					revisions: [response.content.revision]
				}));
			});
		}
	};

	Dropzone.options.hqUploader = {
		maxFilesize: 50, //MB
		acceptedFiles: ".wav,.m4a,.mp3,.aac,.flac",
		clickable: '.dz-clickable',
		init: function(){
			this.on("error", function(file, message){
				if( message === "You can't upload files of this type."){
					var uft = $('#userfile-accepted-types');
					//Turn file types text red and shake it
					uft.css('color', '#e74c3c')
						.animate({'padding-left': 200}, 200, 'easeInOutQuad', function(){
							uft.animate({'padding-left': 0, 'padding-right': 200}, 300, 'easeInOutQuad', function(){
								uft.animate({'padding-right': 0}, 150, 'easeOutQuad', function(){
									setTimeout(function(){
										uft.css('color', '#CCC');
									}, 1500);
								});
							});
						});
				}
				else
					$('#userfile-errors').text(message);
			});
			this.on("sending", function(file){
				//console.log("sending file");
				$('#upload-progress-cont').show(100);
				$('#upload-progress-title').text(file.name);
				$('#upload-progress-status').text("uploading");
				$('#upload-progress-action').css('visibility', 'visible');
				$('#upload-progress-current').css('opacity', 1);
				window.onbeforeunload = function(e){
				        return 'There are uploading files, are you sure you want to leave?';
				}

				//Now animate in the details editor
			});
			this.on("uploadprogress", function(file, progress){
				var upc = $('#upload-progress-current');
				progress = Math.floor(progress);
				upc.width(progress+"%");

				if(progress === 100)
					$('#upload-progress-status').text("processing");
			});
			this.on("success", function(file, data){
				window.onbeforeunload = null
				$('#upload-progress-status').text("completed");
				var response = $.parseJSON(data);
				console.log(response);

				hqFiles.push( new hqFile({
					"id": response.id,
					"extension": response.extension,
					"name": response.name,
					"cont": $('#uploaded-files')
				}) );
			});
		}
	};

	$('#complaint-fixed-button').click(function(e){
		$('#complaint-cont').hide(400);
		$.post('complaintFixed').done(function(data){
			console.log("complaintFixed");
		});
	});

	var sub = $.url().param('sub');
	var comment = $.url().param('comment');
	var fun = false;
	if(sub){
		fun = function() {
			$.scrollTo('#s'+sub);
			if(comment){
				$("#"+comment).trigger("click");
			}
		}
	}
	getUserProjectSubmissions(fun);

	$("#project-submenu > a").on("click", function(e){
		if(!$(this).hasClass("passive") && !$(this).hasClass("active")){
			$(".tab-content.active").fadeOut(400);
			$("#project-submenu > a.active").removeClass('active');
			$(".tab-content.active").removeClass("active");
			var tab = $(this).attr("href");
			$(this).addClass("active");
			$(tab).addClass("active").fadeIn(200);

			$(tab).find(".project-sort-nav-menu:first").trigger("click");

			var section = tab.substring(1);
			var url = $.url();
			window.history.replaceState({tab: section}, "", '?tab='+section);

			if(tab=='exchange')
				document.title = document.title.replace('Submissions','Exchange');
		}
		return false;
	});

	var tab = $.url().param('tab');
	if(tab === "submissions"){
		$("#project-submenu a[href='#submissions']").trigger("click");
	}
	else if(tab == "exchange"){
		$("#project-submenu a[href='#exchange']").trigger("click");
	}
	else if(tab == "details"){
		$("#project-submenu a[href='#details']").trigger("click");
	}
	else{ //Default state
		$("#project-submenu a[href='#details']").trigger("click");
	}

});