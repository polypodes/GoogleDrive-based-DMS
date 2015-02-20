var React = require('react');
var Router = require('react-router');
var DefaultRoute = Router.DefaultRoute;
var Route = Router.Route;
var TodoComponent = require('./TodoComponent.jsx');
var App = require('./AppComponent.jsx');
var ListComponent = require('./ListComponent.jsx');
var BrowseComponent = require('./BrowseComponent.jsx');
var FolderComponent = require('./FolderComponent.jsx');
var StatsComponent = require('./StatsComponent.jsx');

var defaultRoute = React.createClass({
    render: function() {
        return (<h1>404</h1>);
    }
});

/**
 * Routes
 */
var routes = (
  <Route name="app" path="/" handler={App}>
    <Route name="list" handler={ListComponent} />
    <Route name="result" handler={ListComponent} />
    <Route name="browse" handler={BrowseComponent} />
    <Route name="folder" handler={FolderComponent} />
    <Route name="stats" handler={StatsComponent} />
    <DefaultRoute handler={defaultRoute} />
  </Route>
);

Router.run(routes, function (Handler) {
  React.render(<Handler/>, document.body);
});

// React.renderComponent(<TodoComponent />, document.getElementById('app'));