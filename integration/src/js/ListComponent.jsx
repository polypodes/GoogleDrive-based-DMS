var React = require('react');
var FileStore = require('./FileStore');
var ListItemComponent = require('./ListItemComponent.jsx');

var list = [];

var ListComponent = React.createClass({
    getInitialState: function() {
        return {files: ''};
    },
    componentDidMount: function() {
        var that = this;
        this.unsubscribe = FileStore.listen(this.filesUpdated);
    },
    filesUpdated: function(newFiles) {
        this.setState({
          files: newFiles
        });
    },
    render: function() {
        if (this.state.files) {
          return (
                <div>
                    <h1>Hello world</h1>
                    <ul>
                        <ListItemComponent data={this.state.files} />
                    </ul>
                </div>
            );
        } else {
          return <div>Loading...</div>;
        }
    }
});

module.exports = ListComponent;