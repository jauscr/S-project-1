// player.js object that can be called to create a new audio player
// Loaded after soundmanager2.js

function SooundPlayer(){
	var currentSong = null;
	var songs = [];
	soundManager.setup({
		url: '../bundles/sooundapp/js/soundmanager2/swf/', //Path to soundmanager2 swf files
		preferFlash: false,
		onready: function(){
			//console.log("SoundManager Ready");
		},
		ontimeout: function(){
			console.log("Error: SM2 did not start");
		}
	});

	this.addSong = function(opt, cont){
		songs[opt.id] = new Song( opt, cont);
	};
	this.removeSong = function( songId ){ 
		songs[songId].remove();
		soundManager.destroySound( songId ); 
		delete songs[songId];
	};
	this.changeContainer = function( songId, newCont ){
		songs[songId].changeContainer(newCont);
	};

	function sendRating(revisionId, rating, avgRatingCont, fun){
		$.post(ratingURL, {'revisionID': revisionId, 'rating': rating}).done(function(data){
			var response = $.parseJSON(data);
			avgRatingCont.text(response.avgRating+' Team rating');
			if(fun)
				fun(response.msg === "error");
		});
	}

	function sendComment(opt){
		$.post(commentURL, opt);
	}

	function Song(opt, cont){
		var self = this;
		var playing = false;
		var wasPlaying = false;
		var mySound = soundManager.createSound({
			id: opt.id, // 'sub' + opt.id
			url: opt.songURL,
			whileplaying: function(){
				setPlayPercent( this.position / opt.duration );
			},
			onfinish: function(){
				mySound.setPosition(0);
				setPlayPercent(0);
				togglePlay();
			}
		});

		this.pause = function(){
			wasPlaying = true;
			play.css('background-position', '0px 0px');
			mySound.pause();
			playing = false;
		};

		this.remove = function(){
			if(opt.commentsOn.waveform)
				sooundLive.removeChannel("comment/wave/"+opt.id);
			if(opt.commentsOn.team)
				sooundLive.removeChannel("comment/team/"+opt.id);
		};

		this.changeContainer = function(newCont){
			var elements = cont.children().detach();
			if(newCont){
				newCont.prepend(elements);
				cont = newCont;
			}
		}

		var togglePlay = function(){
			if(playing){
				play.css('background-position', '0px 0px');
				mySound.pause();
				playing = false;
			}
			else {
				if(currentSong != null){
					currentSong.pause();
				}
				currentSong = self;
				play.css('background-position', '0px -42px');
				mySound.play();
				playing = true;
				wasPlaying = false;
			}
		}

		var setScrubPos = function(left, ms){ //time in ms
			scrubBar.css('margin-left', left-scrubBar.width()/2);
			scrubTime.css('margin-left',  left-scrubTime.width()/2);
			scrubTime.html(toTime(ms));

			if(newThreadBtn){
				newThreadBtn.css('margin-left', left-11);
			}
			setSkipWidth(left);
		}

		var playWidth = 0;
		var skipWidth = 0;
		var totalWidth = ($(cont).width() ? $(cont).width() : 740);

		function setPlayPercent(percent){
			playWidth = percent*totalWidth;
			var totalTemp = playWidth - 1;
			if(playWidth < .1){
				playWidth = .1;
				totalTemp = playWidth;
			}
			
			played.css({
				'-webkit-clip-path': 'rectangle(0px, 0px, '+playWidth+'px, 100px)',
				'-moz-clip-path': 'rectangle(0px, 0px, '+playWidth+'px, 100px)',
				'-o-clip-path': 'rectangle(0px, 0px, '+playWidth+'px, 100px)',
				'clip-path': 'rectangle(0px, 0px, '+playWidth+'px, 100px)'
			});

			total.css({
				'-webkit-clip-path': 'rectangle('+totalTemp+'px, 0px,'+totalWidth+'px, 100px)',
				'-moz-clip-path': 'rectangle('+totalTemp+'px, 0px,'+totalWidth+'px, 100px)',
				'-o-clip-path': 'rectangle('+totalTemp+'px, 0px,'+totalWidth+'px, 100px)',
				'clip-path': 'rectangle('+totalTemp+'px, 0px,'+totalWidth+'px, 100px)'
			});

			var sw = skipWidth-playWidth;
			if(sw < 0)
				sw = .1;

			skipTo.css({
				'-webkit-clip-path': 'rectangle('+playWidth+'px, 0px, '+sw+'px, 100px)',
				'-moz-clip-path': 'rectangle('+playWidth+'px, 0px, '+sw+'px, 100px)',
				'-o-clip-path': 'rectangle('+playWidth+'px, 0px, '+sw+'px, 100px)',
				'clip-path': 'rectangle('+playWidth+'px, 0px, '+sw+'px, 100px)'
			});
			if(mySound)
				setCurrTime(percent*opt.duration);
				//setCurrTime(percent*mySound.durationEstimate);
/*
			if(playWidth > playTime.width()+20 ){
				playTime.finish();
				playTime.animate({'margin-left': playWidth - (playTime.width()-10)}, 100 );
			}
			else
				playTime.animate({'margin-left': playWidth-10}, 100);
*/				
			if(playWidth > playTime.width()+20)
				playTime.finish();

			if(playWidth < (playTime.width()+10))
				playTime.animate({'margin-left': playWidth}, 100 );
			else
				playTime.animate({'margin-left': playWidth-playTime.width()}, 100 );

			playedSVGOuter.css('width', playWidth);
		}

		function setSkipWidth(width){
			skipWidth = width;

			var sw = skipWidth-playWidth;
			if(sw < 0)
				sw = .1;

			skipTo.css({
				'-webkit-clip-path': 'rectangle('+playWidth+'px, 0px, '+sw+'px, 100px)',
				'-moz-clip-path': 'rectangle('+playWidth+'px, 0px, '+sw+'px, 100px)',
				'-o-clip-path': 'rectangle('+playWidth+'px, 0px, '+sw+'px, 100px)',
				'clip-path': 'rectangle('+playWidth+'px, 0px, '+sw+'px, 100px)'
			});
		}

		function setCurrTime(ms){ playTime.html( toTime(ms) ); }

		function toTime(ms){
			var min = Math.floor( ms / 60000 );
			var sec = Math.floor( (ms-(min*60000) ) / 1000 );
			if( min < 10)
				min = "0"+min;
			if( sec < 10)
				sec = "0"+sec;
			return min+":"+sec;
		}

		//Now for the actual construction of the player view

		//Top part of player, holds pause/play button and title/artist info
			var top = $('<div class="player-top"></div>');
			var play = $('<div class="player-top-button"></div>');
			if(opt.hasOwnProperty('artist') && opt.role !== "song owner"){
				var about = $('<div class="player-top-about"><div class="player-top-title">'+opt.title+'</div><div class="player-top-artist"><a href="'+opt.artistUrl+'">'+opt.artist+'</a></div></div>');
				var rating = $('<div class="player-top-rating"></div>');
				var ratings = rating.raty({
					path: '/bundles/sooundapp/css/images/',
					half: true,
					readOnly: opt.role === "song owner" ? false : false,
					score: opt.userRating > -1 ? opt.userRating : 0,
					iconRange: [
						{ range: 1, on: 'star-on.png', off: 'star-on.png' },
						{ range: 2, on: 'star-on.png', off: 'star-off.png' },
						{ range: 3, on: 'star-on.png', off: 'star-off.png' },
						{ range: 4, on: 'star-on.png', off: 'star-empty.png' },
						{ range: 5, on: 'star-on.png', off: 'star-empty.png' }
					],
				   	click:function(score,evt){
				   		sendRating(opt.id, score, teamRating, function(error){
				   			if(error){
				   				console.log("Error saving feedback, make sure you're logged in.");
				   			}
				   		});
					}
				});

				var teamRating = $('<div class="player-top-team-rating"></div>');
				teamRating.text(opt.avgRating+' Team rating');
				top.append(play, about, rating, teamRating);
			}
			else if(opt.role === "song owner"){
				var about = $('<div class="player-top-about"><div class="player-top-title">'+opt.title+'</div><div class="player-top-artist"><a href="'+opt.artistUrl+'">'+opt.artist+'</a></div></div>');

				var teamRating = $('<div class="player-top-team-rating-star"></div>');
				teamRating.html('<img class="player-star" src="/bundles/sooundapp/css/images/star-on.png" /><span class="player-avg-rating">'+opt.avgRating+'</span>');
				top.append(play, about, teamRating);
			}
			else{
				var about = $('<div class="player-top-credit-title">'+opt.title+'</div>');
				top.append(play, about);
			}

			play.click( function(e){
				e.preventDefault();
				togglePlay();
			});
			cont.append(top);

		//Middle part of player, holds song scrubberTime
			var middle = $('<div class="player-middle"></div>');
			var scrubTime = $('<div class="player-middle-scrub">00:00</div>');
			scrubTime.hide();

			middle.append(scrubTime);
			cont.append(middle);

		if(opt.commentsOn.waveform){//Waveform comments
			var threads = [];
			var threadTimes = [];

			function WaveCommentsView(){
				var self = this;
				var thread;
				var header = $('<div class="wave-comments-header"></div>');
					var arrow = $('<div class="wave-comments-arrow"></div>');
					var clock = $('<div class="wave-comments-clock"></div>');
					var time = $('<div class="wave-comments-time"></div>');
					var next = $('<div class="wave-comments-nav-next"><span class="wave-comments-nav-next-icon allowed"></span></div>');
					var prev = $('<div class="wave-comments-nav-back"><span class="wave-comments-nav-back-icon allowed"></span></div>');
				header.append(arrow, clock, time, next, prev);
				var commentsCont = $('<div class="wave-comments"></div>');
				var newCont = $('<div class="wave-comments-new"></div>');
					var newText = $('<textarea class="wave-comments-new-text" placeholder="What\'s on your mind?"></textarea>');
					var newSubmit = $('<div class="wave-comments-new-submit">SUBMIT</div>');
				newCont.append(newText, newSubmit);

				this.setThread = function(newThread){ 
					commentsCont.empty(); 
					waveComments.find('.wave-comments-bottom')
						.finish()
						.empty()
						.append(header, commentsCont, newCont)
						.css('display', 'block')
						.animate({'opacity': 1}, 200, function(e){
							$(document).bind('mouseup', commentsHideFun);
						});
					thread = newThread;
					if(thread.getPrevious())
						prev.find('.wave-comments-nav-back-icon').addClass("allowed");
					else
						prev.find('.wave-comments-nav-back-icon').removeClass("allowed");

					if(thread.getNext())
						next.find('.wave-comments-nav-next-icon').addClass("allowed");
					else
						next.find('.wave-comments-nav-next-icon').removeClass("allowed");

					var seconds = parseInt((thread.getTime()/1000)%60);
					var minutes = parseInt((thread.getTime()/(1000*60))%60);
					if(seconds === 0)
						seconds = "00";
					else if(seconds < 10)
						seconds = "0"+seconds;

					if(minutes === 0)
						minutes = "00";
					else if(minutes < 10)
						minutes = "0"+minutes;

					time.text(minutes+":"+seconds);

					var comments = thread.getComments().comments;
					var order = thread.getComments().order;

					for(var i=0; i<order.length; i++){
						comments[order[i]].draw( commentsCont );
					}

					prev.click(function(){
						if(thread.getPrevious()){
							self.setThread( threads[thread.getPrevious()] );
						}
					});
					next.click(function(){
						if(thread.getNext()){
							self.setThread( threads[thread.getNext()] );
						}
					});

					newSubmit.click(function(){
						var msg = newText.val();
						if(msg != ""){
							newText.val('');
							/*
							if(wasPlaying)
								togglePlay();*/
							sendComment({
								'to': opt.id,
								'type': 'wave',
								'thread': thread.getId(),
								'msg': msg,
								'parent': false
							});
							/*
							sooundLive.send('comment/wave/'+opt.id, {
								thread: thread.getId(), //The Id of the thread this comment is in reference to
								msg: msg,
								parent: false
							});
							*/
						}
					});
	
					positionCommentsBox( (thread.getTime()/opt.duration)*totalWidth);

					//remove slimscroll and styles
					waveComments.find('.slimScrollDiv').remove();
					commentsCont.attr("style","");

					if(commentsCont.outerHeight()>=parseInt(commentsCont.css("max-height"))){
			            commentsCont.slimScroll({
			                height: commentsCont.css("max-height"),
			                opacity: 0.2
						});
					}
				};
				this.getThread = function(){ return thread.getId(); };
				this.addComment = function(comment){
					comment.draw( commentsCont );
					if(commentsCont.outerHeight()>=parseInt(commentsCont.css("max-height"))){
			            commentsCont.slimScroll({
			                height: commentsCont.css("max-height"),
			                opacity: 0.2
						});
					}
				}
			}

			function Thread(config){
				var self = this;
				var prevThread = false;
				var nextThread = false;

				var comments = [];
				var commentsOrder = [];
				for(var i=0; i<config.comments.length; i++){
					commentsOrder.push(config.comments[i].id);
					comments[config.comments[i].id] = new Comment(config.comments[i]);
				}
				var dot = $('<div id="w'+config.id+'" class="wave-comment-dot '+(config.read ? '' : 'unread')+'"></div>');
				waveComments.find('.wave-comments-top').append(dot);
				dot.css('margin-left', (config.time/opt.duration)*totalWidth-3.5);
				//When a dot is clicked, show it's comments
				dot.click( function(e){	
					if(dot.hasClass("unread")){
						dot.removeClass("unread");
						$.post(readThreadURL, {thread: config.id}, function(data) {});
					}
					waveCommentsView.setThread(self);
				});
				
				this.getId = function(){ return config.id; };
				this.setPrevious = function(threadId){ prevThread = threadId; };
				this.getPrevious = function(){ return prevThread; };
				this.setNext = function(threadId){ nextThread = threadId; };
				this.getNext = function(){ return nextThread; };
				this.getTime = function(){ return config.time };
				this.getComments = function(){ return { comments: comments, order: commentsOrder}; };
				this.addComment = function(commentConfig){
					if(commentConfig.parent === false){
						commentsOrder.push( commentConfig.id );
						var comment = new Comment(commentConfig);
						comments[commentConfig.id] = comment;
						if(waveCommentsView.getThread() === config.id){
							waveCommentsView.addComment(comment);
						}
					} else {
						comments[commentConfig.parent].addReply(commentConfig);
					}
				}

				this.getDot = function(){ return dot[0]; };
			}

			function Comment(config){
				//var replies = [];

				var comment = $('<div class="wave-comment-cont"></div>');
					var content = $('<div class="wave-comment-content"><img class="wave-comment-picture" src="'+config.picture+'"/></div>');
						var right = $('<div class="wave-comment-right"></div>');
							var top = $('<div class="wave-comment-top"><div class="wave-comment-name"><a href="'+config.profileUrl+'">'+config.from+'</a></div></div>');
								//var replyBtn = $('<div class="comment-reply"><span class="comment-reply-icon"></span> Reply</div>');
							//top.append(replyBtn);
							var commentText = $('<div class="wave-comment-text">'+config.msg+'</div>');
						right.append(top, commentText);
					content.append(right);
				/*	
					var repliesCont = $('<div class="wave-comment-replies"></div>');
					var newReplyCont = $('<div class="wave-comment-new-reply"></div>');
						var newReplyText = $('<textarea class="wave-comment-new-reply-text" placeholder="What\'s on your mind?"></textarea>');
						var newReplySubmit = $('<div class="wave-comment-new-reply-submit">REPLY</div>');
					newReplyCont.append(newReplyText, newReplySubmit);
					*/
				comment.append(content);//, repliesCont, newReplyCont);
/*
				for(var i=0; i<config.replies.length; i++){
					var reply = new Reply(config.replies[i]);
					reply.draw(repliesCont)
					replies[config.replies.id] = reply;
				}
*/
				this.draw = function(cont){ 
					/*
					replyBtn.click( function(e){
						//Create a new comment with parent set to opt.id
						if(newReplyCont.css('display') === 'none'){
							newReplyCont.show(200);
						}
					});
					*/
					/*
					if(newReplyCont.css('display') != 'none'){
						newReplySubmit.click( function(){
							var msg = newReplyText.val();
							if(msg != ""){
								newReplyText.val('');
								newReplyCont.hide();
								if(wasPlaying)
									togglePlay();
								sendComment({
									'to': opt.id,
									'type': 'wave',
									'thread': waveCommentsView.getThread(),
									'msg': msg,
									'parent': config.id
								});
							}
						});	
					}
					*/
					cont.prepend(comment); 
				}
				this.addReply = function(replyConfig){
					var reply = new Reply(replyConfig);
					reply.draw(repliesCont)
					replies[replyConfig.id] = reply;
				}
			}
			
			function Reply(config){
				var reply = $('<div class="wave-comment-cont"><div class="wave-comment-content"><img class="wave-comment-picture" src="'+config.picture+'"/><div class="wave-comment-right"><div class="wave-comment-top"><div class="wave-comment-name"><a href="'+config.profileUrl+'">'+config.from+'</a></div></div><div class="wave-comment-text">'+config.msg+'</div></div></div></div>');
				
				this.draw = function(cont){
					cont.append(reply);
				}
			}
			var waveComments = $('<div class="wave-comments-cont"><div class="wave-comments-top"></div><div class="wave-comments-bottom" tabindex="1"></div></div>');
			var waveCommentsView = new WaveCommentsView();
			cont.append(waveComments);

			function positionCommentsBox(tl){
				var box = waveComments.find('.wave-comments-bottom');
				var arrow = box.find('.wave-comments-arrow');
				if( tl < box.width()/2 ){
					box.css('margin-left', 0);
					arrow.css('margin-left', tl - arrow.width()/2);
				}
				else if( tl > totalWidth - box.width()/2){
					box.css('margin-left', totalWidth-box.width());
					arrow.css('margin-left', tl-( totalWidth-box.width() )-arrow.width()/2 );
				}
				else{
					box.css('margin-left', tl - box.width()/2);
					arrow.css('margin-left', ( box.width() - arrow.width() )/2 );
				}
			};
			
			function hideCommentsBottom(){
				waveComments.find('.wave-comments-bottom').animate({'opacity': 0}, 200, function(){
					$(this).css('display', 'none').empty();
				});
			};

			var commentsHideFun = function(e){
				var cont = waveComments.find('.wave-comments-bottom');
				if(!cont.is(e.target) && cont.has(e.target).length === 0 ){
					$(this).unbind('mouseup', commentsHideFun);
					hideCommentsBottom();
				}
			};

			//Load existing threads onto the waveform and add appropriate listeners
			for(var i=0; i<opt.waveThreads.length; i++){
				var thread = new Thread(opt.waveThreads[i]);

				if(i>0)
					thread.setPrevious( opt.waveThreads[i-1].id );
				if(i<opt.waveThreads.length-1)
					thread.setNext( opt.waveThreads[i+1].id );
				threads[opt.waveThreads[i].id] = thread;
				threadTimes[parseInt((opt.waveThreads[i].time/(1000*60))%60)+'_'+parseInt((opt.waveThreads[i].time/1000)%60)]=opt.waveThreads[i].id;
			}
			/* 
				//FOR DEBUGGING ORDER OF THREADS
				for(val in threads){
					if(threads[val].getPrevious() === false){
						var temp = val;
						while(threads[temp].getNext() != false){
							console.log(threads[temp].getDot());
							temp = threads[temp].getNext();
						}
						console.log(threads[temp].getDot() );
					}
				}
			*/
			//When a new thread/comment is recieved, update waveform with payload
			sooundLive.addChannel("comment/wave/"+opt.id, function(uri, payload){
				//console.log("Wave: ", payload);
				if(payload.thread === "new"){
					var comments = [];

					comments.push({
						"id": payload.commentId,
						"msg": payload.msg,
						"from": payload.from,
						"picture": payload.picture,
						"parent": false,
						"replies": []
					});
					var thread = new Thread({
						"id": payload.id,
						"comments": comments,
						"time": payload.time
					});

					if(payload.fromId==userPublicId)
						$(thread.getDot()).removeClass("unread");

					if(payload.prevThread)
						threads[payload.prevThread].setNext(payload.id);

					if(payload.nextThread)
						threads[payload.nextThread].setPrevious(payload.id);

					thread.setPrevious(payload.prevThread);
					thread.setNext(payload.nextThread);
					threads[payload.id] = thread;

				} else {
					//add this comment to it's appropriate thread and parent
					threads[payload.thread].addComment({
						"id": payload.commentId,
						"msg": payload.msg,
						"from": payload.from,
						"picture": payload.picture,
						"parent": payload.parent,
						"replies": []
					});
				}
			});
		}

		//Bottom part of player, holds song waveform and scrubber controls
			var bottom = $('<div class="player-bottom"></div>');
			var scrubBar = $('<div class="player-scrubber-bar"></div>');
			//Add the bottom event listeners
			bottom.mouseover( function(e){ 
				if(mySound){
					scrubTime.show();
					scrubBar.css('visibility', 'visible');
					skipTo.css('visibility', 'visible');
					if(newThreadBtn)
						newThreadBtn.css('visibility', 'visible');
					setScrubPos(
						e.pageX - bottom.offset().left,
						Math.floor((e.pageX - bottom.offset().left)/totalWidth * opt.duration)
					);
				}
			});

			bottom.mousemove( function(e){
				if(mySound){
					setScrubPos(
						e.pageX - bottom.offset().left,
						Math.floor((e.pageX - bottom.offset().left)/totalWidth * opt.duration)
					);
				}
			});

			bottom.mouseout( function(e){
				scrubTime.hide();
				scrubBar.css('visibility', 'hidden');
				skipTo.css('visibility', 'hidden');
				if(newThreadBtn)
					newThreadBtn.css('visibility', 'hidden');
			});

			bottom.click( function(e){
				mySound.setPosition( (e.pageX - bottom.offset().left)/totalWidth * opt.duration );
							
				if(!playing){
					if(currentSong != null && currentSong != mySound){
						currentSong.pause();
					}
					currentSong = self;
					play.css('background-position', '0px -42px');
					mySound.play();
					playing = true;
					wasPlaying = false;
				}
			});

			//SVG waveforms and positon scrubber and playback time
			
			var total = $('<div class="player-waveform" style="width: '+totalWidth+'px; height: 100px;"></div>');
			bottom.append(total);
			total.css({'fill': '#3C3C3C'});

			var skipTo = $('<div class="player-waveform-skipTo" style="width: '+totalWidth+'px; height: 100px;"></div>');
			bottom.append(skipTo);
			skipTo.css({'fill': '#3C3C3C'});

			var played = $('<div class="player-waveform-played" style="width: '+totalWidth+'px; height: 100px;"></div>');
			bottom.append(played);
			played.css({'fill': '#3C3C3C'});

			var playedSVGOuter = $('<div class="outer-waveform-played-part"></div>')
			var playedSVG = $('<div class="waveform-played-part"></div>');
			playedSVGOuter.append(playedSVG);
			playedSVG.css({'fill': '#E74C3D'})

			var importedSVGRootElement = opt.waveURL;
			//Append the imported SVG root element to the appropriate HTML element
			total.append($(importedSVGRootElement).clone());
			skipTo.append($(importedSVGRootElement).clone());
			played.append(importedSVGRootElement);
			playedSVG.append(importedSVGRootElement);
/*
			$.get(opt.waveURL, function(svgDoc){
				//Import contents of the svg document into this document
				var importedSVGRootElement = document.importNode(svgDoc.documentElement, true);
				//Append the imported SVG root element to the appropriate HTML element
				total.append($(importedSVGRootElement).clone());
				skipTo.append($(importedSVGRootElement).clone());
				played.append(importedSVGRootElement);
				playedSVG.append(importedSVGRootElement);
			}, "xml");
*/
			var playTime = $('<div class="player-playback-time"></div>');
			bottom.append(playTime, playedSVGOuter, scrubBar);
			
			cont.append(bottom);

		if(opt.commentsOn.waveform){
			//New thread button
			var newThreadBtn = $('<div class="wave-new-thread-button"></div>');
			var newThreadBox = $('<div class="wave-comments-new"><div class="wave-comments-arrow"><div class="wave-temp-thread-dot"></div></div><textarea class="wave-comments-new-text" placeholder="What\'s on your mind?"></textarea><div class="wave-comments-new-submit">SUBMIT</div></div>');
			newThreadBox.css({'border-radius': 5, 'border-top': 'solid 1px #DDD'});
			newThreadBtn.click(function(e){
				e.stopPropagation();
				//self.pause();
				var scrubPos = parseInt( scrubBar.css('margin-left') )+scrubBar.width()/2;
				var time = Math.round((scrubPos/totalWidth)*opt.duration);
				var seconds = parseInt((time/1000)%60);
				var minutes = parseInt((time/(1000*60))%60);
				if(threadTimes[minutes+'_'+seconds]){
					waveCommentsView.setThread(threads[threadTimes[[minutes+'_'+seconds]]]);
				}
				else{
					waveComments.find('.wave-comments-bottom')
						.finish() //finish any animations running
						.empty()  //clear out any html previously appended
						.append(newThreadBox) //show the new thread input box by fading in
						.css('display', 'block')
						.animate({'opacity': 1}, 200, function(e){
							$(document).bind('mouseup', commentsHideFun);
							//Create a new thread with a new comment
							newThreadBox.find('.wave-comments-new-submit').click( function(e){
								var msg = newThreadBox.find('.wave-comments-new-text').val();
								newThreadBox.find('.wave-comments-new-text').val('');
								var time = Math.round((scrubPos/totalWidth)*opt.duration); //time in milliseconds
								
								$(document).unbind('mouseup', commentsHideFun);
								hideCommentsBottom();
								/*
								if(wasPlaying)
									togglePlay();
								*/
								//create a new thread at the time current time
								if(msg != ""){
									//Find the prevThread/nextThread of the new thread
									var nextThread = false;
									var prevThread = false;

									var tempTime = 0;
									for( val in threads ){
										if(threads[val].getPrevious() === false){ //If there's no previous thread
											tempTime = threads[val].getTime();
											if(tempTime > time){
												nextThread = val;
												break;
											}
										}
										
										if(threads[val].getNext() === false){ //If there's no next thread
											tempTime = threads[val].getTime();
											if(tempTime < time){
												prevThread = val;
												break;
											}
										}
										
										if(threads[val].getTime() < time && threads[ threads[val].getNext() ].getTime() > time){
											prevThread = val;
											nextThread = threads[val].getNext();
											break;
										}
									}

									sendComment({
										'to': opt.id,
										'type': 'wave',
										'thread': 'new',
										'nextThread': nextThread,
										'prevThread': prevThread,
										'msg': msg,
										'time': time,
										'parent': false
									});
									/*
									sooundLive.send('comment/wave/'+opt.id, {
										thread: "new", //The Id of the thread this comment is in reference to
										nextThread: nextThread,
										prevThread: prevThread,
										msg: msg,
										time: time,
										parent: false
									});
									*/
								}
							});
							$(this).find('.wave-comments-new-text').focus();
						});
					positionCommentsBox(scrubPos);
				}
			});
			newThreadBtn.mouseover( function(){ scrubBar.addClass('scrub-bar-circle'); });
			newThreadBtn.mouseout( function(){ scrubBar.removeClass('scrub-bar-circle'); });

			bottom.append(newThreadBtn);
		}

		if(opt.commentsOn.team){//Team Comments
			var numTeamComments = 0;
			var teamComments = [];

			function TeamCommentsView(){
				var commentsCont = $('<div class="team-comments-cont"></div>');
					var header = $('<div class="team-comments-header"></div>');
						var expander = $('<span class="team-comments-expander"><span class="team-comments-icon"></span></span>');
							var commentCount = $('<span class="team-comments-count">'+numTeamComments+' Team Comments</span>');
							var state = $('<span class="team-comments-state">Show</span>');
						expander.append(commentCount, state);
					header.append(expander);
					var content = $('<div class="team-comments-content"><div>');
						var comments = $('<div class="team-comments-old"></div>');
						var newCommentCont = $('<div class="team-comments-new"></div>');
							var newTop = $('<div class="team-comments-new-top"><img class="team-comments-user-pic" src="'+userPic+'"/></div>');
								var newText = $('<textarea class="team-comments-new-text" placeholder="What would you like to say? (@mm:ss if you want to link a certain moment in the song)"></textarea>');
							newTop.append(newText);
							var newBottom = $('<div class="team-comments-new-bottom"></div>');
								var newSubmit = $('<span class="team-comments-new-submit">ADD COMMENT</span>');
							newBottom.append(newSubmit);
						newCommentCont.append(newTop, newBottom);
					content.append(comments, newCommentCont);
				commentsCont.append(header, content);

				cont.append(commentsCont);

				for(var i=0; i<opt.teamComments.length; i++){
					teamComments[opt.teamComments[i].id] = new TeamComment( opt.teamComments[i], comments );
				}
				commentCount.text(numTeamComments+' Team Comments');

				expander.click(function(e){
					if(state.text() === "Show"){
						content.show(200, function(){
							$(this).css('display', 'inline-block');
						});
						state.text("Hide");
					} else {
						content.hide(200);
						state.text("Show");
					}
				});

				newSubmit.click(function(){
					var msg = newText.val();
					newText.val('');
					if(wasPlaying)
						togglePlay();

					if(msg != ""){
						sendComment({
							'to': opt.id,
							'type': 'team',
							'comment': 'new',
							'msg': msg
						});
						/*
						sooundLive.send('comment/team/'+opt.id, {
							comment: "new",
							msg: msg
						});
						*/
					}
				});

				this.addComment = function(config){
					commentCount.text( numTeamComments+' Team Comments' );
					teamComments[config.id] = new TeamComment( config, comments );
				}
			}

			function TeamComment(config, cont){
				numTeamComments++;
				var replies = [];

				var comment = $('<div class="team-comment-cont"></div>');
					var content = $('<div class="team-comment-content"><img class="team-comment-picture" src="'+config.picture+'"/></div>');
						var right = $('<div class="team-comment-right"><div class="team-comment-text">'+config.msg+'</div></div>');
							var top = $('<div class="team-comment-top"><div class="team-comment-name">'+config.from+'</div></div>');
								//var replyBtn = $('<div class="comment-reply"><span class="comment-reply-icon"></span> Reply</div>');
							//top.append(replyBtn);
						right.prepend(top);
					content.append(right);
					/*
					var repliesCont = $('<div class="team-comment-replies"></div>');
					var newReplyCont = $('<div class="team-comment-new-reply"><div>');
						var newReplyText = $('<textarea class="team-comments-new-text" placeholder="What would you like to say? (@mm:ss if you want to link a certain moment in the song)"></textarea>');
						var newReplyBottom = $('<div class="team-comments-new-bottom"></div>');
							var newReplySubmit = $('<span class="team-comments-new-submit">REPLY</span>');
						newReplyBottom.append(newReplySubmit);
					newReplyCont.append(newReplyText, newReplyBottom);
					*/
				comment.append(content);//, repliesCont, newReplyCont);
/*
				for(var i=0; i<config.replies.length; i++){
					var reply = new TeamReply(config.replies[i], repliesCont);
					replies[config.replies.id] = reply;
				}

				replyBtn.click( function(e){
					//Create a new comment with parent set to opt.id
					if(newReplyCont.css('display') === 'none'){
						newReplyCont.show(200);
					}
				});

				if(newReplyCont.css('display') != 'none'){
					newReplySubmit.click( function(){
						var msg = newReplyText.val();
						if(wasPlaying)
							togglePlay();

						if(msg != ""){
							newReplyText.val('');
							newReplyCont.hide();

							sendComment({
								'to': opt.id,
								'type': 'team',
								'msg': msg,
								'comment': config.id
							});
							/*
							sooundLive.send('comment/team/'+opt.id, {
								msg: msg,
								comment: config.id
							});
							*/
/*
						}
					});	
				}
*/
				cont.append(comment); 
				
				this.addReply = function(replyConfig){
					var reply = new TeamReply(replyConfig, repliesCont);
					replies[replyConfig.id] = reply;
				}
			}
			function TeamReply(config, cont){
				var reply = $('<div class="team-comment-cont"><div class="team-comment-content"><img class="team-comment-picture" src="'+config.picture+'"/><div class="team-comment-right"><div class="team-comment-top"><div class="team-comment-name">'+config.from+'</div></div><div class="team-comment-text">'+config.msg+'</div></div></div></div>');
				cont.append(reply);
			}

			var teamCommentsView = new TeamCommentsView();

			sooundLive.addChannel("comment/team/"+opt.id, function(uri, payload){

				if(payload.comment === "new"){
					numTeamComments++;
					teamCommentsView.addComment({
						"id": payload.id,
						"msg": payload.msg,
						"from": payload.from,
						"picture": payload.picture,
						"replies": []
					});
				}
				else {
					teamComments[payload.comment].addReply(payload);
				}		
			});
		}
	}
}