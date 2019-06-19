window.me = (function ($) {
    var pub = {
        init: function () {
            $('a[data-method]').click(function (e) {
                var method = $(this).data('method');
                if ('post' === method) {
                    e.preventDefault();
                    pub.handlePost.call(this);
                }
            });
        },
        handlePost: function () {
            var url = $(this).attr('href');
            if (pub.hasConfirm.call(this)) {
                pub.confirm.call(this, function () {
                    pub.postRequest.call(this, url);
                });
            } else {
                pub.postRequest.call(this, url);
            }
        },
        hasConfirm: function () {
            return $(this).data('confirm') !== undefined;
        },
        confirm: function (callback) {
            var message = $(this).data('confirm');
            if (window.confirm(message)) {
                callback.call(this);
            }
        },
        postRequest: function (url) {
            var $form = $('<form/>');
            $form.attr('method', 'post');
            $form.attr('action', url);
            $form.appendTo('body');
            $form.submit();
        }
    };
    return pub;
})(jQuery);

$(function () {
    me.init();
});