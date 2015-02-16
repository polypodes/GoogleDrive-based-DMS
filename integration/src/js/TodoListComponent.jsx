var React = require('react');
var TodoItemComponent = require('./TodoItemComponent.jsx');

var TodoListComponent = React.createClass({
	render: function() {
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