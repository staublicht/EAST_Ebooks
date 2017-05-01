/*jslint
		node : true, browser : true, devel : true, maxlen: 122, indent: 2, sloppy: true
*/

var $ = require('jquery');
var server_url = "api/index.php";

function makeRequest(data, JSON_reviver_transform) {
	var deferred = $.Deferred();

    data = JSON.stringify(data);
    data = {
        "request" : data
    };
    console.log("Server Request Data:", data);

	$.post(server_url + '?v=' + Math.floor(Math.random() * 500),
		data
		).done(function (return_data) {
		console.log("Server Reply received.");

        try {
            return_data = JSON.parse(return_data, JSON_reviver_transform);
            return_data = $.isArray(return_data) ? return_data[0] : return_data; //hack needed in case data is wrapped in array
            deferred.resolve(return_data);
        } catch(e) {
            console.log("Couldn't read JSON data.", e);
            deferred.reject(e);
        }

	}).fail(function (e) {
		console.log("Server Request failed.", e);
		deferred.reject(e);
	});

	return deferred.promise();
}

function getList(table, limit, offset, return_fields, JSON_reviver_transform) {
    var data = {};
    data[table] = {
        'get' : {
            'limit' : limit, //-1 = all
            'offset' : offset,
            'return_fields' : return_fields
        }
    };

    console.log(data);
    return makeRequest(data, JSON_reviver_transform);
}

function getSingle(table, id, return_fields, JSON_reviver_transform) {
    id = parseInt(id, 10); //id has to be integer
    var data = {};
    data[table] = {
        'get' : {
            'id' : id,
            'return_fields' : return_fields
        }
    };

    console.log("Request Data:", data);
    return makeRequest(data, JSON_reviver_transform);
}

function getSession(){
    return makeRequest({
        'session': {
            'login' : true
        }
    });
}

function login(user, pw){
    return makeRequest({
        'session': {
            'login': {
                'username': user,
                'password': pw
            }
        }
    });
}

function logout(){
    return makeRequest({
        'session' : {
            'logout' : 'true'
        }
    });
}

exports.getList = getList;
exports.getSingle = getSingle;
exports.getSession = getSession;
exports.login = login;
exports.logout = logout;
