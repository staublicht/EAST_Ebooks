/*global Ractive */

var ractive = new Ractive({
	el: '#ractive-container',
	template: "<h1>Test {{name}}</h1>",
	data: {name: "World"}
});
