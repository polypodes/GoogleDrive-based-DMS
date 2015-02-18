var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');

var _filesType = [];
var req = null;

var FileByTypeStore = Reflux.createStore({
    init: function() {
        this.listenTo(FileActions.getFilesByType, this.getFilesByType);
    },
    getFilesByType: function(type) {
        var that = this;
        if(req != null) req.abort();

        var url = CONST.API_GET_FILES_BY_TYPE + '/' + type;

        req = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data) {
                _filesType = data;
                console.log(data.list);
                that.trigger(data.list);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    onGetFileTypes: function(keyword) {
        this.loadFiles(keyword);
    }
});

module.exports = FileByTypeStore;