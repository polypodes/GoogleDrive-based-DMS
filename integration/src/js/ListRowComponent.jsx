var React = require('react');

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

        window.location.assign(
          'http://localhost/app_dev.php/files/' + this.props.file.id
        );
    },
    render: function() {
        return (
            <li className="files-row" onClick={this.handleDownload} data-icon={this.props.file.fileExtension} >
                <span className="files-field files-name"><b>{this.props.file.title}</b></span>
                <span className="files-field files-size"><FileSize data={this.props.file.fileSize} /></span>
                <span className="files-download"><a href={this.props.file.downloadUrl} title="Lien vers de téléchargement vers le fichier"></a></span>
            </li>);
    }
});

module.exports = ListRowComponent;