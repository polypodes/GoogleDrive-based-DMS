var React = require('react');
var FileStore = require('./FileStore');

var list = [];

var ListItemComponent = React.createClass({
    render: function() {
        return (
            <div>
                {this.props.data.map(function(item) {
                    console.log(item);
                  return (
                    <li>
                        <span>{item.title}</span> -
                        <span>{item.fileExtension}</span> -
                        <span>{item.fileSize}</span> -
                        <span>{item.downloadUrl}</span>
                    </li>);
                })}
            </div>
        );
    }
});

module.exports = ListItemComponent;