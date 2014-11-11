/**
 * Click handler that sends events to Google Analytics
 *
 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/events?hl=fr#crossbrowser
 * @example To handle a click on a <a>, add a class="googleAnalyticsEvent" attribute to it.
 */

var links = document.getElementsByClassName('googleAnalyticsEvent');
for(var i = 0; i < links.length; i++)
{
    var elem = links.item(i);
    addListener(elem, 'click', function () {
        if('undefined' != typeof ga
            && 'undefined' != typeof elem.title
            && 'undefined' != typeof elem.href) {
            ga('send', 'event', 'link', 'click', elem.title, elem.href);
        }
    });
}
/**
 * Utility to wrap the different behaviors between W3C-compliant browsers
 * and IE when adding event handlers.
 *
 * @param {Object} element Object on which to attach the event listener.
 * @param {string} type A string representing the event type to listen for
 *     (e.g. load, click, etc.).
 * @param {function()} callback The function that receives the notification.
 */
function addListener(element, type, callback) {
    if (element.addEventListener) element.addEventListener(type, callback);
    else if (element.attachEvent) element.attachEvent('on' + type, callback);
}
