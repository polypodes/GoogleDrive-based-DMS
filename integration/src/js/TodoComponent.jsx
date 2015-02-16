var React = require('react');
var TodoListComponent = require('./TodoListComponent.jsx');
var TodoStore = require('./TodoStore');
var TodoActions = require('./TodoActions');

var TodoComponent = React.createClass({
	getInitialState: function() {
		return {items: TodoStore.getTodos(), text: ''};
	},
	onChange: function(e) {
		this.setState({text: e.target.value});
	},
	onTodoChange: function(todos) {
		this.setState({items: todos, text: ''});
	},
	handleSubmit: function(e) {
		e.preventDefault();
		TodoActions.createTodo(this.state.text);
	},
	componentDidMount: function() {
		this.unsubscribe = TodoStore.listen(this.onTodoChange);
	},
	componentDidUnmount: function() {
		this.unsubscribe();
	},
	render: function() {
		return (
			<div>
				<h3>TODO</h3>
				<TodoListComponent items={this.state.items} />
				<form onSubmit={this.handleSubmit}>
					<input onChange={this.onChange} value={this.state.text} />
					<button>{'Add #' + (this.state.items.length + 1)}</button>
				</form>
			</div>
		);
	}
});

module.exports = TodoComponent;