var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');

var arbo = {
    folders: [],
    files: [],
    breadcrumb: ['home']
};
var req = null;

var FolderStore = Reflux.createStore({
    init: function() {
        this.loadFolders();
        this.listenTo(FileActions.getFolders, this.loadFolders);
        this.listenTo(FileActions.getFilesFromFolder, this.getFilesFromFolders);
    },
    loadFolders: function() {
        var url = CONST.API_GET_FOLDERS;
        this.getFolders(url);
    },
    getFolders: function(url, args) {
        var that = this;
        if(req != null) req.abort();

        req = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data) {
                arbo.folders = data;
                console.log(data);
                that.trigger(arbo, args);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    getFilesFromFolders: function(id) {
        console.log('getFilesFromFolders called');
        var that = this;
        if(req != null) req.abort();

        var url = CONST.API_GET_FOLDERS + '/' + id;

        req = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                arbo.files = data;
                arbo.folders = data.folders;
                if(arbo.breadcrumb.indexOf(data.folder.file.title) === -1) {
                    arbo.breadcrumb.push(data.folder.file.title);
                }
                console.log('title', arbo);
                that.trigger(arbo);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    getParentFolder: function() {
        console.log(arbo.files.folder.modelData.parents[0].id);
        arbo.breadcrumb.pop();
        if(arbo.files.folder.modelData.parents[0].id === '0B3jjhr3nFZ1JajdyVDlDcHpTZkE') {
            this.loadFolders();
            arbo.files = [];
        } else {
            this.getFilesFromFolders(arbo.files.folder.modelData.parents[0].id);
        }

    }
});

module.exports = FolderStore;