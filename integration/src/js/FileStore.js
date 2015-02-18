var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');

var _files = [];
var req = null;

var FileStore = Reflux.createStore({
    init: function() {
        this.loadFiles();
        this.listenTo(FileActions.searchFile, this.onSearchFile);
    },
    loadFiles: function(keyword) {
        var terms = keyword ? ('/search/' + keyword) : '';
        var url = CONST.API_GET_FILES + terms;

        this.getResources(url, keyword);
    },
    getResources: function(url, args) {
        var that = this;
        if(req != null) req.abort();

        req = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data) {
                _files = data;
                that.trigger(_files.list, args);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    getFilesType: function() {
        var url = CONST.API_GET_FILES_TYPE;
        if(req != null) req.abort();
        var that = this;

        req = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                that.trigger(data);
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