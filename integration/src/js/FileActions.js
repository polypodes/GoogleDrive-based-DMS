var Reflux = require('reflux');

var FileActions = Reflux.createActions([
	'searchFile',
    'getFileTypes',
    'getFilesByType',
    'getFolders',
    'getFilesFromFolder',
    'lastModifiedFile',
    'getNext',
    'getPrev'
]);

module.exports = FileActions;