/*jslint
		node : true, browser : true, devel : true, maxlen: 122, indent: 2, sloppy: true
*/

var $ = require('jquery');
var server_url = "api/index.php";
var msg_strings = {
    MSG_ADD_SUCCESS: "Succesfully added a new entry",
    MSG_ADD_FAIL: "Could not add the new entry, because the server request failed.",
    MSG_DELETE_SUCCESS: "Succesfully removed the entry",
    MSG_DELETE_FAIL: "Could not remove the entry, because the server request failed."
}

function makeRequest(data, json_transform){
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
            return_data = JSON.parse(return_data, json_transform);
            return_data = $.isArray(return_data) ? return_data[0] : return_data; //hack needed in case data is wrapped in array
            console.log("Server Reply JSON", return_data);
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

function ServerDataRequest(table, type, content, json_transform){
    this.table_name = table;
    this.request_type = type;
    this.request_content = content;
    this.json_transform = json_transform
}

ServerDataRequest.prototype.makeRequest = function(){
    var data = {};
    data[this.table_name] = {};
    data[this.table_name][this.request_type] = this.request_content;
    /*
    data structure is something like :
    { table_name : { request_type : { id: ... , fields: { ... } , .... } }    }
     */
    return makeRequest(data, this.json_transform);
};

function updateSingle(table, id, send_data) {
    var request = new ServerDataRequest(
        table,
        'put',
        {
            'id' : id,
            'data' : send_data
        }
    );

    return request.makeRequest();
}

function getList(table, limit, offset, return_fields, JSON_reviver_transform) {
    var request = new ServerDataRequest(
        table,
        'get',
        {
            'limit' : limit, //-1 = all
            'offset' : offset,
            'return_fields' : return_fields
        },
        JSON_reviver_transform
    );
    return request.makeRequest();
}

function getSingle(table, id, return_fields, JSON_reviver_transform) {
    id = parseInt(id, 10); //id has to be integer
    var request = new ServerDataRequest(
        table,
        'get',
        {
            'id' : id,
            'return_fields' : return_fields
        },
        JSON_reviver_transform
    );
    return request.makeRequest();
}

function addSingle(table, send_data) {
    var request = new ServerDataRequest(
        table,
        'post',
        {
            'data' : send_data
        }
    );
    return request.makeRequest();
}

function deleteSingle(table, id) {
    var request = new ServerDataRequest(
        table,
        'delete',
        {
            'id' : id
        }
    );
    return request.makeRequest();
}

function getSession(){
    return makeRequest({
        'session': {
            'login' : true
        }
    });
}

function login(user, pw){
    var data = {
        'session' : {
            'login' : {
                'username': user,
                'password': pw
            }
        }
    };
    return makeRequest(data);
}

function logout(){
    var data = {
        'session' : {
            'logout' : true
        }
    };
    return makeRequest(data);
}

function findFirstInArray(array,field_name,value){
    for (var i = 0; i < array.length; i++)
    {
        if ( array[i][field_name] === value ) {
            return array[i];
        }
    }
    return false;
}

function dropFromArraybyValue(array,field_name,value){
    var deleted = [];
    for (var i = 0; i < array.length; i++)
    {
        if ( array[i][field_name] === value ) {
            deleted.push(array[i]);
            array.splice(i, 1);
        }
    }
    return deleted;
}

//Data Table Provider holds the connection to one table and updates it regularly
//TODO: Data Model validation!

function DataTableProvider (table_name, offset, request_limit, updateInterval, json_transform_func) {
    var undef;
    this.timer = undef;
    this.updateInterval = updateInterval ? updateInterval : 0; //5sec
    this.autoUpdate = this.updateInterval > 0;
    this.table_name = table_name; //table name
    this.filters = {};
    this.sortBy = {};
    this.offset = offset ? offset : 0;
    this.data = [];
    this.dataObjects = [];

    if(json_transform_func){
        this.transformJSONFunc = json_transform_func;
    }

    this.request_limit = request_limit ? request_limit : 100;

    console.log("offset:",offset," request_limit:", request_limit);

    this.autoRetrieve();
}

//Data Table Provider holds the connection to one database table
DataTableProvider.prototype = {
    constructor: DataTableProvider,
    retrieveData: function(offset, limit) {
        console.log("offset:",offset,"limit:", limit);
        var _this = this;
        this.offset = offset ? offset : this.offset;
        this.request_limit = limit ? limit : this.request_limit;

        getList(this.table_name,this.request_limit,this.offset,"*",this.json_data_transform).then(
            function (return_data) {
                _this.data = return_data['data'];

                //prepare DataObjects
                var l = _this.data.length;
                var entry = new DataObject();
                var d = {};
                for (var i = 0; i < l; i++){
                    entry = _this.getEntry(_this.data[i]["id"]);
                    d = _this.data[i];
                    for(var k in d){
                        entry.data[k] = d[k];
                    }
                }

                _this.onUpdate();
            }
        ).fail(function (e) {
            console.log("Server Request 'get' failed.", e);
        });
    },
    getEntry: function(id){
        var entry = findFirstInArray(this.dataObjects,"id",id);
        if(!entry){ //problematic if we don't know table size
            entry = new DataObject(this, id);
            this.dataObjects.push(entry);
        }
        //entry.data = findFirstInArray(this.data,"id",id); //prefill with cached data
        //this.retrieveData(this.offset,this.limit); //request immediate update
        return entry;
    },
    updateEntry: function(id,data){
        //TODO validate data!
        _this = this;

        //push to server
        updateSingle(_this.table_name, id, data).then(function (return_data) {
            console.log("Server Request 'put' Return Data: ",JSON.stringify(return_data));

            //refresh table with updated data
            var entry = findFirstInArray(_this.data,"id",id);
            for(var x in data) entry[x] = data[x]; //update properties with new values

            //update associated DataObjects
            l = _this.dataObjects.length;
            var dp = new DataObject();
            for (var i = 0; i < l; i++)
            {
                dp = _this.dataObjects[i];
                if ( dp.id === id ) {
                    for(var x in dp.data) dp.data[x] = data[x]; //update properties with new values
                }
            }

            _this.onUpdate();
        }).fail(function (e) {
            console.log("Server Request 'put' failed.", e);
        });
    },
    addEntry: function(data){
        var _this = this;
        //TODO validate!
        addSingle(_this.table_name,data).then(
            function (return_data) {
                _this.retrieveData(); //request reload table with extra data object
                //example return_data : {data:199} //book id
                //message success
                var new_id = return_data.data;
                _this.onAddEntryResult(true,{"id" : new_id});
                return true;
            }
        ).fail(function (e) {
            console.log("Server Request 'post' failed.", e);
            _this.onAddEntryResult(false,{"error" : "Server Request 'post' failed."});
            return false;
            /*
            if(deferred) {
                deferred.reject("Server Request 'put' failed.");
            }
            */
        });
    },
    deleteEntry: function(id){
        var _this = this;
        var dp = _this.getEntry(id);
        var d = dp.data;
        d["id"] = id;
        deleteSingle(this.table_name,id).then(function (return_data) {
            console.log("Server Request 'delete' succcess. Return Data: ",return_data);
            console.log("removing entry from table, cleaning up references.");
            dropFromArraybyValue(_this.data,"id",id); //drop data entry from table data
            var dps = dropFromArraybyValue(_this.dataObjects,"id",id); //remove associated DataObjects
            for(var i in dps){  //cleanup
                dps[i].dispose();
            }
            _this.onDeleteEntryResult(true, d);
            return true;
        }).fail(function (e) {
            console.log("Server Request 'delete' failed.", e);
            d["error"] = "Server Request 'delete' failed.";
            _this.onDeleteEntryResult(false, d);
            return false;
        });
    },
    autoRetrieve : function(){
        if(this.autoUpdate){
            this.timer = setTimeout(
                this.autoRetrieve.bind(this),
                this.updateInterval
            )
        }

        this.retrieveData();
    },
    startInterval: function(time){
        this.stopInterval();
        this.updateInterval = time;
        this.autoUpdate = true;
        this.autoRetrieve();
    },
    stopInterval: function(){
        clearTimeout(this.timer);
        this.autoUpdate = false;
    },
    onUpdate: function(){
        //empty hook function for updating linked data objects
        console.log( "Update received, but no update function implemented:", this.table_name);
    },
    onAddEntryResult : function(success,return_data) {
        //empty hook function for result feedback
    },
    onDeleteEntryResult : function(success,return_data) {
        //empty hook function for result feedback
    },
    onUpdateEntryResult : function(success,return_data) {
        //empty hook function for result feedback
    },
    onActionFeedback : function(){

    },
    getData: function(){
        return this.data;
    },
    toString: function(){
        return "DataProvider: "+JSON.stringify(this.data);
    },
    transformJSONFunc: function(key, value){
        return value;
    },
    dispose: function(){
        //cleanup
        this.stopInterval();
        this.onUpdate = function(){};
        this.data = undefined;

        //dispose all Data Objects
    }
};

//Data Object is a single table entry object

function DataObject (dataProvider, id) {
    this.dataProvider = dataProvider;
    this.id = id;
    this.data = {};
}

DataObject.prototype = {
    constructor: DataObject,
    onUpdate: function(){
        console.log( "Update received:", this.dpTable.table_name, this.id);
    },
    update: function(input_data){
        console.log("data object update");
        var change = false;
        for(var v in input_data){
            var d = this.data[v];
            if (typeof d != 'undefined' && d !== v){
                this.data[v] = input_data[v];
                change = true;
                console.log("data change detected:", v, input_data[v]);
            }else{
                console.log("Tried to set unknown property: ", v, "value: ", input_data[v]);
            }
        }

        if(change)
            this.dataProvider.updateEntry(this.id,this.data);
    },
    dispose: function(){
        this.onUpdate = function(){};
        this.data = undefined;
    },
    delete: function(){
        this.dataProvider.deleteEntry(this.id);
    }
};

const RactiveAdaptor = {
    filter: function (object) {
        // return `true` if a particular object is of the type we want to adapt.
        //console.log("Adaptor filter called", object, "is Dataobject:", object instanceof DataObject, "isDataTable:", object instanceof DataTableProvider);

        return object instanceof DataObject || object instanceof DataTableProvider;
    },
    wrap: function (ractive, dataproviderobj, keypath, prefix) {
        if (dataproviderobj instanceof DataObject) {
            return new DataObjectWrapper(ractive, dataproviderobj, keypath, prefix);
        }

        return new DataTableWrapper(ractive, dataproviderobj, keypath, prefix);
    }
};


function DataObjectWrapper ( ractive, dataproviderobj, keypath, prefixer ) {
    this.object = dataproviderobj;

    dataproviderobj.updateLock = false;

    dataproviderobj.onUpdate = function(){
        _this = this;
        _this.updateLock = true;
        ractive.set(keypath,prefixer(_this.data)).then(function(){
            _this.updateLock = false;
        }).catch(function(e){
            console.log("Ractive.set on Data Object failed. Object locked.", e);
        });
    };
}

DataObjectWrapper.prototype = {
    teardown: function(){
        // Code executed on teardown.
        this.object.onUpdate = function(){}
    },
    get: function(){
        // Returns POJO version of your data backend.
        return this.object.data;
    },
    set: function(property, value){
        // Data setter for POJO property keypaths.
        if( ! this.object.updateLock && property.indexOf(".") === -1 ) { //has tobe unlocked, cannot set nested properties
            console.log("set property on data object:", property, value, this.object);
            var o = new Object();
            o[property] = value;
            this.object.update(o);
        }
    },
    reset: function(values){
        // don't allow setting itself with a new object of its own type, or not an object
        if(values instanceof DataObject || !(values instanceof Object)){
            return false;
        }

        console.log("reset data object:", values, this.object);

        if(this.object instanceof DataObject){
            this.object.update(values);
        }
    }
};

function DataTableWrapper ( ractive, dataproviderobj, keypath, prefixer ) {
    this.object = dataproviderobj;

    dataproviderobj.updateLock = false;

    dataproviderobj.onUpdate = function(){
        _this = this;
        _this.updateLock = true;
        console.log("Table onUpdate", _this.dataObjects);
        ractive.set(keypath,_this).then(function(){
            _this.updateLock = false;
        }).catch(function(e){
            console.log("Ractive.set on Data Table failed", e);
        });
    };

    console.log("DataTable Adaptor created: ",dataproviderobj.table_name);
}

DataTableWrapper.prototype = {
    teardown: function(){
        this.object.onUpdate = function(){};
    },
    get: function(){
        return this.object.dataObjects;
    }
    //no set, set only directly on the entry objects, to be implemented later if/when INSERT of several entries is supported by server
    /*
    reset: function(values){
        // don't allow setting itself with a new object of its own type, or not a collection of objects
        if(values instanceof DataTableProvider || Object.prototype.toString.call( models ) !== '[object Array]'){
            return false;
        }
        console.log("reset data table.", values, _this);

        _this.update(values);
    }
    */
};

exports.login = login;
exports.logout = logout;
exports.getSession = getSession;
exports.DataTableProvider = DataTableProvider;
exports.DataObject = DataObject;
exports.RactiveAdaptor = RactiveAdaptor;
