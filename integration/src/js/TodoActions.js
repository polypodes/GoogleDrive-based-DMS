var Reflux = require('reflux');

var TodoActions = Reflux.createActions([
	'createTodo',
	'deleteTodo',
]);

module.exports = TodoActions;