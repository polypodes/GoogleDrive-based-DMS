var Reflux = require('reflux');
var TodoActions = require('./TodoActions');

var _todos = [];

var TodoStore = Reflux.createStore({
	init: function() {
		this.loadTodos();
		this.listenTo(TodoActions.createTodo, this.onCreate);
		this.listenTo(TodoActions.deleteTodo, this.onDelete);
	},
	onCreate: function(todo) {
		_todos.push(todo);
		this.saveTodos()
		this.trigger(_todos);
	},
	onDelete: function(todo) {
		_todos.splice(_todos.indexOf(todo), 1);
		this.saveTodos();
		this.trigger(_todos);
	},
	loadTodos: function() {
		var localStorage = window.localStorage;
		localStorageKey = "todos";

		var todos = localStorage.getItem(localStorageKey);

		if(todos) {
			_todos = JSON.parse(todos);
		}
	},
	saveTodos: function() {
		localStorage.setItem(localStorageKey, JSON.stringify(_todos));
	},
	getTodos: function() {
		return _todos;
	}
});

module.exports = TodoStore;