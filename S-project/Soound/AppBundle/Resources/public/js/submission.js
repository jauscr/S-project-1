function Submission(config){
	var revs = [];
	var revisions = config.revisions;
	var listeningForRevisions = false;
	var self = this;

	this.remove = function(){
		for(var i=0; i<revisions.length; i++){
			config.sooundPlayer.removeSong( revisions[i].id );
		}
		container.remove();
		if(listeningForRevisions)
			sooundLive.removeChannel("revision/"+config.publicId);
	}

	this.listenForRevisions = function(){
		sooundLive.addChannel("revision/"+config.publicId, function(uri, payload){
			console.log("REVISION", payload);
        	addRevision(payload);
        });
        listeningForRevisions = true;
	}

	function addRevision(rev){
		compressBtn = $('<span class="submission-revised compressed">Revised ('+(revisions.length)+') <i>+</i></span>');
		//Toggle the state of compression/expansion of this submission
		compressBtn.click(function(e){
			if(compressBtn.hasClass("compressed")){
				compressBtn.removeClass("compressed");
				compressBtn.addClass("expanded");
				//firstHistory.css('visibility', 'visible');
				//revs[0].date.css('visibility', 'visible');
				if(revisions.length > 1)
					olderRevisions.slideDown(300, function(){ $(this).css('display', 'inline-block');});
			}
			else{
				compressBtn.removeClass("expanded");
				compressBtn.addClass("compressed");
				//firstHistory.css('visibility', 'hidden');
				//revs[0].date.css('visibility', 'hidden');
				if(revisions.length > 1)
					olderRevisions.slideUp(300);
			}
		});

		if(!olderRevisions){ //The first rev will become the first olderRev
			olderRevisions = $('<div class="submission-older-revisions-cont"></div>');
			olderHistoryTitle=$("<h2 class='submission-history-title'>Past versions of this submission</h2>");
			olderHistoryClose=$("<span class='submission-history-close'></span>");
			olderHistoryTitle.append(olderHistoryClose);
			//olderHistory = $('<div class="submission-history"></div>');
			olderPlayers = $('<div class="submission-players"></div>');

			olderRevisions.append(olderHistoryTitle, olderPlayers);

			firstRevision.after(olderRevisions);

			olderHistoryClose.click(function(){
				compressBtn.trigger("click");
			});
		}
		config.sooundPlayer.changeContainer(revisions[0].id, false);
		createOlderRev(revisions[0], olderPlayers);

		if(compressBtn.hasClass("compressed"))
			olderRevisions.hide();

		/*
		var oldRev = revs[0];
		oldRev.date.detach();
		oldRev.date.css('visibility', 'visible');
		olderPlayers.prepend(oldRev.date);
		oldRev.dateDisplay = $('<div class="submission-history-display older">Submitted On: '+revisions[0].date+'</div>');
		oldRev.date.off('mouseover mouseout');
		oldRev.date.on('mouseover', function(e){ $(this).append( oldRev.dateDisplay ); });
		oldRev.date.on('mouseout', function(e){ oldRev.dateDisplay.detach(); });
		*/
		//olderHistory.height(olderRevisions.height()+20);	

		//Now add the new firstRev
		revisions.unshift(rev);
		var newRev = new Revision(rev, firstPlayer);
		/*
		newRev.date = $('<div class="submission-date"></div>');
		if(compressBtn.hasClass("compressed"))
			newRev.date.css('visibility', 'hidden');
		newRev.dateDisplay = $('<div class="submission-history-display">Submitted On: '+rev.date+'</div>');
		newRev.date.on('mouseover', function(e){ newRev.date.append(newRev.dateDisplay); });
		newRev.date.on('mouseout', function(e){ newRev.dateDisplay.detach(); });
		*/
		newRev.create();
		revs.unshift(newRev);

		top = firstPlayer.find(".player-top").first();
		top.append(compressBtn);
		playerActions();
	}

	function addSong(rev, cont){
		config.sooundPlayer.addSong({
			id: rev.id,
			duration: rev.duration,
			waveThreads: rev.waveThreads,
			teamComments: rev.teamComments,
			userRating: rev.userRating,
			avgRating: rev.avgRating,
			songURL: rev.songURL,
			waveURL: rev.waveURL,
			title: rev.title,
			artist: rev.artist,
			artistUrl : rev.artistUrl,
			commentsOn: config.commentsOn,
			role: config.role
		}, cont);
	}

	function Revision(opt, cont){
		var self = this;
		this.date;
		this.dateDisplay;
		this.create = function(){
			cont.prepend(self.date);
			addSong(opt, cont);
		}
	}

	var createOlderRev = function(opt, cont){
		var rev = new Revision(opt, cont);
		/*
		rev.date = $('<div class="submission-date"></div>');
		rev.dateDisplay = $('<div class="submission-history-display older">Submitted On: '+opt.date+'</div>');
		rev.date.mouseover(function(e){ $(this).append(rev.dateDisplay); });
		rev.date.mouseout(function(e){ rev.dateDisplay.detach(); });
		*/
		rev.create();
		revs.push(rev);
	}

	//Create the first song
	var container = $('<div class="submission-container"></div>');
	if(config.top)
		config.cont.prepend(container);
	else
		config.cont.append(container);

	//Create the middle of the player, where revisions are displayed
	var middle = $('<div class="submission-middle"></div>');
	container.append(middle);
		
	//Create the first revision view
	var firstRevision = $('<div class="submission-first-revision-cont"></div>');

	//var firstHistory = $('<div class="submission-first-revision-history"></div>');
	var firstPlayer = $('<div class="submission-players"></div>');
	var firstRev = new Revision(revisions[0], firstPlayer);
	/*
	firstRev.date = $('<div class="submission-date"></div>');
	firstRev.date.css('visibility', 'hidden');
	firstRev.dateDisplay = $('<div class="submission-history-display">Submitted On: '+revisions[0].date+'</div>');
	firstRev.date.on('mouseover', function(e){
		firstRev.date.append(firstRev.dateDisplay);
	});
	firstRev.date.on('mouseout', function(e){ firstRev.dateDisplay.detach(); });
	*/
	firstRev.create();
	revs.push(firstRev);

	firstRevision.append(firstPlayer);//firstHistory

	middle.append(firstRevision);

	//Create the top of the submission inside the player, with title and compressBtn

	var top = container.find(".player-top");
//Start Actions **********************************************

	//Actions are different depending on role
	//
	// song owner can only remove a song
	// project owner can reject a song and pick a song as the winner
	// project team can't take any actions
	function playerActions(){
		if(config.role === "song owner" || "project owner"){
			var actions = $('<div class="player-top-actions"><span class="actions-icon"></span>Actions<div class="actions-options"></div></div>');
			
			var hideActions = function(e){
				actions.removeClass('player-top-actions-active');
				actions.find('.actions-options').animate({'opacity': 0}, 200, function(){
					$(this).css('visibility', 'hidden');
				});
				$(document).unbind('click', hideActions);
			};

			actions.click(function(e){
				e.preventDefault();
				$(this).addClass('player-top-actions-active');
				$(this).find('.actions-options').css('visibility', 'visible').animate({'opacity': 1}, 200);
				
				$(document).bind('click', hideActions);
				return false;
			});
		}
		if(config.role === "song owner"){
			//(submitter) withdraw submission
			var remove = $('<div class="actions-remove"><span class="actions-remove-icon"></span>Remove Submission</div>');

			remove.click(function(e){
				hideActions();
				//console.log("remove submission");
				container.animate({'opacity': 0}, 1500);
				$.post(/*'submit/*/'remove', {'submission': config.publicId}).done(function(data){
					var response = $.parseJSON(data);
					self.remove();
					//console.log(response);
				});
				return false;
			});
			actions.find('.actions-options').append(remove);
			top.append(actions);
		} else if( config.role === "project owner"){
			//(project owner) Pick as winner, remove submission
			var winner = $('<div class="actions-winner"><span class="actions-winner-icon"></span>Pick as winner</div>');
			var reject = $('<div class="actions-reject"><span class="actions-reject-icon"></span>Reject Submission</div>');
			var teamRating = top.find(".player-top-team-rating");
			winner.click(function(e){
				//Select this song as the winner for the project and close the project
				hideActions();

				$(".winner-popup .artist").text(config.revisions[0].artist);
				$(".winner-popup .revision_title").text(config.revisions[0].title);
				winnerPublicId = config.publicId;
				$(".winner-overlay").fadeIn(400);
				return false;
			});

			$(".winner-popup-close").on("click", function(){
				$(".winner-overlay").fadeOut(200);
				return false;
			});

			$(".winner-popup .gray-button").on("click", function(){
				$(".winner-overlay").fadeOut(200);
				return false;
			});

			$('.winner-overlay').bind('click', function(e){
				if( e.target === this ){
					$(this).fadeOut(200);
				}
				return false;
			});

			$(".winner-accept").on("click", function(){
				top.append('<div class="submission-winner-icon"></div>');
				$.post(winnerURL, {'submission': winnerPublicId}).done(function(data){
					var response = $.parseJSON(data);
					if(response.msg=='ok')
						location.reload(true);
				});
				return false;
			})


			reject.click(function(e){
				//Remove this submission from the project
				hideActions();
				console.log("reject submission");
				container.animate({'opacity': 0}, 1500);
				$.post(/*'submissions/*/'reject', {'submission': config.publicId}).done(function(data){
					var response = $.parseJSON(data);
					self.remove();
					//console.log(response);
				});
				return false;
			});
			actions.find('.actions-options').append(winner, reject);
			teamRating.before(actions);
		}		
	}

	playerActions();

//End Actions *************************************************************

	//Create the middle revisions view
	var olderRevisions, olderPlayers;
	if(revisions.length > 1){

		olderRevisions = $('<div class="submission-older-revisions-cont"></div>');
		olderHistoryTitle=$("<h2 class='submission-history-title'>Past versions of this submission</h2>");
		olderHistoryClose=$("<span class='submission-history-close'></span>");
		olderHistoryTitle.append(olderHistoryClose);
		//olderHistory = $('<div class="submission-history"></div>');
		olderPlayers = $('<div class="submission-players"></div>');

		olderRevisions.append(olderHistoryTitle, olderPlayers);

		middle.append(olderRevisions);

		olderHistoryClose.click(function(){
			compressBtn.trigger("click");
		});

		for(var i=1; i<=revisions.length-1; i++){
			createOlderRev(revisions[i], olderPlayers);
		}
		//olderHistory.height(olderRevisions.height()+20);
		olderRevisions.hide();
	}

	var compressBtn;
	if(revisions.length > 1){
		compressBtn = $('<span class="submission-revised compressed">Revised ('+(revisions.length-1)+') <i>+</i></span>');

		//Toggle the state of compression/expansion of this submission
		compressBtn.click(function(e){
			if(compressBtn.hasClass("compressed")){
				compressBtn.removeClass("compressed").addClass("expanded").find("i").text("-");
				//firstHistory.css('visibility', 'visible');
				//revs[0].date.css('visibility', 'visible');
				if(revisions.length > 1)
					olderRevisions.slideDown(300, function(){ $(this).css('display', 'inline-block');});
			}
			else{
				compressBtn.removeClass("expanded").addClass("compressed").find("i").text("+");
				//firstHistory.css('visibility', 'hidden');
				//revs[0].date.css('visibility', 'hidden');
				if(revisions.length > 1)
					olderRevisions.slideUp(300);
			}
		});
		top.append(compressBtn);
	}

/*
	//Create the last revision view
	var lastRevision, lastHistory, lastPlayer, lastRev;
	if(revisions.length > 1){
		lastRevision = $('<div class="submission-last-revision-cont"></div>');
		lastHistory = $('<div class="submission-last-revision-history"></div>');
		lastPlayer = $('<div class="submission-players"></div>');

		lastRevision.append(lastHistory, lastPlayer);
		middle.append(lastRevision);

		var lastRev = new Revision(revisions[ revisions.length-1 ], lastPlayer);
		lastRev.date = $('<div class="submission-date"></div>');
		lastRev.dateDisplay = $('<div class="submission-history-display older">Submitted On: '+revisions[ revisions.length-1 ].date+'</div>');
		lastRev.date.mouseover(function(e){
			lastRev.date.append( lastRev.dateDisplay );
		});
		lastRev.date.mouseout(function(e){
			lastRev.dateDisplay.detach();
		});
		lastRev.create();
		revs.push(lastRev);
	}
*/
	//Create the bottom of the submission, which is the upload revision button
	var bottom = $('<div class="submission-bottom"></div>');
	container.append(bottom);
	container.attr("id", 's'+config.publicId);
	if(config.submitable){
		var uploadBtn = $('<div id="u'+config.publicId+'" class="submission-upload-button">UPLOAD REVISION</div>');
		bottom.append(uploadBtn);
		var dz = new Dropzone('div#s'+config.publicId, {
			url: /*"submit/*/"revision",
			headers: { "submission": config.publicId},
			maxFilesize: 10, //MB
			acceptedFiles: ".wav,.m4a,.mp3,.aac",
			clickable: '#u'+config.publicId,
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
					window.onbeforeunload = function(e){
					        return 'There are uploading files, are you sure you want to leave?';
					}
					//console.log("sending file");
					$('#upload-progress-cont').show(100);
					$('#upload-progress-title').text(file.name);
					$('#upload-progress-status').text("uploading");
					$('#upload-progress-action').css('visibility', 'visible');
					$('#upload-progress-current').css('opacity', 1);

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
					window.onbeforeunload = null;
					$('#upload-progress-status').text("completed");
					var response = $.parseJSON(data);
					//Set the newly uploaded revision as the lastRev...
					console.log(response.content)
					addRevision(response.content);
				});
			}
		});
	}

}