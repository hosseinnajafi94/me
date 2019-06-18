me.validation = (function ($) {
    var validations = {
        required: function (value, messages, options) {
            var valid = false;
            if (options.requiredValue === undefined) {
                if (options.strict && value !== undefined || !options.strict && !isEmpty(isString(value) ? $.trim(value) : value)) {
                    valid = true;
                }
            } else if (!options.strict && value == options.requiredValue || options.strict && value === options.requiredValue) {
                valid = true;
            }
            if (!valid) {
                addMessage(messages, options.message, value);
            }
        },
        string: function (value, messages, options) {
            if (!isString(value)) {
                addMessage(messages, options.message, value);
                return;
            }
            if (options.is !== undefined && value.length !== options.is) {
                addMessage(messages, options.notEqual, value);
                return;
            }
            if (options.min !== undefined && value.length < options.min) {
                addMessage(messages, options.tooShort, value);
            }
            if (options.max !== undefined && value.length > options.max) {
                addMessage(messages, options.tooLong, value);
            }
        },
        number: function (value, messages, options) {
            if (isEmpty(value)) {
                return;
            }
            if (isString(value) && !options.pattern.test(value)) {
                addMessage(messages, options.message, value);
                return;
            }
            if (options.min !== undefined && value < options.min) {
                addMessage(messages, options.tooSmall, value);
            }
            if (options.max !== undefined && value > options.max) {
                addMessage(messages, options.tooBig, value);
            }
        },
        file: function (attribute, messages, options) {
            var fileInput = attribute.get(0);
            var files = getUploadedFiles(fileInput, messages, options);
            $.each(files, function (i, file) {
                validateFile(file, messages, options);
            });
        },
        image: function (attribute, messages, options, deferredList) {
            var files = getUploadedFiles(attribute, messages, options);
            $.each(files, function (i, file) {
                validateFile(file, messages, options);
                // Skip image validation if FileReader API is not available
                if (typeof FileReader === "undefined") {
                    return;
                }
                var deferred = $.Deferred();
                validations.validateImage(file, messages, options, deferred, new FileReader(), new Image());
                deferredList.push(deferred);
            });
        },
        validateImage: function (file, messages, options, deferred, fileReader, image) {
            image.onload = function () {
                validateImageSize(file, image, messages, options);
                deferred.resolve();
            };

            image.onerror = function () {
                messages.push(options.notImage.replace(/\{file\}/g, file.name));
                deferred.resolve();
            };

            fileReader.onload = function () {
                image.src = this.result;
            };

            // Resolve deferred if there was error while reading data
            fileReader.onerror = function () {
                deferred.resolve();
            };

            fileReader.readAsDataURL(file);
        },
        range: function (value, messages, options) {
            if (options.skipOnEmpty && isEmpty(value)) {
                return;
            }

            if (!options.allowArray && $.isArray(value)) {
                addMessage(messages, options.message, value);
                return;
            }

            var inArray = true;

            $.each($.isArray(value) ? value : [value], function (i, v) {
                if ($.inArray(v, options.range) == -1) {
                    inArray = false;
                    return false;
                } else {
                    return true;
                }
            });

            if (options.not === undefined) {
                options.not = false;
            }

            if (options.not === inArray) {
                addMessage(messages, options.message, value);
            }
        },
        regularExpression: function (value, messages, options) {
            if (options.skipOnEmpty && isEmpty(value)) {
                return;
            }

            if (!options.not && !options.pattern.test(value) || options.not && options.pattern.test(value)) {
                addMessage(messages, options.message, value);
            }
        },
        email: function (value, messages, options) {
            if (options.skipOnEmpty && isEmpty(value)) {
                return;
            }

            var valid = true,
                    regexp = /^((?:"?([^"]*)"?\s)?)(?:\s+)?(?:(<?)((.+)@([^>]+))(>?))$/,
                    matches = regexp.exec(value);

            if (matches === null) {
                valid = false;
            } else {
                var localPart = matches[5],
                        domain = matches[6];

                if (options.enableIDN) {
                    localPart = punycode.toASCII(localPart);
                    domain = punycode.toASCII(domain);

                    value = matches[1] + matches[3] + localPart + '@' + domain + matches[7];
                }

                if (localPart.length > 64) {
                    valid = false;
                } else if ((localPart + '@' + domain).length > 254) {
                    valid = false;
                } else {
                    valid = options.pattern.test(value) || (options.allowName && options.fullPattern.test(value));
                }
            }

            if (!valid) {
                addMessage(messages, options.message, value);
            }
        },
        url: function (value, messages, options) {
            if (options.skipOnEmpty && isEmpty(value)) {
                return;
            }
            if (options.defaultScheme && !/:\/\//.test(value)) {
                value = options.defaultScheme + '://' + value;
            }
            var valid = true;
            if (options.enableIDN) {
                var matches = /^([^:]+):\/\/([^\/]+)(.*)$/.exec(value);
                if (matches === null) {
                    valid = false;
                } else {
                    value = matches[1] + '://' + punycode.toASCII(matches[2]) + matches[3];
                }
            }
            if (!valid || !options.pattern.test(value)) {
                addMessage(messages, options.message, value);
            }
        },
        trim: function ($form, attribute, options, value) {
            var $input = $form.find(attribute.input);
            if ($input.is(':checkbox, :radio')) {
                return value;
            }
            value = $input.val();
            if (!options.skipOnEmpty || !isEmpty(value)) {
                value = $.trim(value);
                $input.val(value);
            }
            return value;
        },
        captcha: function (value, messages, options) {
            if (options.skipOnEmpty && isEmpty(value)) {
                return;
            }
            // CAPTCHA may be updated via AJAX and the updated hash is stored in body data
            var hash = $('body').data(options.hashKey);
            hash = hash == null ? options.hash : hash[options.caseSensitive ? 0 : 1];
            var v = options.caseSensitive ? value : value.toLowerCase();
            for (var i = v.length - 1, h = 0; i >= 0; --i) {
                h += v.charCodeAt(i);
            }
            if (h != hash) {
                addMessage(messages, options.message, value);
            }
        },
        compare: function (value, messages, options, $form) {
            if (options.skipOnEmpty && isEmpty(value)) {
                return;
            }
            var compareValue, valid = true;
            if (options.compareAttribute === undefined) {
                compareValue = options.compareValue;
            } else {
                var $target = $('#' + options.compareAttribute);
                if (!$target.length) {
                    $target = $form.find('[name="' + options.compareAttributeName + '"]');
                }
                compareValue = $target.val();
            }
            if (options.type === 'number') {
                value = value ? parseFloat(value) : 0;
                compareValue = compareValue ? parseFloat(compareValue) : 0;
            }
            switch (options.operator) {
                case '==':
                    valid = value == compareValue;
                    break;
                case '===':
                    valid = value === compareValue;
                    break;
                case '!=':
                    valid = value != compareValue;
                    break;
                case '!==':
                    valid = value !== compareValue;
                    break;
                case '>':
                    valid = value > compareValue;
                    break;
                case '>=':
                    valid = value >= compareValue;
                    break;
                case '<':
                    valid = value < compareValue;
                    break;
                case '<=':
                    valid = value <= compareValue;
                    break;
                default:
                    valid = false;
                    break;
            }
            if (!valid) {
                addMessage(messages, options.message, value);
            }
        },
        ip: function (value, messages, options) {
            if (options.skipOnEmpty && isEmpty(value)) {
                return;
            }
            var negation = null, cidr = null, matches = new RegExp(options.ipParsePattern).exec(value);
            if (matches) {
                negation = matches[1] || null;
                value = matches[2];
                cidr = matches[4] || null;
            }
            if (options.subnet === true && cidr === null) {
                addMessage(messages, options.messages.noSubnet, value);
                return;
            }
            if (options.subnet === false && cidr !== null) {
                addMessage(messages, options.messages.hasSubnet, value);
                return;
            }
            if (options.negation === false && negation !== null) {
                addMessage(messages, options.messages.message, value);
                return;
            }
            var ipVersion = value.indexOf(':') === -1 ? 4 : 6;
            if (ipVersion == 6) {
                if (!(new RegExp(options.ipv6Pattern)).test(value)) {
                    addMessage(messages, options.messages.message, value);
                }
                if (!options.ipv6) {
                    addMessage(messages, options.messages.ipv6NotAllowed, value);
                }
            } else {
                if (!(new RegExp(options.ipv4Pattern)).test(value)) {
                    addMessage(messages, options.messages.message, value);
                }
                if (!options.ipv4) {
                    addMessage(messages, options.messages.ipv4NotAllowed, value);
                }
            }
        }
    };
    function isEmpty(value) {
        return value === null || value === undefined || ($.isArray(value) && value.length === 0) || value === '';
    }
    function isString(value) {
        return typeof value === 'string' || value instanceof String;
    }
    function addMessage(messages, message, value) {
        messages.push(message.replace(/\{value\}/g, value));
    }
    function getUploadedFiles(fileInput, messages, options) {
        if (typeof File === "undefined") {
            return [];
        }
        if (typeof fileInput === "undefined") {
            return [];
        }
        var files = fileInput.files;
        if (!files) {
            messages.push(options.message);
            return [];
        }
        if (options.maxFiles && options.maxFiles < files.length) {
            messages.push(options.tooMany);
            return [];
        }
        if (options.minFiles && files.length < options.minFiles) {
            messages.push(options.tooFew);
            return [];
        }
        return files;
    }
    function validateFile(file, messages, options) {
        if (options.extensions && options.extensions.length > 0) {
            var index = file.name.lastIndexOf('.');
            var ext = !~index ? '' : file.name.substr(index + 1, file.name.length).toLowerCase();
            if (!~options.extensions.indexOf(ext)) {
                messages.push(options.wrongExtension.replace(/\{file\}/g, file.name));
            }
        }
        if (options.maxSize && options.maxSize < file.size) {
            messages.push(options.tooBig.replace(/\{file\}/g, file.name));
        }
        if (options.minSize && options.minSize > file.size) {
            messages.push(options.tooSmall.replace(/\{file\}/g, file.name));
        }
    }
    return validations;
})(jQuery);
