var React = require('react');
var FileTypeStore = require('./FileTypeStore');
var FileByTypeStore = require('./FileByTypeStore');
var FileActions = require('./FileActions');
var ListItemComponent = require('./ListItemComponent.jsx');
var $ = require('zepto-browserify').$;
var NProgress = require('nprogress');
var If = require('./If.jsx');

var BrowseComponent = React.createClass({
    getInitialState: function() {
        return {
            fileType: [],
            files: [],
            layout: 'list'
        }
    },
    componentWillMount: function() {
        NProgress.start();
        FileActions.getFileTypes();
        $('.pagination-prev').attr('disabled', 'true');
    },
    componentDidMount: function() {
        this.unsubscribe = FileTypeStore.listen(this.fileTypeUpdated);
        this.unsubscribe2 = FileByTypeStore.listen(this.fileByTypeUpdated);
    },
    componentWillUnmount: function() {
        this.unsubscribe();
        this.unsubscribe2();
    },
    fileByTypeUpdated: function(files, hasPagination, isFirstPage) {
        this.state.files = files;
        this.setState(this.state);

        // pagination next/prev stuff
        if(isFirstPage && hasPagination) {
            $('.pagination-prev').attr('disabled', 'true');
            $('.pagination-next').removeAttr('disabled');
        } else if(isFirstPage) {
            $('.pagination-btn').attr('disabled', 'true');
        } else if(hasPagination) {
            $('.pagination-btn').removeAttr('disabled');
        } else if(!hasPagination) {
            $('.pagination-next').attr('disabled', 'true');
            $('.pagination-prev').removeAttr('disabled');
        }
    },
    fileTypeUpdated: function(newFileType) {
        this.setState({
          fileType: newFileType
        });
        $('.pagination-prev').attr('disable', 'true');
        this.handleSelectChange();
    },
    handleSelectChange: function() {
        NProgress.start();
        var selectIndex = this.refs.select.getDOMNode().selectedIndex;
        var text = this.refs.select.getDOMNode()[selectIndex].text;
        FileActions.getFilesByType(text);
    },
    showList: function() {
        this.state.layout = 'list';
        this.setState(this.state);
    },
    showThumbnail: function() {
        this.state.layout = 'thumbnail';
        this.setState(this.state);
    },
    handlePrev: function() {
        FileActions.getPrev();
        $('.pagination-btn').attr("disabled", "true");
    },
    handleNext: function() {
        FileActions.getNext();
        $('.pagination-btn').attr("disabled", "true");
    },
    render: function() {
        if(this.state.fileType.length) {
            NProgress.done();
            $('.content').scrollTop(0);

            return (
                <div>
                    <h1 className="title-1">Filtrer par type de fichier</h1>
                    <p className="instruction">Utiliser le filtre ci-dessous afin de trier les fichiers par type (image, documents, videos etc…)</p>
                    <div className="files-select-wrapper">
                        <select className="files-select" ref="select" id="filetype" name="filetype" onChange={this.handleSelectChange}>
                            <option value="">Trier par type</option>
                            {this.state.fileType.map(function(item, index) {
                              return <option value={item} key={index}>{item}</option>;
                            })}
                        </select>
                    </div>
                    <aside className="files-button">
                        <button className="files-button-list" onClick={this.showList}></button>
                        <button className="files-button-thumbnail" onClick={this.showThumbnail}></button>
                    </aside>
                    <ListItemComponent data={this.state.files} layout={this.state.layout} />
                    <div>
                        <button className="pagination-btn pagination-prev" onClick={this.handlePrev}>Précédent</button>
                        <button className="pagination-btn pagination-next" onClick={this.handleNext}>Suivant</button>
                    </div>
                </div>
            );
        } else {
            return (<span>Chargement…</span>);
        }
    }
});

module.exports = BrowseComponent;