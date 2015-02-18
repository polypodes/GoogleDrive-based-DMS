var util = (function() {

    var debounce = (function() {
        var timer = null;

        return function(fn, delay) {
            if(timer === null) {
                timer = setTimeout(fn, delay);
                return;
            }
            clearTimeout(timer);
            timer = setTimeout(fn, delay);
        };
    })();

    return {
        debounce: debounce
    }
})();

module.exports = util;