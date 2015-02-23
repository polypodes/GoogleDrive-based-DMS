var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');
var NProgress = require('nprogress');

var _files = [];
var req = null;

var LastModifiedStore = Reflux.createStore({
    init: function() {
        this.getFiles();
        this.listenTo(FileActions.lastModifiedFile, this.getFiles);
    },
    getFiles: function() {
        var that = this;
        if(req != null) req.abort();

        req = $.ajax({
            type: 'GET',
            url: CONST.API_GET_LAST_FILES,
            dataType: 'json',
            success: function(data) {
                _files = data;
                that.trigger(_files.list);
                console.log(data);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    }
});

module.exports = LastModifiedStore;