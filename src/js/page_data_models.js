$ = require('jquery');
EbookServer = require('./ebooks_server_api.js');
app_data = require('./app_data.json');

var eBooks = new EbookServer.DataTableProvider(
    "ebooks", 120, 50, 1000*30
);

/*
eBooks.onAddEntry = function(success, return_data){
    if (success) {
        //report success: create message, positive, message strings where?
        //notify subscribers/trigger what happens on completion
    }else{
        //report failure:
    }
}

eBooks.onDeleteEntry = function(success, return_data){
    if (success) {
        //report success
    }else{
        //report failure
    }
}
*/

var lookupPageData = {
    //login page
    0 : function(){
    },
    //list page
    1 : function(){

        /*
        var transformJSONFunc = function(key, value){
            switch(key) {
                case "title_original":
                    this.original_title = value;
                    return;
                case "date_release":
                    this.release_date = value;
                    return;
                default:
                    return value;
            }
        };
        */

        return {
            "data_table" : eBooks
        };

        /*
        EbookServer.getList(
            'ebooks',
            100, //limit
            0, //offset
            [ 'id', 'author', 'title_original', 'language', 'status', 'date_release', 'downloads' ],
            transformJSONFunc
        ).then(function (return_data) {
            deferred.resolve({
                "login_state" : return_data['session'],
                'table_entries' : return_data['data']
            });

        }).fail(function (e) {
            console.log("Page data preparation failed.", e);
            deferred.reject("Book data could not be loaded!");
        });
        */
    },
    //edit page
    2 : function(book_id) {
        //input data is book id

        /*
        var transformJSONFunc = function(key, value){
            switch(key) {
                case "title_original":
                    this.original_title = value;
                    return;
                case "date_release":
                    this.release_date = value;
                    return;
                default:
                    return value;
            }
        };
        */

        return {
            "edit_book" : eBooks.getEntry(book_id)
        };


        /*
        EbookServer.getSingle(
            'ebooks',
            input_data,
            '*', //* for select all
            transformJSONFunc
        ).then(function (return_data) {
            console.log(return_data);
            deferred.resolve({
                "login_state" : return_data['session'],
                "edit_book" : return_data['data'][0] //get first entry
            });
        }).fail(function (e) {
            console.log("Page data preparation failed.", e);
            deferred.reject("Book data could not be loaded!");
        });
        */
    }
};

exports.getModel = function(page_id, input_data){
    console.log("getting page data for id:", page_id);
    if (!(typeof page_id === 'number' && (page_id % 1) === 0)) {
        //not an integer
        if (app_data.hasOwnProperty('page_id')) {
            page_id = app_data[page_id];
        } else {
            console.log("page id not a number and not in list:", page_id);
            return false;
        }
    }
    return lookupPageData[page_id](input_data);
};