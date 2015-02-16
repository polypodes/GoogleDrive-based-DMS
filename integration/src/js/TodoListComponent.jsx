var React = require('react');
var TodoItemComponent = require('./TodoItemComponent.jsx');

var TodoListComponent = React.createClass({
	handleItemClick: function() {
		console.log('okok');
		alert('clicked');
	},
	render: function() {
		var createItem = function(itemText) {
			return <li>{itemText} - <input type="button" onClick={this.handleItemClick} value="ok" /></li>;
		}
		return (
			<div>
				{this.props.items.map(function(item) {
		          return <TodoItemComponent item={item} />;
		        })}
			</div>
		);
	}
});

module.exports = TodoListComponent;