var React = require('react');
var FileStore = require('./FileStore');
var ListRowComponent = require('./ListRowComponent.jsx');
var If = require('./If.jsx');

var ListItemComponent = React.createClass({
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
                          return <ListRowComponent file={item} layout={this.props.layout} />;
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