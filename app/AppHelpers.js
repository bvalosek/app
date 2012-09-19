/**
 * App module helpers
 *
 * Interact with the App Kohana module backend
 *
 * @author      Brandon Valosek
 *
 * @package     appModule.app
 * @require     jquery/jquery-1.8.1.js
 */

(function(app, $){

    /**
     * get the templated HTML from the backend by package name
     *
     * packageName : html package
     * callback : function after recieving
     * data : data to pass to the view
     * async : block on request
     * */
    app.getHtmlPackage = function(opts) {
        $.ajax({
            url: '/app/html_package/' + opts.packageName,
            async: opts.isAsync === undefined ? true : opts.isAsync,
            data: { data: opts.data || {} },

            success: function(html) {
                console.log('loaded html package ' + opts.packageName);
                if (opts.callback)
                    opts.callback(html);
            }
        });
    };

}(window.app = window.app || {}, jQuery));



