/*jslint
		node : true, browser : true, devel : true, maxlen: 122, indent: 2, sloppy: true
*/

var $ = require('jquery');

var server_url, load, return_data;

server_url = "api/index.php";

function makeRequest(type, send_data) {
	var deferred, data;
	deferred = $.Deferred();


	switch (type) {
		case 'load':
			data = { type : send_data };
			break;
		case 'save':
			data = { type : send_data };
			break;
		case 'login':
			data = {
				'session' : {
					'login' : {
						'username' : send_data.user,
						'password' : send_data.pw
					}
				}
			};
			break;
		case 'logout':
            data = {
                'session' : {
                    'logout' : 'true'
                }
			};
			break;
		default:
			console.log("Not a valid Ebooks Server Request", type);
			break;
	}

	//data = $.extend( action_type_json, send_data);
	//data = JSON.stringify(data);

	$.post(server_url + '?v=' + Math.floor(Math.random() * 500),
		data
		).done(function (return_data) {
		console.log("Server Request successful.", return_data);
		deferred.resolve(return_data);
	}).fail(function (e) {
		console.log("Server Request failed.", e);
		deferred.reject(e);
	});

	return deferred.promise();
}

exports.load = function (send_data) {
	return makeRequest('load', send_data);
};

exports.save = function (send_data) {
	return makeRequest('save', send_data);
};

exports.login = function (user, pw) {
	return makeRequest('login', {
			'user' : user,
			'pw' : pw
		}
	);
};

exports.logout = function () {
	return makeRequest('logout', {});
}
