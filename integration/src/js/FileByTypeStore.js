var Reflux = require('reflux');
var FileActions = require('./FileActions');
var CONST = require('./Constant');
var $ = require('zepto-browserify').$;
var NProgress = require('nprogress');

var _filesType = [];
var tokenHistory = [''];
var currentType = '';
var isFirstPage = true;
var req = null;

var FileByTypeStore = Reflux.createStore({
    init: function() {
        this.listenTo(FileActions.getFilesByType, this.getFilesByType);
        this.listenTo(FileActions.getPrev, this.getPrev);
        this.listenTo(FileActions.getNext, this.getNext);
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
                that.trigger(_filesType.list, _filesType.has_pagination, isFirstPage);
            }
        });
    },
    getNext: function() {
        if(_filesType.has_pagination) {
            NProgress.start();
            tokenHistory.push(_filesType.nextPageToken);
            this.getFilesByType(currentType, _filesType.nextPageToken);
        }
    },
    getPrev: function() {
        if(tokenHistory.length > 2) {
            NProgress.start();
            tokenHistory.pop();
            this.getFilesByType(currentType, tokenHistory[tokenHistory.length - 1]);
        }
    }
});

module.exports = FileByTypeStore;