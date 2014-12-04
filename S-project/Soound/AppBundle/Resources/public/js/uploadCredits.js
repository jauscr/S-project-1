var credits = [];
var currentCredit;
var addCreditCont;
var sm;

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
            	commentsOn: {
            		waveform: false,
            		team: false
            	}
			}, config.container);

			//Now add in the credit specific details

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

			//(credit owner) Edit credit details, remove credit
			var edit = $('<div class="credit-edit-button"><span class="credit-edit-icon"></span>Edit</div>');
			var remove = $('<div class="actions-remove"><span class="actions-remove-icon"></span>Remove</div>');
			
			edit.click(function(e){
				hideActions();
				//Allow user to edit credit details inline
				var creditEditor = $('<div class="upload-details-cont"><div class="upload-details-arrow"></div><div class="upload-details-left"><div class="upload-details-title">WHAT WORK DID YOU DO FOR THE PROJECT?</div><input type="text" class="upload-details-input upload-details-roles" value="'+config.roles+'" placeholder="Mixing, Production, Background Vocals" maxlength="80" /><div class="upload-details-submit">UPDATE CREDIT</div></div><div class="upload-details-right"><div><div class="upload-details-title">SHORT CREDIT DESCRIPTION</div><div class="upload-details-characters-used">'+config.description.length+' / 200 characters used</div></div><textarea class="upload-details-input upload-details-description" placeholder="Short project description" maxlength="200">'+config.description+'</textarea></div></div>');
				creditEditor.css('margin-top', 30);
				config.container.append(creditEditor);
				creditEditor.hide();

				creditEditor.find('.upload-details-description').bind('input propertychange', function(){
					var length = this.value.length;
					addCreditCont.find('.upload-details-characters-used').text( length +" / 200 characters used");
				});

				creditEditor.find('.upload-details-submit').click(function(){
					var description = creditEditor.find('.upload-details-description').val();
					var roles = creditEditor.find('.upload-details-roles').val();

					config.description = description;
					config.roles = roles;

					$.post('uploadCredits/update', {
						"creditID": config.id,
						"description": description, 
						"roles": roles
					});

					creditEditor.hide(400, function(){
						rolesCont.find('.credit-details-text').text(roles);
						descrCont.find('.credit-details-text').text(description);
						detailsCont.show(400);
						creditEditor.remove();
					});
				});

				detailsCont.hide(400, function(){
					creditEditor.show(400);
				});
				return false;
			});

			remove.click(function(e){
				hideActions();
				//Remove this credit
				if(self === currentCredit)
					currentCredit = null;

				$.post('uploadCredits/delete', {creditID: config.id});

				credits.splice( credits.indexOf(self), 1); //Remove this credit from array
				config.container.hide(400, function(){
					config.container.remove();
					self = null;
				});

				$('.page-title').text("Your Credits ("+credits.length+")");

				return false;
			});

			//(submitter) withdraw submission

			actions.find('.actions-options').css({'width': 130, 'margin-left': -43}).append(edit, remove);
			config.container.find('.player-top').append(actions);

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

function removeCredit(creditID){
	$.post('uploadCredits/delete', {creditID: creditID}).done( function(data){
			var response = $.parseJSON(data);
			if(response.msg){ //credit deleted
				$('#upload-progress-title').text("");
				$('#upload-progress-action').css('visibility', 'hidden');
				addCreditCont.hide(400);
			}
			else{ //credit not deleted
				console.log("error encountered deleting credit");
			}
		});
}

function getCredits(){
	$.post('uploadCredits/list').done( function(data){
		var response = $.parseJSON(data);
		for(var i=0; i<response.content.length; i++){
			var opt = response.content[i];

			var credCont = $('<div class="credit-container"></div>');
			$('#uploaded-credits').append( credCont );
			credCont.hide();
			var credit = new Credit(opt);
			credit.setOption("container", credCont);
			credits.push( credit );
		}
		for(var i=0; i<credits.length; i++)
			credits[i].draw();
	});
}

$(function(){
	addCreditCont = $('#upload-progress-cont').next();
	addCreditCont.hide();
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
				$('#upload-progress-title').text(file.name);
				$('#upload-progress-status').text("uploading");
				$('#upload-progress-action').css('visibility', 'visible');
				$('#upload-progress-current').css('opacity', 1);

				//Now animate in the details editor
				addCreditCont.show(400);
			});
			this.on("uploadprogress", function(file, progress){
				var upc = $('#upload-progress-current');
				progress = Math.floor(progress);
				upc.width(progress+"%");
				
				if(progress === 100)
					$('#upload-progress-status').text("processing");
			});
			this.on("success", function(file, data){
				$('#upload-progress-status').text("completed");
				var response = $.parseJSON(data);

				var credCont = $('<div class="credit-container"></div>');

				$('#uploaded-credits').prepend( credCont );
				credCont.hide(); //Hide now, show when submitted
				currentCredit = new Credit( response.content );
				currentCredit.setOption( "container", credCont );
				credits.unshift( currentCredit );
			});
		}
	};

	addCreditCont.find('.upload-details-description').bind('input propertychange', function(){
		var length = this.value.length;
		addCreditCont.find('.upload-details-characters-used').text( length +" / 200 characters used");
	});

	//Uploaded Audio/Waveforms can be deleted as soon as they're uploaded
	$('#upload-progress-action').click(function(){
		$('#upload-progress-status').text("deleting");

		$('#upload-progress-current').delay(100).animate({
			'width': 1
		}, 2000, function(){
			$('#upload-progress-status').text("");
		});

		$.post('uploadCredits/delete').done( function(data){
			var response = $.parseJSON(data);
			if(response.msg){ //credit deleted
				$('#upload-progress-title').text("");
				$('#upload-progress-action').css('visibility', 'hidden');
				addCreditCont.hide(400);
			}
			else{ //credit not deleted
				console.log("error encountered deleting credit");
			}
		});
	});

	//Submit credit details and generate a credit on screen
	addCreditCont.find('.upload-details-submit').click(function(){
		var description = addCreditCont.find('.upload-details-description').val();
		var roles = addCreditCont.find('.upload-details-roles').val();

		$('#upload-progress-current').delay(100).animate({
			'opacity': 0
		}, 1000, function(){
			$('#upload-progress-current').css({'opacity': 1, 'width': 1});
			$('#upload-progress-title').text("");
			$('#upload-progress-action').css('visibility', 'hidden');
			$('#upload-progress-status').text("");
		});
		addCreditCont.hide(400, function(){
			$('.page-title').text("Your Credits ("+credits.length+")");
			if(currentCredit){
				currentCredit.setOption( "description", description );
				currentCredit.setOption( "roles", roles );
				currentCredit.draw();
			}
		});

		$.post('uploadCredits/create', {
				"description": description, 
				"roles": roles
			});
	});

	sm = new SooundPlayer();

	getCredits();
})