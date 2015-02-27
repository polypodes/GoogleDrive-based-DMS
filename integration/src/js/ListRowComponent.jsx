var React = require('react');
var NProgress = require('nprogress');
var $ = require('zepto-browserify').$;
var CONST = require('./Constant');

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
        e.preventDefault();
        NProgress.start();

        $('.notify').toggleClass('show');
        $('.notify').html('Votre téléchargement est sur le point de démarer…');
        setTimeout(function() {
            $('.notify').toggleClass('show');
        }, 2500);

        window.location.assign(
            CONST.API_GET_FILE + this.props.file.id
        );
        setTimeout(NProgress.done, 3000);
    },
    notifyCopy: function() {
        $('.notify').toggleClass('show');
        $('.notify').html('Le lien à bien été copié dans votre presse-papier');

        setTimeout(function() {
            $('.notify').toggleClass('show');
        }, 3000);
    },
    render: function() {
        if(this.props.layout === "list") {
            return (
                <li className="files-row" data-icon={this.props.file.fileExtension}>
                    <span className="files-field files-name"><b>{this.props.file.title}</b></span>
                    <span className="files-field files-size"><FileSize data={this.props.file.fileSize} /></span>
                    <span className="files-download"><button className="files-download-copy" data-clipboard-text={document.location.host + CONST.API_GET_FILE + this.props.file.id}>Copier le lien</button><a href="#" onClick={this.handleDownload} title="Lien vers de téléchargement vers le fichier"></a></span>
                </li>);
        } else if(this.props.layout === "thumbnail") {
            return (
                <li className="files-thumbnail" >
                    <div className="files-thumbnail-box">
                        <img src={this.props.file.thumbnailLink} className="files-thumbnail-img" />
                        <div className="files-download files-thumbnail-download">
                            <a href="#" onClick={this.handleDownload} title="Lien vers de téléchargement vers le fichier">
                                Télécharger
                            </a>
                            <button className="files-download-copy" data-clipboard-text={document.location.host + CONST.API_GET_FILE + this.props.file.id}>Copier le lien</button>
                        </div>
                    </div>
                    <span className="files-thumbnail-name" data-icon={this.props.file.fileExtension}><b>{this.props.file.title}</b></span>
                </li>);
        }

    }
});

module.exports = ListRowComponent;