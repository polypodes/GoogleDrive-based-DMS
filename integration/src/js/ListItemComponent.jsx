var React = require('react');
var FileStore = require('./FileStore');
var ListRowComponent = require('./ListRowComponent.jsx');
var If = require('./If.jsx');
var ZeroClipboard = require('zeroclipboard');
var $ = require('zepto-browserify').$;

var ListItemComponent = React.createClass({
    componentDidMount: function() {
        this.handleCopy();
    },
    componentDidUpdate: function() {
        this.handleCopy();
    },
    handleCopy: function() {
        var client = new ZeroClipboard($("button.files-download-copy"));

        client.on('ready', function(readyEvent) {
            client.on('aftercopy', function(e) {
                $('.notify').toggleClass('show');
                $('.notify').html('Le lien à bien été copié dans votre presse-papier');

                setTimeout(function() {
                    $('.notify').toggleClass('show');
                }, 3000);
            });
        });
    },
    render: function() {
        if(this.props.data.length) {
            return (
                <div>
                    <If test={this.props.layout === 'list'}>
                        <ul className="files-heading">
                            <li>Nom</li>
                            <li>Taille</li>
                            <li>Télécharger</li>
                        </ul>
                    </If>
                    <ul className="files">
                        {this.props.data.map(function(item) {
                          return <ListRowComponent file={item} layout={this.props.layout} key={item.id} />;
                        }.bind(this))}
                    </ul>
                </div>
            );
        } else {
            return <h3>Aucun fichiers</h3>;
        }
    }
});

module.exports = ListItemComponent;