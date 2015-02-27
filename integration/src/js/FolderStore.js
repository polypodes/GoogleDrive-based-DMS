var Reflux = require('reflux');
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');
var NProgress = require('nprogress');

var arbo = {
    folders: [],
    files: [],
    breadcrumb: ['home']
};
var tokenHistory = [''];
var currentId = null;
var isFirstPage = true;
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
                that.trigger(arbo, args);
            }
        });
    },
    getFilesFromFolders: function(id, token) {
        var that = this;
        if(req != null) req.abort();

        if(typeof token !== 'undefined') {
            var token = '/' + token;
            isFirstPage = false;
        } else {
            var token = '';
            isFirstPage = true;
        }

        currentId = id;
        var url = CONST.API_GET_FOLDERS + '/' + id + token;

        req = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data) {
                arbo.files = data;
                arbo.folders = data.folders;
                if(arbo.breadcrumb.indexOf(data.folder.file.title) === -1) {
                    arbo.breadcrumb.push(data.folder.file.title);
                }
                that.trigger(arbo, data.has_pagination, isFirstPage);
            }
        });
    },
    getNext: function() {
        if(arbo.files.has_pagination) {
            NProgress.start();
            tokenHistory.push(arbo.files.nextPageToken);
            this.getFilesFromFolders(currentId, arbo.files.nextPageToken);
        }
    },
    getPrev: function() {
        if(tokenHistory.length > 2) {
            NProgress.start();
            tokenHistory.pop();
            this.getFilesFromFolders(currentId, tokenHistory[tokenHistory.length - 1]);
        } else if(tokenHistory.length === 2) {
            NProgress.start();
            tokenHistory.pop();
            this.getFilesFromFolders(currentId);
        }
    },
    getParentFolder: function() {
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