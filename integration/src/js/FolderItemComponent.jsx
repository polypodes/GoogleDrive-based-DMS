var React = require('react');
var FileActions = require('./FileActions');
var NProgress = require('nprogress');

var FolderItemComponent = React.createClass({
    handleClick: function() {
        NProgress.start();
        FileActions.getFilesFromFolder(this.props.folder.id);
    },
    render: function() {
        return (
            <div key={this.props.key} className="folder-item" onClick={this.handleClick}>{this.props.folder.title}</div>
            );
    }
});

module.exports = FolderItemComponent;