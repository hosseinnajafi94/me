(function ($) {
    function validate($form, attributeOptions) {
        var attributes = $form.data('activeForm').attributes;
        var messages = {};
        $.each(attributes, function (index, options) {
            if (attributeOptions === undefined || attributeOptions.input === options.input) {
                var $input = $(options.input);
                var value = $input.val();
                var message = [];
                options.validate.call(this, $input, value, message, $form);
                messages[index] = message;
            }
        });
        return messages;
    }
    function showMessages(attributes, messages) {
        var isValid = true, hasError;
        $.each(messages, function (i) {
            $(attributes[i].container).removeClass('has-error has-success');
            (hasError = messages[i].length > 0) && (isValid = false);
            $(attributes[i].container).addClass((hasError ? 'has-error' : 'has-success')).find('.help-block').html((hasError ? messages[i][0] : null));
        });
        return isValid;
    }
    $.fn.activeForm = function (attributes, options) {
        var $this = $(this);
        $this.each(function () {
            var $form = $(this);
            if ($form.data('activeForm'))
                return;
            $form.data('activeForm', {options, attributes});
            $form.on('submit.activeForm', function () {
                var messages = validate($form);
                return showMessages(attributes, messages);
            });
            $.each(attributes, function (i) {
                var options = attributes[i];
                $(options.input).on('blur.activeForm', function () {
                    var messages = validate($form, options);
                    showMessages(attributes, messages);
                });
            });
        });
        return $this;
    };
})(jQuery);