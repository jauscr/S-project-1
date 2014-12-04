//Utility functions for site use

//Runs a function after checking if the user is logged in
function loggedIn(fun){
	$.post(loggedInURL).done(function(data){
		if(fun)
			fun(data == "true" ? true : false);
	});
};