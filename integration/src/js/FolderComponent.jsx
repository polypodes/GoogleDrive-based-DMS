var React = require('react');
var FolderStore = require('./FolderStore');
var FolderItemComponent = require('./FolderItemComponent.jsx');
var ListItemComponent = require('./ListItemComponent.jsx');
var If = require('./If.jsx');
var NProgress = require('nprogress');
var $ = require('zepto-browserify').$;

var FolderComponent = React.createClass({
    getInitialState: function() {
        NProgress.start();
        FolderStore.init();
        $('.pagination-prev').attr('disabled', 'true');

        return {
            folders: [],
            files: [],
            breadcrumb: [],
            layout: 'list'
        };
    },
    componentDidMount: function() {
        this.unsubscribe = FolderStore.listen(this.onFoldersUpdate);
    },
    componentWillUnmount: function() {
        this.unsubscribe();
    },
    onFoldersUpdate: function(arbo, hasPagination, isFirstPage) {
        console.log('L15 ', arbo);
        arbo.layout = this.state.layout;
        this.setState(arbo);

        if(isFirstPage && hasPagination) {
            $('.pagination-prev').attr('disabled', 'true');
            $('.pagination-next').removeAttr('disabled');
            console.log("first page & pagination");
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
        console.log(this.state);
        NProgress.done();
        $('.content').scrollTop(0);

        return (
                <section>
                    {this.state.breadcrumb.map(function(item) {
                        return <span>/ {item}</span>;
                    })}
                    <h1>Folder componenet</h1>
                    {this.state.folders.map(function(folder) {
                        return <FolderItemComponent folder={folder} />;
                    })}
                    <If test={this.state.breadcrumb.length > 1}>
                        <button onClick={this.getParent}>parent</button>
                    </If>
                    <If test={this.state.files.list}>
                        <div>
                            <aside className="files-button">
                                <button className="files-button-list" onClick={this.showList}></button>
                                <button className="files-button-thumbnail" onClick={this.showThumbnail}></button>
                            </aside>
                            <ListItemComponent data={this.state.files.list} layout={this.state.layout} />
                            <button className="pagination-btn pagination-prev" onClick={this.handlePrev}>Précédent</button>
                            <button className="pagination-btn pagination-next" onClick={this.handleNext}>Suivant</button>
                        </div>
                    </If>
                </section>
            );
    }
});

module.exports = FolderComponent;