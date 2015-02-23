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
                console.log(data);
                that.trigger(arbo, args);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    getFilesFromFolders: function(id, token) {
        console.log('getFilesFromFolders called');
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
                console.log(data);
                arbo.files = data;
                arbo.folders = data.folders;
                if(arbo.breadcrumb.indexOf(data.folder.file.title) === -1) {
                    arbo.breadcrumb.push(data.folder.file.title);
                }
                console.log('title', arbo);
                that.trigger(arbo, data.has_pagination, isFirstPage);
            },
            error: function(xhr, type) {
                console.log('Ajax error!');
            }
        });
    },
    getNext: function() {
        console.log('in next fn :', arbo.files.has_pagination);
        if(arbo.files.has_pagination) {
            NProgress.start();
            tokenHistory.push(arbo.files.nextPageToken);
            console.log(currentId, tokenHistory)
            this.getFilesFromFolders(currentId, arbo.files.nextPageToken);
        } else {
            console.log("no next");
        }
    },
    getPrev: function() {
        if(tokenHistory.length > 2) {
            NProgress.start();
            tokenHistory.pop();
            console.log(currentId, tokenHistory);
            this.getFilesFromFolders(currentId, tokenHistory[tokenHistory.length - 1]);
        } else if(tokenHistory.length === 2) {
            NProgress.start();
            tokenHistory.pop();
            console.log(currentId, tokenHistory);
            this.getFilesFromFolders(currentId);
        } else {
            console.log("no prev");
        }
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