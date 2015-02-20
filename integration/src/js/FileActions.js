var Reflux = require('reflux');

var FileActions = Reflux.createActions([
	'searchFile',
    'getFileTypes',
    'getFilesByType',
    'getFolders',
    'getFilesFromFolder',
]);

module.exports = FileActions;