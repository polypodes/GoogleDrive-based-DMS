var React = require('react');
var TodoActions = require('./TodoActions');

var TodoItemComponent = React.createClass({
	handleItemClick: function() {
		TodoActions.deleteTodo(this.props.item);
	},
	render: function() {
		return (
				<ul>
					<li>{this.props.item} - <input type="button" onClick={this.handleItemClick} value="ok" /></li>
				</ul>
			);
	}
});

module.exports = TodoItemComponent;