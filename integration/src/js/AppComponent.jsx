var React = require('react');
var Router = require('react-router');
var RouteHandler = Router.RouteHandler;
var Link = Router.Link;
var Navigation = require('react-router').Navigation;
var FileActions = require('./FileActions');
var $ = require('zepto-browserify').$;
var util = require('./util');
var NProgress = require('nprogress');

/**
 * Main view, handles other views
 */
var App = React.createClass({
    mixins: [Navigation],
    getInitialState: function() {
        return {
            searchViewName: 'Tous les fichiers'
        };
    },
    /**
     * Handle searched keyword and get the result
     */
    handleSubmit: function(e) {
        e.preventDefault();

        /**
         * Wait for user to stop keystroke for submit
         */
        util.debounce(function() {
            var keyword = this.refs.keyword.getDOMNode().value.trim();
            FileActions.searchFile(keyword);
            this.setMenuCurrent();
            this.transitionTo('list');
            this.setState({ searchViewName: 'Résultats de recherche' });
            NProgress.start();
        }.bind(this), 400);
    },
    /**
     * Actions to trigger when the view change
     */
    handleChangeView: function(e) {
        this.setMenuCurrent();
        this.refs.keyword.getDOMNode().value = '';
        this.setState({ searchViewName: 'Tous les fichiers' });
    },
    /**
     * Add css style to current view in menu
     */
    setMenuCurrent: function() {
        $('.current').removeClass('current');
        setTimeout(function() {
            $('.menu .active').parent().toggleClass('current');
        }, 50);
    },
    /**
     * Button to toggle menu
     */
    handleMenuButton: function(e) {
        e.preventDefault();
        $('main').toggleClass('menu-open');
    },
    render: function() {
        return (
            <div>
                <div className="notify"></div>
                <header className='header'>
                    <div className="header-logo">
                        <Link to="list" id="menu-2" onClick={this.handleChangeView}>
                            <img src="./images/logo-drive.png" alt="" className="header-logo-1" />
                            <div className="header-logo-2">
                                <img src="./images/logo-sedap.png" alt="" />
                                <span>centre de ressources</span>
                            </div>
                        </Link>
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
                        <div className="menu-result current">
                            <Link to="list" onClick={this.handleChangeView}>{this.state.searchViewName}</Link>
                        </div>
                        <nav>
                            <ul>
                                <li>
                                    <Link to="folder" id="menu-1" onClick={this.handleChangeView}><i></i>Parcourir</Link>
                                </li>
                                <li>
                                    <Link to="browse" id="menu-1" onClick={this.handleChangeView}><i></i>Filtrer</Link>
                                </li>
                                <li>
                                    <Link to="lastmodified" id="menu-2" onClick={this.handleChangeView}><i></i>Modifications récentes</Link>
                                </li>
                            </ul>
                        </nav>
                        <article className="menu-sites">
                            <ul>
                                <li><a href="http://www.lucid.fr/" target="_blank"><img src="" alt="" />LUCID</a></li>
                                <li><a href="http://www.belleslumieres.fr/" target="_blank"><img src="" alt="" />bellelummières.fr</a></li>
                                <li><a href="http://www.dixheuresdix.com/fr/" target="_blank"><img src="" alt="" />dix heures dix</a></li>
                                <li><a href="http://www.sedap.com/" target="_blank"><img src="" alt="" />atelier sedap</a></li>
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