var Navigation = function(config){
	this.container = config.container,
	this.children = $(this.container).children(),
	this.actionOne = config.actionOne,
	this.actionTwo = config.actionTwo || "empty",
	this.navLinks = [this.actionOne, this.actionTwo],
	this.navMenus = [this.actionOne + "-menu", this.actionTwo + "-menu"];
};
