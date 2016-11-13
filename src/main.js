/*jslint
		node : true, browser : true, devel : true, maxlen: 122, indent: 2, sloppy: true
*/
/*global
*/

//Definitions
var jQuery, $, Ractive, EbookServer, bootstrap, app_data, mainRactive;
window.jQuery = $ = require('jquery');
Ractive = require('ractive');
Ractive.load = require('ractive-load');
EbookServer = require('./js/ebooks_server_api.js');
app_data = require('./js/app_data.json');
mainRactive = new Ractive();

//Error handling
function handleError(e) {
	"use strict";
	console.log(e);
}

function loadPage(page_id, page_data) {

	if (typeof mainRactive.teardown !== "undefined") {
		mainRactive.teardown();
	}

	var data = $.extend({}, app_data, page_data);

	//console.log("Hello" , data);

	if (app_data.login_state && app_data.session_key) {
		Ractive.load(app_data.pages[page_id]).then(function (RPage) {
			mainRactive = new RPage({
				el: app_data.target_element,
				data: data
			});
			app_data.current_page = page_id;
		});
	} else {
		Ractive.load(app_data.pages[app_data.login_page]).then(function (RPage) {
			mainRactive = new RPage({
				el: app_data.target_element,
				data: data
			});
			app_data.current_page = app_data.login_page;
		});
	}

}

function serverLogin(user, pw) {
	EbookServer.login(user, pw).then(function (return_data) {
		console.log("API Login Request:", return_data);
		return_data = JSON.parse(return_data);
		if (return_data.login_state) {
			console.log("login state:", return_data.login_state);
			app_data.login_state = return_data.login_state;
			app_data.session_key = return_data.session_key;
			loadPage(app_data.index_page);
		} else {
			if (app_data.current_page === app_data.login_page) {
				mainRactive.set('alerts', [{ type : "warning", content : "Login Failed!", dismissible : false}]);
				//TODO: handle Server not reachable? Should we have some possibility to work offline in future?
			} else {
				loadPage(app_data.login_page);
			}
		}
	});
}

function init() {
	"use strict";

	var helpers;

	//global helper functions
	helpers = Ractive.defaults.data;
	Ractive.load.baseUrl = app_data.component_base_url;

	helpers.showAlert = function (type, content, dismissible) {
		return false;
	};

	helpers.loginFunction = serverLogin;
	helpers.loadPage = loadPage;

	loadPage(app_data.index_page);
}

/* Init */
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
			console.log("API LOAD Request:", return_data);
			test_booklist.push(return_data);
		});
	});

	ractiveTest.on('test.sendRequest', function (event) {
		console.log("loadRequest geklickt.");
		var send_data = {'type': 'ebook', 'id': '123'};
		EbookServer.save(send_data).then(function (return_data) {
			console.log("API SAVE Request:", return_data);
			test_booklist.push(return_data);
		});
	});

}
