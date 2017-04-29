/*jslint
		node : true, browser : true, devel : true, maxlen: 122, indent: 2, sloppy: true
*/

var $ = require('jquery');

var server_url, load, return_data;

server_url = "api/index.php";

function makeRequest(type, send_data) {
	var deferred, action_type_json, data;
	deferred = $.Deferred();


	switch (type) {
		case 'load':
			action_type_json = { type };
			break;
		case 'save':
			action_type_json = { type };
			break;
		case 'login':
			action_type_json = {
				'session' : {
					'login' : {
						'username' : '',
						'password' : ''
					}
				}
			};
			break;
		case 'logout':
            action_type_json = {
                'session' : {
                    'logout' : true
                }
			};
			break;
		default:
			console.log("Not a valid Ebooks Server Request", type);
			break;
	}

	data = $.extend( action_type_json, send_data);
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
		'session' : {
			'login' : {
				'username' : user,
				'password' : pw
			}
		}
	});
};

exports.logout = function () {
	return makeRequest('logout', {});
}
