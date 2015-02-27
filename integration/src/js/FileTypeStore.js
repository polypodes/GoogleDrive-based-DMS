var Reflux = require('reflux');

var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');
var NProgress = require('nprogress');

var _filesType = [];
var tokenHistory = [''];
var req = null;

var FileTypeStore = Reflux.createStore({
    init: function() {
        this.loadFiles();
        this.listenTo(FileActions.getFileTypes, this.onGetFileTypes);
    },
    loadFiles: function(keyword) {
        var url = CONST.API_GET_FILES_TYPE;
        this.getTypes(url, keyword);
    },
    getTypes: function(url, args) {
        var that = this;
        if(req != null) req.abort();

        req = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data) {
                _filesType = data;
                that.trigger(_filesType.grouped, args);
            }
        });
    },
    getNext: function() {
        NProgress.start();
        var url = CONST.API_GET_FILES + '/' + _filesType.nextPageToken;
        tokenHistory.push('/' + _filesType.nextPageToken);
        this.getResources(url);
    },
    getPrev: function() {
        if(tokenHistory.length > 1) {
            NProgress.start();
            tokenHistory.pop();
            var url = CONST.API_GET_FILES + tokenHistory[tokenHistory.length - 1];
            this.getResources(url);
        }
    },
    onGetFileTypes: function(keyword) {
        this.loadFiles(keyword);
    }
});

module.exports = FileTypeStore;