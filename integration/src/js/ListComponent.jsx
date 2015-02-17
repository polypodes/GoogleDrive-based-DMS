var React = require('react');
var FileStore = require('./FileStore');
var ListItemComponent = require('./ListItemComponent.jsx');

var If = React.createClass({
    render: function() {
        if (this.props.test) {
            return this.props.children;
        }
        else {
            return false;
        }
    }
});

var ListComponent = React.createClass({
    getInitialState: function() {
        return {files: ''};
    },
    componentDidMount: function() {
        var that = this;
        this.unsubscribe = FileStore.listen(this.filesUpdated);
    },
    filesUpdated: function(newFiles, newTerms) {
        this.setState({
          files: newFiles,
          terms: newTerms
        });
    },
    render: function() {
        if (this.state.files) {
          return (
                <div>
                    <If test={this.state.terms}>
                        <h1>Résultat de votre recherche pour le(s) mot(s) : {this.state.terms}</h1>
                    </If>
                    <ul>
                        <ListItemComponent data={this.state.files} terms={this.state.terms} />
                    </ul>
                </div>
            );
        } else {
          return <div>Loading...</div>;
        }
    }
});

module.exports = ListComponent;