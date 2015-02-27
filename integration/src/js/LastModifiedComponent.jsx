var React = require('react');
var LastModifiedStore = require('./LastModifiedStore');
var ListItemComponent = require('./ListItemComponent.jsx');
var $ = require('zepto-browserify').$;
var NProgress = require('nprogress');

var LastModifiedComponent = React.createClass({
    getInitialState: function() {
        NProgress.start();
        LastModifiedStore.init();
        return {
            files: '',
            layout: 'list'
        };
    },
    componentDidMount: function() {
        var that = this;
        this.unsubscribe = LastModifiedStore.listen(this.filesUpdated);
    },
    componentWillUnmount: function() {
        this.unsubscribe();
    },
    showList: function() {
        this.setState({layout: 'list'});
    },
    showThumbnail: function() {
        this.setState({layout: 'thumbnail'});
    },
    filesUpdated: function(newFiles) {
        this.setState({files: newFiles});
    },
    render: function() {
        if (this.state.files) {
            NProgress.done();
            return (
                <div>
                    <h1 className="title-1">Derniers fichiers modifiés</h1>
                    <p className="instruction">Vous trouverez ici la liste des 100 derniers fichiers modifiés.</p>
                    <aside className="files-button">
                        <button className="files-button-list" onClick={this.showList}></button>
                        <button className="files-button-thumbnail" onClick={this.showThumbnail}></button>
                    </aside>
                    <ListItemComponent data={this.state.files} terms={''} layout={this.state.layout} />
                </div>
            );
        } else {
          return <div>Loading...</div>;
        }
    }
});

module.exports = LastModifiedComponent;
