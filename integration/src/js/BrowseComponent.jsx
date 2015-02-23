var React = require('react');
var FileTypeStore = require('./FileTypeStore');
var FileByTypeStore = require('./FileByTypeStore');
var FileActions = require('./FileActions');
var ListItemComponent = require('./ListItemComponent.jsx');
var NProgress = require('nprogress');

var data = [];

var BrowseComponent = React.createClass({
    getInitialState: function() {
        NProgress.start();
        FileActions.getFileTypes();
        return {
            fileType: [],
            files: [],
            layout: 'list'
        }
    },
    componentDidMount: function() {
        this.unsubscribe = FileTypeStore.listen(this.fileTypeUpdated);
        this.unsubscribe2 = FileByTypeStore.listen(this.fileByTypeUpdated);
        NProgress.done();
    },
    fileByTypeUpdated: function(files) {
        this.state.files = files;
        this.setState(this.state);
    },
    componentWillUnmount: function() {
        this.unsubscribe();
        this.unsubscribe2();
    },
    handleSelectChange: function() {
        var selectIndex = this.refs.select.getDOMNode().selectedIndex;
        var text = this.refs.select.getDOMNode()[selectIndex].text;
        console.log('select changed : ' + selectIndex + ' = ' + text);
        FileActions.getFilesByType(text);
        NProgress.start();
    },
    fileTypeUpdated: function(newFileType) {
        data.fileType = newFileType;
        this.setState({
          fileType: newFileType
        });
        console.log(this.state.fileType);
    },
    showList: function() {
        console.log('okok');
        this.state.layout = 'list';
        this.setState(this.state);
    },
    showThumbnail: function() {
        console.log('okok');
        this.state.layout = 'thumbnail';
        this.setState(this.state);
    },
    render: function() {
        if(this.state.fileType.length) {
            NProgress.done();
            return (
                <div>
                    <h1>Vue parcourir</h1>
                    <aside className="files-button">
                        <select className="files-select" ref="select" id="filetype" name="filetype" onChange={this.handleSelectChange}>
                            <option value="">-- Trier par type --</option>
                            {this.state.fileType.map(function(item) {
                              return <option value={item}>{item}</option>;
                            })}
                        </select>
                        <button className="files-button-list" onClick={this.showList}></button>
                        <button className="files-button-thumbnail" onClick={this.showThumbnail}></button>
                    </aside>
                    <ul>
                        <ListItemComponent data={this.state.files} layout={this.state.layout} />
                    </ul>
                </div>
            );
        } else {
            return <h3>Loading…</h3>;
        }
    }
});

module.exports = BrowseComponent;