/*jslint
		node : true, browser : true, devel : true, maxlen: 122, indent: 2, sloppy: true
*/

var $ = require('jquery');
var server_url = "api/index.php";

function makeRequest(data) {
	var deferred = $.Deferred();

	//data doesn't need to be converted to JSOn, this os done in Jquery.post
    console.log("Server Request Data:", data);

	$.post(server_url + '?v=' + Math.floor(Math.random() * 500),
		data
		).done(function (return_data) {
		console.log("Server Request successful.", return_data);
        return_data = JSON.parse(return_data);
        return_data = $.isArray(return_data) ? return_data[0] : return_data; //hack needed in case data is wrapped in array
		deferred.resolve(return_data);
	}).fail(function (e) {
		console.log("Server Request failed.", e);
		deferred.reject(e);
	});

	return deferred.promise();
}

function load(data) {
    data = { "load" : data };
    return makeRequest(data);
}

function save(data){
    data = { "save" : data };
    return makeRequest(data);
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

exports.load = load;
exports.save = save;
exports.login = login;
exports.logout = logout;
exports.getSession = getSession;
