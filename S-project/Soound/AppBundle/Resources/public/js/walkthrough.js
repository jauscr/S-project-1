var Walkthrough = (function () {
	var walkthrough = this;
	var steps = [];
	var currentStep = 0;
	var walkthroughCont;

	function Step(opt) {
		var step = this;

		var cont, text, next, prev, exit, title, content;
		var offset;

		//Get the base offset
		if( opt.el )
			offset = opt.el.offset();
		else if( opt.left && opt.top )
			offset = { left: left, top: top };
		else
			offset = { top: $(window).height()/2 };

		if( opt.modal ) {
			cont = $('<div class="walkthrough-modal"></div>');
			prev = $('<div class="walkthrough-modal-prev"><</div>');
			next = $('<div class="walkthrough-modal-next">></div>');
			exit = $('<div class="walkthrough-modal-exit">X</div>');
			content = $('<div class="walkthrough-modal-content"></div>');
			if( opt.title ){
				title = $('<div class="walkthrough-modal-title">'+opt.title+'</div>');
				content.append(title);
			}
			if( opt.text ){
				text = $('<div class="walkthrough-modal-text">'+opt.text+'</div>');
				content.append(text);
			}

			cont.append(content);
			content.offset(offset);
			console.log(content.width());

			cont.click(function(e){
				walkthrough.stop();
			});
		}
		else {
			cont = $('<div class="walkthrough-bg');
			prev = $('<div class="walkthrough-prev"><</div>');
			next = $('<div class="walkthrough-next">></div>');
			exit = $('<div class="walkthrough-exit">X</div>');
			text = $('<div class="walkthrough-text">'+opt.text+'</div>');
		}

		cont.append()

		
		step.show = function() {
			walkthroughCont.append(cont);
			cont.css('display', 'block').delay(opt.delay || 0).animate({'opacity': 1}, opt.fadeIn || 300, function(){
				//if scrollTo, then do it on cont
			});
		};

		step.hide = function() {
			cont.animate({'opacity': 0}, opt.fadeOut || 300, function(){
				cont.css('display', 'none');
			});
		};

		return step;
	};

	walkthrough.start = function() {
		if(!walkthroughCont){
			walkthroughCont = $('<div class="walkthrough-cont"></div>');
			$('.overlay').after(walkthroughCont);
		}
		steps[currentStep].show();
		return walkthrough;
	};

	walkthrough.next = function(){
		walkthrough.stop();
		currentStep++;
		if(currentStep < steps.length-1)
			walkthrough.start();
	};

	walkthrough.prev = function(){
		walkthrough.stop();
		currentStep--;
		if(currentStep > -1)
			walkthrough.start();
	}

	walkthrough.stop = function() {
		steps[currentStep].hide();
		return walkthrough;
	};

	walkthrough.add = function(opt) {
		steps.push(new Step(opt));
		return walkthrough;
	};

	return walkthrough;
}());


/*

Walkthrough.add({
	modal: true,
	
}).start();

*/