var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');
var NProgress = require('nprogress');

var _filesType = [];
var tokenHistory = [''];
var currentType = '';
var isFirstPage = true;
var req = null;

var FileByTypeStore = Reflux.createStore({
    init: function() {
        this.listenTo(FileActions.getFilesByType, this.getFilesByType);
    },
    getFilesByType: function(type, token) {
        var that = this;
        if(typeof token !== 'undefined') {
            var token = '/' + token;
            isFirstPage = false;
        } else {
            var token = '';
            isFirstPage = true;
        }
        if(req != null) req.abort();

        currentType = type;
        var url = CONST.API_GET_FILES_BY_TYPE + '/' + type + token;

        req = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data) {
                _filesType = data;
                console.log(data.list);
                that.trigger(data.list, _filesType.has_pagination, isFirstPage);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    getNext: function() {
        // @TODO check if no next token
        console.log(_filesType);
        if(_filesType.has_pagination) {
            NProgress.start();
            tokenHistory.push(_filesType.nextPageToken);
            console.log(tokenHistory);
            this.getFilesByType(currentType, _filesType.nextPageToken);
        } else {
            console.log('next max reached');
        }
    },
    getPrev: function() {
        if(tokenHistory.length > 2) {
            NProgress.start();
            tokenHistory.pop();
            console.log(tokenHistory);
            this.getFilesByType(currentType, tokenHistory[tokenHistory.length - 1]);
        } else {
            this.getFilesByType(currentType);
        }
    },
    onGetFileTypes: function(keyword) {
        this.loadFiles(currentType);
    }
});

module.exports = FileByTypeStore;