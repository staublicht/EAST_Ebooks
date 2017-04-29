$ = require('jquery');
EbookServer = require('./ebooks_server_api.js');
app_data = require('./app_data.json');

var lookupPageData = {
    //login page
    0 : function(){
        return {};
    },
    //list page
    1 : function(){
        var data = EbookServer.getTableData('ebooks', 20, 0);
        console.log(data);
        return data;
    },
    //edit page
    2 : function(){
        return {};
    }
};

exports.getPageData = function(page_id, data_overrides){
    return $.extend(app_data, lookupPageData[page_id], data_overrides);
};