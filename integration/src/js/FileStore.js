var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');
var NProgress = require('nprogress');

var _files = [];
var tokenHistory = [''];
var isFirstPage = true;
var req = null;

var FileStore = Reflux.createStore({
    init: function() {
        this.loadFiles();
        this.listenTo(FileActions.searchFile, this.loadFiles);
    },
    loadFiles: function(keyword) {
        var terms = keyword ? ('/search/' + keyword) : '';
        var url = CONST.API_GET_FILES + terms;

        this.getResources(url, keyword);
    },
    getResources: function(url, term, token) {
        var that = this;

        if(typeof token !== 'undefined') {
            var token = '/' + token;
            isFirstPage = false;
        } else {
            var token = '';
            isFirstPage = true;
        }

        if(req != null) req.abort();

        req = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data) {
                _files = data;
                that.trigger(_files.list, term, _files.has_pagination, isFirstPage);
                console.log(data);
                console.log(_files.has_pagination, isFirstPage);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    getNext: function() {
        // @TODO check if no next token
        if(_files.has_pagination) {
            NProgress.start();
            var url = CONST.API_GET_FILES + '/' + _files.nextPageToken;
            tokenHistory.push('/' + _files.nextPageToken);
            console.log(tokenHistory);
            this.getResources(url, null, true);
        } else {
            console.log('next max reached');
        }
    },
    getPrev: function() {
        if(tokenHistory.length > 2) {
            NProgress.start();
            tokenHistory.pop();
            console.log(tokenHistory);
            var url = CONST.API_GET_FILES + tokenHistory[tokenHistory.length - 1];
            this.getResources(url, null, true);
        } else {
            this.getResources(CONST.API_GET_FILES, null);
        }
    }
});

module.exports = FileStore;