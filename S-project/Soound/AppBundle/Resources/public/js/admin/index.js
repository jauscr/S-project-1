var APP = (function(){
	self = {};
	var fillView = function(html){ $('#documents').html(html); };
	var getDocuments = function(url, args, done){ 
		$.post(url, args).done(function(data){
			done( $.parseJSON(data) );
		});
	};
	var populate = function(type, data){
		var r = [];
		for(var i=0; i < data.length; i++){
			r.push( type( data[i] ) );
		}
		return r;
	};
	var viewList = function( docs ){
		$('#documents').hide(400).empty();
		for(var i=0; i<docs.length; i++){
			//console.log( docs[i].view() );
			$('#documents').append( docs[i].view() );
		}
		$('#documents').show(400);
	};

	var Document = function(){
		var doc = {};

		doc.propsHtml = function(props){
			var html = "";
			for(var prop in props){
				html += '<span class="property"><span class="property-name">'+prop+': </span><span class="property-value">'+props[prop]+'</span></span>';
			}
			return html;
		}

		return doc;
	}

	var Project = function(opt){
		var project = Document(); //Extends Document

		project.view = function(){
			var props = project.propsHtml(opt);
			//return props;
			var list = $('<div class="project"></div>');
			list.append(props);
			var details = $('<div class="project-details"></div>').hide();
			list.append(details);
			list.click(function(e){
				if(!details.is(':visible'))
					details.show(400);
				else
					details.hide(400);
			});
			return list;
		}
		
		return project;
	}

	var User = function(opt){
		var user = Document(); //Extends Document

		user.view = function(){
			var props = user.propsHtml(opt);
			//return props;
			var list = $('<div class="user"></div>');
			list.append(props);
			var details = $('<div class="user-details"></div>').hide();
			list.append(details);
			list.click(function(e){
				if(!details.is(':visible'))
					details.show(400);
				else
					details.hide(400);
			});
			return list;
		}

		return user;
	}

	self.getProjects = function(){
		getDocuments('admin/projects', {}, function(json){
			var projects = populate(Project, json);
			console.log(projects);
			viewList( projects );
		});
	}

	self.getUsers = function(){
		getDocuments('admin/users', {}, function(json){
			var users = populate(User, json);
			viewList( users );
		});
	}

	//Query for documents, and return only selected fields to a callback function
	//or if not defined, just log the response
	self.get = function(docType, queryParams, fields, callback){
		//queryParams: 
		// {
		//	 field: 'field',
		//	 comparison: 'gt',
		//	 val: 10 || {'start': 0, 'end': 10}
		// }
		console.log("Query", queryParams);
		if(docType){
			$.post(
				'admin/get',
				{
					'docType': docType,
					'query': queryParams || [],
					'fields': fields || []
				}
			);
			/*
			.done( function(data){
				var response = $.parseJSON(data);
				if(callback)
					callback(response);
				else
					console.log(response);
			} );
*/
		}
		else {
			console.log("You need to pass what kind of document you want.");
		}
	}

	return self;
}());

$(function(){

	$('#projects').click(function(e){
		APP.getProjects();
	});

	$('#users').click(function(e){
		APP.getUsers();
	});

	APP.get('User', [
		{
			field: 'fullname',
			comparison: 'equals',
			val: 'Beta User'	
		}
	]);

});