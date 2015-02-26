var React = require('react');
var Router = require('react-router');
var DefaultRoute = Router.DefaultRoute;
var Route = Router.Route;
var App = require('./AppComponent.jsx');
var ListComponent = require('./ListComponent.jsx');
var BrowseComponent = require('./BrowseComponent.jsx');
var FolderComponent = require('./FolderComponent.jsx');
var lastModifiedComponent = require('./lastModifiedComponent.jsx');
var NProgress = require('nprogress');

NProgress.start();

/**
 * Routes
 */
var routes = (
  <Route name="app" path="/" handler={App}>
    <Route name="list" handler={ListComponent} />
    <Route name="browse" handler={BrowseComponent} />
    <Route name="folder" handler={FolderComponent} />
    <Route name="lastmodified" handler={lastModifiedComponent} />
    <DefaultRoute handler={ListComponent} />
  </Route>
);

Router.run(routes, function (Handler) {
  React.render(<Handler/>, document.body);
});