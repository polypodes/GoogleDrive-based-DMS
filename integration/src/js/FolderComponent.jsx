var React = require('react');
var FolderStore = require('./FolderStore');
var FolderItemComponent = require('./FolderItemComponent.jsx');
var ListItemComponent = require('./ListItemComponent.jsx');
var If = require('./If.jsx');
var NProgress = require('nprogress');
var $ = require('zepto-browserify').$;

var FolderComponent = React.createClass({
    getInitialState: function() {
        FolderStore.init();
        return {
            folders: [],
            files: [],
            breadcrumb: [],
            layout: 'list'
        };
    },
    componentWillMount: function() {
        NProgress.start();
        $('.pagination-prev').attr('disabled', 'true');
    },
    componentDidMount: function() {
        this.unsubscribe = FolderStore.listen(this.onFoldersUpdate);
    },
    componentWillUnmount: function() {
        this.unsubscribe();
    },
    onFoldersUpdate: function(arbo, hasPagination, isFirstPage) {
        arbo.layout = this.state.layout;
        this.setState(arbo);

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
    getParent: function() {
        FolderStore.getParentFolder();
    },
    showThumbnail: function() {
        this.state.layout = 'thumbnail';
        this.setState(this.arbo);
    },
    showList: function() {
        this.state.layout = 'list';
        this.setState(this.arbo);
    },
    handlePrev: function() {
        FolderStore.getPrev();
        $('.pagination-btn').attr("disabled", "true");
    },
    handleNext: function() {
        FolderStore.getNext();
        $('.pagination-btn').attr("disabled", "true");
    },
    render: function() {
        NProgress.done();
        $('.content').scrollTop(0);

        return (
                <section>
                    <h1 className="title-1">Parcourir</h1>
                    <p className="instruction">Pour parcourir les fichiers par dossier, cliquer sur le dossiers voulut pour accèder à ses sous-dossiers et fichiers.</p>
                    {this.state.folders.map(function(folder) {
                        return <FolderItemComponent key={folder.id} folder={folder} />;
                    })}
                    <div className="breadcrumb">
                        {this.state.breadcrumb.map(function(item, index) {
                            return <span key={index} >/{item}</span>;
                        })}
                    </div>
                    <If test={this.state.breadcrumb.length > 1}>
                        <button className="btn-parent" onClick={this.getParent}>Dossier parent</button>
                    </If>
                    <If test={this.state.files.list}>
                        <div>
                            <aside className="files-button">
                                <button className="files-button-list" onClick={this.showList}></button>
                                <button className="files-button-thumbnail" onClick={this.showThumbnail}></button>
                            </aside>
                            <ListItemComponent data={this.state.files.list} layout={this.state.layout} />
                            <div>
                                <button className="pagination-btn pagination-prev" onClick={this.handlePrev}>Précédent</button>
                                <button className="pagination-btn pagination-next" onClick={this.handleNext}>Suivant</button>
                            </div>
                        </div>
                    </If>
                </section>
            );
    }
});

module.exports = FolderComponent;