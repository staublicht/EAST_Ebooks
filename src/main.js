/*global $, JQuery, Ractive, window */

Ractive.load.baseUrl = 'component_templates/';

Ractive.load( 'test.html' ).then( function ( Test_widget ){
	Ractive.components.test = Test_widget;

	init();
}).catch( handleError );


var test_booklist = [];

function init(){
	var ractive = new Ractive({
		el: '#ractive-container',
		template: "<h1>Test {{name}}</h1> <test msg={{testmsg}} booklist={{booklist}} />",
		data: {
			name: "World",
			testmsg: ">>Test in sub component<<",
			booklist: test_booklist
		},
	});

	ractive.on( 'test.loadRequest' , function ( event ){
		console.log("loadRequest geklickt.");
		var send_data = {'action':'load', 'type':'ebook', 'id':'123'};
		$.post('api.php?v=' + Math.floor(Math.random() * 500), send_data ).then(function (return_data) {
			console.log("API LOAD Request successful:",return_data);
			test_booklist.push(return_data);
		});
	});

	ractive.on( 'test.sendRequest' , function ( event ){
		console.log("sendRequest geklickt.");
		var send_data = {'action':'save', 'type':'ebook', 'id':'123', 'save_name':'123', 'save_value':'123'};
		$.post('api.php?v=' + Math.floor(Math.random() * 500), send_data ).then(function (return_data) {
			console.log("API SEND Request successful:",return_data);
		});
	});

}


//Error handling
function handleError (e) {
	console.log(e);
}
