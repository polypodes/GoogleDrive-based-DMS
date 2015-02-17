var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;

var _files = [];
var req = null;

var FileStore = Reflux.createStore({
    init: function() {
        this.loadFiles();
        this.listenTo(FileActions.searchFile, this.onSearchFile);
    },
    loadFiles: function(keyword) {
        var terms = keyword ? ('/search/' + keyword) : '';
        var that = this;

        if(req != null) req.abort();
        console.log('search : ' + keyword);

        req = $.ajax({
            type: 'GET',
            url: 'http://localhost/app_dev.php/api/files' + terms,
            dataType: 'json',
            success: function(data) {
                _files = data;
                that.trigger(_files.list, keyword);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    onSearchFile: function(keyword) {
        this.loadFiles(keyword);
    }
});

module.exports = FileStore;