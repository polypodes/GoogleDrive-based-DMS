var Reflux = require('reflux');

var FileActions = Reflux.createActions([
	'searchFile',
    'getFileTypes',
    'getFilesByType'
]);

module.exports = FileActions;