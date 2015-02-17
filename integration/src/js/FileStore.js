var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;

var _files = [];

var FileStore = Reflux.createStore({
    init: function() {
        this.loadFiles();
        this.listenTo(FileActions.searchFile, this.onSearchFile);
    },
    loadFiles: function() {
        var that = this;
        $.ajax({
            type: 'GET',
            url: 'http://localhost/app_dev.php/api/files',
            dataType: 'json',
            success: function(data) {
                _files = data;
                that.trigger(_files.list);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    onSearchFile: function(keyword) {
        console.log('search : ' + keyword);
        // process request for search
    }
});

module.exports = FileStore;