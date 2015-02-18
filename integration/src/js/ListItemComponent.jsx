var React = require('react');
var FileStore = require('./FileStore');
var ListRowComponent = require('./ListRowComponent.jsx');

var list = [];

var ListItemComponent = React.createClass({
    render: function() {
        if(this.props.data.length) {
            return (
                <div>
                    <ul className="files-heading">
                        <li>Nom</li>
                        <li>Taille</li>
                        <li>Télécharger</li>
                    </ul>
                    <ul className="files">
                        {this.props.data.map(function(item) {
                          return <ListRowComponent file={item} />;
                        })}
                    </ul>
                </div>
            );
        }
        return <h1>Aucun résultat trouvé</h1>;
    }
});

module.exports = ListItemComponent;