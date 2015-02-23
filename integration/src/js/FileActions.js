var Reflux = require('reflux');

var FileActions = Reflux.createActions([
	'searchFile',
    'getFileTypes',
    'getFilesByType',
    'getFolders',
    'getFilesFromFolder',
    'lastModifiedFile'
]);

module.exports = FileActions;