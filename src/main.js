/*jslint
		node : true, browser : true, devel : true, maxlen: 122, indent: 2, sloppy: true
*/
/*global
*/

//Definitions
var jQuery, $, Ractive, EbookServer, bootstrap, init_data;
window.jQuery = $ = require('jquery');
Ractive = require('ractive');
Ractive.load = require('ractive-load');
EbookServer = require('./js/ebooks_server_api.js');
init_data = require('./js/init_data.json');

Ractive.load.baseUrl = 'component_templates/';

//Error handling
function handleError(e) {
	"use strict";
	console.log(e);
}

//Init
var RactivePage;

function init() {
	"use strict";

	var helpers;

	//global helper functions
	helpers = Ractive.defaults.data;
	/*
	helpers.return_status_type = function (key) {
		return this.data.status_types[key];
	};
	*/

	Ractive.load('page_list.html').then(function (RPage) {
		RactivePage = new RPage({
			el: '#ractive_page',
			data: init_data
		});

		window.RactivePage = RactivePage; //for debugging, TODO: remove or comment
	});//.catch(handleError);
}
init();



//TEST component

Ractive.load('test.html').then(function (Test_widget) {
	Ractive.components.test = Test_widget;
	initTest();
});//.catch(handleError);


var test_booklist = [];

function initTest() {
	var ractiveTest = new Ractive({
		el: '#ractive-container',
		template: "<h1>Test {{name}}</h1> <test msg={{testmsg}} booklist={{booklist}} />",
		data: {
			name: "World",
			testmsg: ">>Test in sub component<<",
			booklist: test_booklist
		}
	});

	ractiveTest.on('test.loadRequest', function (event) {
		console.log("loadRequest geklickt.");
		var send_data = {'type': 'ebook', 'id': '123'};
		EbookServer.load(send_data).then(function (return_data) {
			console.log("API LOAD Request successful:", return_data);
			test_booklist.push(return_data);
		});
	});

	ractiveTest.on('test.sendRequest', function (event) {
		console.log("loadRequest geklickt.");
		var send_data = {'type': 'ebook', 'id': '123'};
		EbookServer.save(send_data).then(function (return_data) {
			console.log("API LOAD Request successful:", return_data);
			test_booklist.push(return_data);
		});
	});

}
