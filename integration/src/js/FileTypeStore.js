var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');

var _filesType = [];
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
                console.log(data.grouped);
                that.trigger(_filesType.grouped, args);
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

module.exports = FileTypeStore;