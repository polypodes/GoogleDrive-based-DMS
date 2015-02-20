var React = require('react');
var FileStore = require('./FileStore');
var ListItemComponent = require('./ListItemComponent.jsx');
var $ = require('zepto-browserify').$;
var If = require('./If.jsx');

var classes = "list"

var ListComponent = React.createClass({
    getInitialState: function() {
        FileStore.init();
        return {
            files: '',
            layout: 'list'
        };
    },
    componentDidMount: function() {
        var that = this;
        this.unsubscribe = FileStore.listen(this.filesUpdated);
    },
    componentWillUnmount: function() {
        this.unsubscribe();
    },
    showList: function() {
        this.setState({
          files: this.state.files,
          terms: this.state.terms,
          layout: 'list'
        });
    },
    showThumbnail: function() {
        this.setState({
          files: this.state.files,
          terms: this.state.terms,
          layout: 'thumbnail'
        });
    },
    filesUpdated: function(newFiles, newTerms) {
        this.setState({
          files: newFiles,
          terms: newTerms,
          layout: this.state.layout
        });
    },
    render: function() {
        if (this.state.files) {
          return (
                <div>
                    <aside className="files-button">
                        <button className="files-button-list" onClick={this.showList}></button>
                        <button className="files-button-thumbnail" onClick={this.showThumbnail}></button>
                    </aside>
                    <If test={this.state.terms}>
                        <h1>Résultats de votre recherche pour le(s) mot(s) : {this.state.terms}</h1>
                    </If>
                    <ListItemComponent data={this.state.files} terms={this.state.terms} layout={this.state.layout} />
                </div>
            );
        } else {
          return <div>Loading...</div>;
        }
    }
});

module.exports = ListComponent;