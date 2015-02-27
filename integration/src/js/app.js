var React = require('react');

// Router stuff
var Router = require('react-router');
var DefaultRoute = Router.DefaultRoute;
var Route = Router.Route;

// Components
var App = require('./AppComponent.jsx');
var ListComponent = require('./ListComponent.jsx');
var BrowseComponent = require('./BrowseComponent.jsx');
var FolderComponent = require('./FolderComponent.jsx');
var lastModifiedComponent = require('./lastModifiedComponent.jsx');

// Libs
var NProgress = require('nprogress');

/**
 * Routes
 */
var routes = (
  <Route name="app" path="/" handler={App}>
    // File list/Result list view
    <Route name="list" handler={ListComponent} />
    // Filter by type view
    <Route name="browse" handler={BrowseComponent} />
    // Browse through folder
    <Route name="folder" handler={FolderComponent} />
    // Last modified files view
    <Route name="lastmodified" handler={lastModifiedComponent} />
    // Default view
    <DefaultRoute handler={ListComponent} />
  </Route>
);

Router.run(routes, function(Handler) {
    NProgress.configure({ showSpinner: false });
    NProgress.start();

    React.render(<Handler/>, document.body);
});