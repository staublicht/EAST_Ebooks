/*jslint
		node : true, browser : true, devel : true, maxlen: 122, indent: 2, sloppy: true
*/
/*global
*/

//Definitions
var jQuery, $, Ractive, EbookServer, app_data, mainRactive, PageDataModels;
jQuery = $ = require('jquery');
window.jQuery = window.$ = jQuery;
window.Popper = require('popper.js'); //required by bootstrap tooltips, dropdowns
require('bootstrap');
Ractive = require('ractive');
Ractive.load = require('ractive-load');
EbookServer = require('./js/ebooks_server_api.js');
Ractive.adaptors.EbooksServerAdaptor = EbookServer.RactiveAdaptor;
app_data = require('./js/app_data.json');
PageDataModels = require('./js/page_data_models.js');
mainRactive = new Ractive();

"use strict";

//Database Event Hooks
PageDataModels.Ebooks.onAddEntryResult = function(success, return_data){
    console.log("Add Entry Success:", success, return_data);

    if (success) {
        showAlert("success", "Book added to the Database.",true );
        loadPage("edit_page",return_data["id"]);
    }else{
        showAlert("danger", "Adding a new Book to the Database failed!",true);
    }
};

PageDataModels.Ebooks.onDeleteEntryResult = function(success, return_data){
    console.log("Delete Entry Success:", success, return_data);
    if (success) {
        showAlert("success", "Book removed from the Database.",true );
        loadPage("list_page");
    }else{
        showAlert("danger", "Deleting a Book from the Database failed!",true);
    }
};

function loadPage(page_id, input_data, data_overrides) {

	//validate page id
    if (!(typeof page_id === 'number' && (page_id % 1) === 0)) {
        //not an integer
        if (app_data.hasOwnProperty(page_id)) {
            page_id = app_data[page_id];
        } else {
            console.log("page id not a number and not in list of pages:", page_id);
            return false;
        }
    }

	//check if logged in
	var page = app_data.login_state ? page_id : app_data.login_page;

    if(!app_data.login_state){
        console.log("Not logged in. returning to login page.");
	}

	var pageDataModel = PageDataModels.getModel(page_id, input_data); //get page data
	var data = app_data.login_state ? $.extend( app_data, pageDataModel, data_overrides) : {}; //full data or none if login

	Ractive.load(app_data.pages[page].template_file).then(function (RPage) {
		if (typeof mainRactive.teardown !== "undefined") {
			mainRactive.teardown();
		}

		mainRactive = new RPage({
			el: app_data.target_element,
			data: data,
			lazy: true,
            adapt: [ 'EbooksServerAdaptor' ]
            //modifyArrays : true,
		});

		app_data.current_page = page;

		history.pushState({"page" : page_id}, app_data.pages[page_id].title, "");

	}).catch(function(e){
	  console.log(e);
	});
}

function serverLogin(user, pw) {
	EbookServer.login(user, pw).then(function (return_data) {
		if (return_data.session) {
			console.log("login session state:", return_data.session);
			app_data.login_state = return_data.session;
			//app_data.session_key = return_data.session_key;
			loadPage(app_data.index_page);
		} else {
            console.log("Login failed, session state:", return_data.session);
            if (app_data.current_page !== app_data.login_page) {
                loadPage(app_data.login_page);
            } else {
                mainRactive.set('alerts', [{type: "warning", content: "Login Failed!", dismissible: false}]);
                //TODO: handle Server not reachable? Should we have some possibility to work offline in future?
            }
		}
	}).fail(function (e) {
        console.log("Login failed. Could not get data from server:", e);
        if (app_data.current_page !== app_data.login_page) {
            loadPage(app_data.login_page);
        }

        mainRactive.set('alerts', [{type: "warning", content: "Login failed. Could not get data from server.", dismissible: false}]);
	});
}

function serverLogout(){
    EbookServer.logout().then(function (return_data) {
        console.log("login session state:", return_data.session);
        loadPage(app_data.login_page);
    });
}

function showAlert(type, content, dismissible) {
	console.log("Creating Alert", type, content);
	//types: primary, secondary, success, danger, warning, info, light, dark
	mainRactive.push('alerts',{ type : type, content : content, dismissible : dismissible});
}

function init() {
	"use strict";

	var helpers;

	//global functions, used instead of events
	helpers = Ractive.defaults.data;
	Ractive.load.baseUrl = app_data.component_base_url;

	helpers.showAlert = showAlert;
	helpers.serverLogin = serverLogin;
	helpers.loadPage = loadPage;
	helpers.serverLogout = serverLogout;

    serverLogin();

	window.addEventListener('popstate', browserHistoryChange);
}

function browserHistoryChange(e) {
	if (e.state) {
		if (e.state.page) {
			loadPage(e.state.page);
		}
	}
}

/* Init */
init();

/*
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
*/
