/*global Ractive */

var ractive = new Ractive({
	el: '#ractive-container',
	template: "<h1>Test {{name}}</h1> <test msg={{testmsg}} />",
	data: {name: "World", testmsg: ">>Test in sub component<<"}
});


