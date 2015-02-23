var React = require('react');
var NProgress = require('nprogress');

var FileSize = React.createClass({
    formatFileSize: function() {
        return Math.round(this.props.data / 100)/10;
    },
    render: function() {
        return <div>{this.formatFileSize()} KB</div>;
    }
});

var ListRowComponent = React.createClass({
    handleDownload: function(e) {
        console.log(e);
        e.preventDefault();
        NProgress.start();
        window.location.assign(
          'http://localhost/app_dev.php/files/' + this.props.file.id
        );
        var url = 'http://localhost/app_dev.php/files/' + this.props.file.id;
        setTimeout(NProgress.done, 4000);
    },
    count: 0,
    downloadUrl: function(url, callback) {
        var hiddenIFrameID = 'hiddenDownloader' + this.count++;
        var iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
        iframe.src = url;
        callback();
    },
    render: function() {
        if(this.props.layout === "list") {
            return (
                <li className="files-row" onClick={this.handleDownload} data-icon={this.props.file.fileExtension} >
                    <span className="files-field files-name"><b>{this.props.file.title}</b></span>
                    <span className="files-field files-size"><FileSize data={this.props.file.fileSize} /></span>
                    <span className="files-download"><a href={this.props.file.downloadUrl} title="Lien vers de téléchargement vers le fichier"></a></span>
                </li>);
        } else if(this.props.layout === "thumbnail") {
            // {this.props.file.thumbnailLink}
            return (
                <li className="files-thumbnail" onClick={this.handleDownload} >
                    <div className="files-thumbnail-box">
                        <img src={this.props.file.thumbnailLink} className="files-thumbnail-img" />
                        <div className="files-download files-thumbnail-download">
                            <a href={this.props.file.downloadUrl} title="Lien vers de téléchargement vers le fichier">
                                Télécharger
                            </a>
                        </div>
                    </div>
                    <span className="files-thumbnail-name" data-icon={this.props.file.fileExtension}><b>{this.props.file.title}</b></span>
                </li>);
        }

    }
});

module.exports = ListRowComponent;