var React = require('react');
var Router = require('react-router');
var RouteHandler = Router.RouteHandler;
var Navigation = require('react-router').Navigation;
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;

var debounce = function(func, wait) {
 // we need to save these in the closure
 var timeout, args, context, timestamp;

 return function() {

  // save details of latest call
  context = this;
  args = [].slice.call(arguments, 0);
  timestamp = new Date();

  // this is where the magic happens
  var later = function() {

   // how long ago was the last call
   var last = (new Date()) - timestamp;

   // if the latest call was less that the wait period ago
   // then we reset the timeout to wait for the difference
   if (last < wait) {
    timeout = setTimeout(later, wait - last);

   // or if not we can null out the timer and run the latest
   } else {
    timeout = null;
    func.apply(context, args);
   }
  };

  // we only need to set the timer now if one isn't already running
  if (!timeout) {
   timeout = setTimeout(later, wait);
  }
 }
};

var App = React.createClass({
    mixins: [Navigation],
    handleSubmit: function(e) {
        e.preventDefault();
        var keyword = this.refs.keyword.getDOMNode().value.trim();
        debounce(FileActions.searchFile(keyword), 500);
    },
    handleMenuButton: function(e) {
        e.preventDefault();
        console.log('menu btn handled');
        $('main').toggleClass('menu-open');
    },
    render: function () {
        return (
            <div>
                <header className='header'>
                    <div className="header-logo">
                        <img src="./images/logo-drive.png" alt="" className="header-logo-1" />
                        <div className="header-logo-2">
                            <img src="./images/logo-sedap.png" alt="" />
                            <span>centre de ressource</span>
                        </div>
                    </div>
                    <div className="header-search">
                        <form onSubmit={this.handleSubmit}>
                            <input type="search" ref="keyword" onKeyUp={this.handleSubmit} placeholder="Rechercher dans Google Drive…" className="header-input" />
                        </form>
                    </div>
                    <div className="header-button">
                        <button className="header-button-item" onClick={this.handleMenuButton}></button>
                    </div>
                </header>
                <main role="main" className="main menu-open">
                    <aside className="menu">
                        <div className="menu-result">
                            Résultats de recherche
                        </div>
                        <nav>
                            <ul>
                                <li>
                                    <a href="" title=""><i></i>Parcourir</a>
                                </li>
                                <li>
                                    <a href="" title=""><i></i>Statistiques</a>
                                </li>
                            </ul>
                        </nav>
                        <article className="menu-sites">
                            <ul>
                                <li><a href="" target="_blank"><img src="" alt="" />LUCID</a></li>
                                <li><a href="" target="_blank"><img src="" alt="" />bellelummières.fr</a></li>
                                <li><a href="" target="_blank"><img src="" alt="" />dix heures dix</a></li>
                                <li><a href="" target="_blank"><img src="" alt="" />atelier sedap</a></li>
                            </ul>
                        </article>
                    </aside>
                    <section className="content">
                        <RouteHandler/>
                    </section>
                </main>
            </div>
        );
    }
});

module.exports = App;