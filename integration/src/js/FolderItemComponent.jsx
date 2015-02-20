var React = require('react');
var FileActions = require('./FileActions');

var FolderItemComponent = React.createClass({
    handleClick: function() {
        console.log('clicked ', this.props.folder.id);
        FileActions.getFilesFromFolder(this.props.folder.id);
    },
    render: function() {
        return (
            <div onClick={this.handleClick}>{this.props.folder.title} - {this.props.folder.id}</div>
            );
    }
});

module.exports = FolderItemComponent;