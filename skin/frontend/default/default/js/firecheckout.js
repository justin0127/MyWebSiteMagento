var FireCheckout = Class.create();
FireCheckout.prototype = {
    initialize: function(form, urls, agreement){
        this.acceptAgreementText = agreement;
        this.successUrl = urls.success;
        this.saveUrl = urls.save;
        this.updateUrl = urls.update;
        this.failureUrl = urls.failure;
        this.form = form;
        this.loadWaiting = false;
        this.validator = new Validation(this.form);
        this.sectionsToValidate = [
            payment
        ];
        if (typeof shippingMethod === 'object') {
            this.sectionsToValidate.push(shippingMethod);
        }
        this._addEventListeners();
    },

    _addEventListeners: function() {
        $('login-form') && $('login-form').observe('submit', function(e) {
            Event.stop(e);

            if (!loginForm.validator.validate()) {
                return;
            }

            $('login-please-wait').show();
            $('send2').setAttribute('disabled', 'disabled');
            $$('#login-form .buttons-set')[0]
                .addClassName('disabled')
                .setOpacity(0.5);

            new Ajax.Request($('login-form').action, {
                parameters: $('login-form').serialize(),
                onSuccess: function(transport) {
                    FireCheckout.Messenger.clear('login-form');

                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        FireCheckout.Messenger.add(response.error, 'login-form', 'error');
                    }
                    if (response.redirect) {
                        document.location = response.redirect;
                        return;
                    }
                    $('login-please-wait').hide();
                    $('send2').removeAttribute('disabled');
                    $$('#login-form .buttons-set')[0]
                        .removeClassName('disabled')
                        .setOpacity(1);
                }
            });
        });

        $('forgot-password-form') && $('forgot-password-form').observe('submit', function(e) {
            Event.stop(e);

            if (!forgotForm.validator.validate()) {
                return;
            }

            $('forgot-please-wait').show();
            $('btn-forgot').setAttribute('disabled', 'disabled');
            $$('#forgot-password-form .buttons-set')[0]
                .addClassName('disabled')
                .setOpacity(0.5);

            new Ajax.Request($('forgot-password-form').action, {
                parameters: $('forgot-password-form').serialize(),
                onSuccess: function(transport) {
                    FireCheckout.Messenger.clear('forgot-password-form');

                    $('forgot-please-wait').hide();
                    $('btn-forgot').removeAttribute('disabled');
                    $$('#forgot-password-form .buttons-set')[0]
                        .removeClassName('disabled')
                        .setOpacity(1);

                    var response = transport.responseText.evalJSON();

                    if (response.error) {
                        FireCheckout.Messenger.add(response.error, 'forgot-password-form', 'error');
                    } else if (response.message) {
                        firecheckoutWindow.activate('login');
                        FireCheckout.Messenger.clear('login-form');
                        FireCheckout.Messenger.add(response.message, 'login-form', 'success');
                    }
                }
            });
        });
    },

    ajaxFailure: function(){
        location.href = this.failureUrl;
    },

    _disableEnableAll: function(element, isDisabled) {
        var descendants = element.descendants();
        for (var k in descendants) {
            descendants[k].disabled = isDisabled;
        }
        element.disabled = isDisabled;
    },

    setLoadWaiting: function(flag) {
        if (flag) {
            var container = $('review-buttons-container');
            container.addClassName('disabled');
            container.setStyle({opacity:0.5});
            this._disableEnableAll(container, true);
        } else if (this.loadWaiting) {
            var container = $('review-buttons-container');
            container.removeClassName('disabled');
            container.setStyle({opacity:1});
            this._disableEnableAll(container, false);
        }
        this.loadWaiting = flag;
    },

    save: function() {
        if (this.loadWaiting != false) {
            return;
        }

        var isValid = true;

        if (!this.validator.validate()) {
            isValid = false;
        }

        for (i in this.sectionsToValidate) {
            if (typeof this.sectionsToValidate[i] === 'function') {
                continue;
            }
            if (!this.sectionsToValidate[i].validate()) {
                isValid = false;
            }
        }
        FireCheckout.Messenger.clear('checkout-review-submit');
        $$('#checkout-review-submit .checkout-agreements input[type="checkbox"]').each(function(el) {
            if (!el.checked) {
                FireCheckout.Messenger.add(this.acceptAgreementText, 'checkout-review-submit', 'error');
                isValid = false;
                throw $break;
            }
        }.bind(this));

        if (!isValid) {
            // scroll to error
            var validationMessages = $$('.validation-advice, .messages').findAll(function(el) {
                return el.visible();
            });
            if (!validationMessages.length) {
                return;
            }

            var viewportSize = document.viewport.getDimensions();
            var hiddenMessages = [];
            var needToScroll = true;

            validationMessages.each(function(el) {
                var offset = el.viewportOffset();
                if (offset.top < 0
                    || offset.top > viewportSize.height
                    || offset.left < 0
                    || offset.left > viewportSize.width) {

                    hiddenMessages.push(el);
                } else {
                    needToScroll = false;
                }
            });

            if (needToScroll) {
                Effect.ScrollTo(validationMessages[0], {
                    duration: 1,
                    offset: -20
                });
            }
            return;
        }

        checkout.setLoadWaiting(true);
        var params = Form.serialize(this.form);
        $('review-please-wait').show();
        var request = new Ajax.Request(this.saveUrl, {
            method:'post',
            parameters:params,
            onSuccess: this.setResponse.bind(this),
            onFailure: this.ajaxFailure.bind(this)
        });
    },

    update: function(params) {
        var parameters = $(this.form).serialize(true);
        for (var i in params) {
            if (!params[i]) {
                continue;
            }
            var size = $('checkout-' + i + '-load').getDimensions();
            $('checkout-' + i + '-load')
                .setStyle({
                    'width': size.width + 'px',
                    'height': size.height + 'px'
                })
                .update('')
                .addClassName('loading');
            parameters[i] = params[i];
        }
        checkout.setLoadWaiting(true);
        var request = new Ajax.Request(this.updateUrl, {
            method: 'post',
            onSuccess: this.setResponse.bind(this),
            onFailure: this.ajaxFailure.bind(this),
            parameters: parameters
        });
    },

    setResponse: function(response){
        response = response.responseText.evalJSON();

        if (response.redirect) {
            location.href = response.redirect;
            return true;
        }

        if (response.order_created) {
            window.location = this.successUrl;
            return;
        } else if (response.error_messages) {
            var msg = response.error_messages;
            if (typeof(msg) == 'object') {
                msg = msg.join("\n");
            }
            alert(msg);
        }

        checkout.setLoadWaiting(false);
        $('review-please-wait').hide();

        if (response.update_section) {
            for (var i in response.update_section) {
                $('checkout-' + i + '-load')
                    .setStyle({
                        'width': 'auto',
                        'height': 'auto'
                    })
                    .update(response.update_section[i])
                    .setOpacity(1)
                    .removeClassName('loading');

                if (i === 'shipping-method') {
                    shippingMethod.addObservers();
                }
            }
        }

        if (response.duplicateBillingInfo) {
            shipping.syncWithBilling();
        }

        return false;
    }
};

// billing
var Billing = Class.create();
Billing.prototype = {
    initialize: function(){
        $('billing:country_id') && $('billing:country_id').observe('change', function() {
            if ($('billing:region_id')) {
                function resetRegionId() {
                    $('billing:region_id').value = '';
                    $('billing:region_id')[0].selected = true;
                }
                resetRegionId.delay(0.2);
            }
            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked) {
                shipping.syncWithBilling();
            }
            checkout.update({
                'payment-method': 1,
                'shipping-method': !$('shipping:same_as_billing') || $('shipping:same_as_billing').checked ? 1 : 0
            });
        });
        $('billing-address-select') && $('billing-address-select').observe('change', function() {
            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked) {
                shipping.syncWithBilling();
            }
            checkout.update({
                'payment-method': 1,
                'shipping-method': !$('shipping:same_as_billing') || $('shipping:same_as_billing').checked ? 1 : 0
            });
        });
        $('billing:region_id') && $('billing:region_id').observe('change', function() {
            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked) {
                shipping.syncWithBilling();
                checkout.update({
                    'review': 1
                });
            } else if (!$('shipping:same_as_billing')) {
                checkout.update({
                    'review': 1
                });
            }
        });
        $('billing:postcode') && $('billing:postcode').observe('change', function() {
            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked) {
                shipping.syncWithBilling();
                checkout.update({
                    'review': 1
                });
            } else if (!$('shipping:same_as_billing')) {
                checkout.update({
                    'review': 1
                });
            }
        });
        
        
       $$('input[name="chooseFree"]').each(function(el) {
            el.observe('click', function() {
                checkout.update({
                    'review': 1
                });
            });
        });
        
    },

    newAddress: function(isNew){
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('billing-new-address-form');
        } else {
            Element.hide('billing-new-address-form');
        }
    },

    resetSelectedAddress: function(){
        var selectElement = $('billing-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },

    setCreateAccount: function(flag) {
        if (flag) {
            $('register-customer-password').show();
        } else {
            $('register-customer-password').hide();
        }
    }
};

// shipping
var Shipping = Class.create();
Shipping.prototype = {
    initialize: function(form){
        this.form = form;

        $('shipping:country_id') && $('shipping:country_id').observe('change', function() {
            if ($('shipping:region_id')) {
                $('shipping:region_id').value = '';
                $('shipping:region_id')[0].selected = true;
            }
            checkout.update({
                'shipping-method': 1
            });
        });
        $('shipping-address-select') && $('shipping-address-select').observe('change', function() {
            checkout.update({
                'shipping-method': 1
            });
        });
        $('shipping:region_id') && $('shipping:region_id').observe('change', function() {
            checkout.update({
                'review': 1
            });
        });
        $('shipping:postcode') && $('shipping:postcode').observe('change', function() {
            checkout.update({
                'review': 1
            });
        });
    },

    newAddress: function(isNew){
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('shipping-new-address-form');
        } else {
            Element.hide('shipping-new-address-form');
        }
    },

    resetSelectedAddress: function(){
        var selectElement = $('shipping-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },

    setSameAsBilling: function(flag) {
        $('shipping:same_as_billing').checked = flag;
        $('billing:use_for_shipping').value = flag ? 1 : 0;
        if (flag) {
            $('shipping-address').hide();
            this.syncWithBilling();
            checkout.update({
                'shipping-method': 1
            });
        } else {
            $('shipping-address').show();
        }
    },

    syncWithBilling: function () {
        $('billing-address-select') && this.newAddress(!$('billing-address-select').value);
        $('shipping:same_as_billing').checked = true;
        $('billing:use_for_shipping').value = 1;
        if (!$('billing-address-select') || !$('billing-address-select').value) {
            arrElements = Form.getElements(this.form);
            for (var elemIndex in arrElements) {
                if (arrElements[elemIndex].id) {
                    var sourceField = $(arrElements[elemIndex].id.replace(/^shipping:/, 'billing:'));
                    if (sourceField){
                        arrElements[elemIndex].value = sourceField.value;
                    }
                }
            }
            //$('shipping:country_id').value = $('billing:country_id').value;
            shippingRegionUpdater.update();
            $('shipping:region_id').value = $('billing:region_id').value;
            $('shipping:region').value = $('billing:region').value;
            //shippingForm.elementChildLoad($('shipping:country_id'), this.setRegionValue.bind(this));
        } else {
            $('shipping-address-select').value = $('billing-address-select').value;
        }
    },

    setRegionValue: function(){
        $('shipping:region').value = $('billing:region').value;
    }
};

// shipping method
var ShippingMethod = Class.create();
ShippingMethod.prototype = {
    initialize: function() {
        this.addObservers();
    },

    addObservers: function() {
        $$('input[name="shipping_method"]').each(function(el) {
            el.observe('click', function() {
                checkout.update({
                    'review': 1
                });
            });
        });
    },

    validate: function() {
        FireCheckout.Messenger.clear('checkout-shipping-method-load');
        var methods = document.getElementsByName('shipping_method');
        if (methods.length==0) {
            FireCheckout.Messenger.add(
                Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make neccessary changes in your shipping address.'),
                'checkout-shipping-method-load',
                'error'
            );
            return false;
        }

        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        FireCheckout.Messenger.add(
            Translator.translate('Please specify shipping method.'),
            'checkout-shipping-method-load',
            'error'
        );
        return false;
    }
};

// payment
var Payment = Class.create();
Payment.prototype = {
    beforeInitFunc:$H({}),
    afterInitFunc:$H({}),
    beforeValidateFunc:$H({}),
    afterValidateFunc:$H({}),
    initialize: function(container){
        this.cnt = container;
    },

    addBeforeInitFunction : function(code, func) {
        this.beforeInitFunc.set(code, func);
    },

    beforeInit : function() {
        (this.beforeInitFunc).each(function(init){
           (init.value)();
        });
    },

    init : function () {
        this.beforeInit();
        var elements = $(this.cnt).select('input', 'select', 'textarea');
        var method = null;
        for (var i=0; i<elements.length; i++) {
            if (elements[i].name=='payment[method]') {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete','off');
        }
        if (method) this.switchMethod(method);
        this.afterInit();

        this.initWhatIsCvvListeners();
    },

    addAfterInitFunction : function(code, func) {
        this.afterInitFunc.set(code, func);
    },

    afterInit : function() {
        (this.afterInitFunc).each(function(init){
            (init.value)();
        });
    },

    switchMethod: function(method){
        if (this.currentMethod && $('payment_form_'+this.currentMethod)) {
            var form = $('payment_form_'+this.currentMethod);
            form.style.display = 'none';
            var elements = form.select('input', 'select', 'textarea');
            for (var i=0; i<elements.length; i++) elements[i].disabled = true;
        }
        if ($('payment_form_'+method)){
            var form = $('payment_form_'+method);
            form.style.display = '';
            var elements = form.select('input', 'select', 'textarea');
            for (var i=0; i<elements.length; i++) elements[i].disabled = false;
        } else {
            //Event fix for payment methods without form like "Check / Money order"
            document.body.fire('payment-method:switched', {method_code : method});
        }
        this.currentMethod = method;
    },

    addBeforeValidateFunction : function(code, func) {
        this.beforeValidateFunc.set(code, func);
    },

    beforeValidate : function() {
        var validateResult = true;
        var hasValidation = false;
        (this.beforeValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    validate: function() {
        FireCheckout.Messenger.clear('checkout-payment-method-load');
        var result = this.beforeValidate();
        if (result) {
            return true;
        }
        var methods = document.getElementsByName('payment[method]');
        if (methods.length==0) {
            FireCheckout.Messenger.add(
                Translator.translate('Your order cannot be completed at this time as there is no payment methods available for it.'),
                'checkout-payment-method-load',
                'error'
            );
            return false;
        }
        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        result = this.afterValidate();
        if (result) {
            return true;
        }
        FireCheckout.Messenger.add(
            Translator.translate('Please specify payment method.'),
            'checkout-payment-method-load',
            'error'
        );
        return false;
    },

    addAfterValidateFunction : function(code, func) {
        this.afterValidateFunc.set(code, func);
    },

    afterValidate : function() {
        var validateResult = true;
        var hasValidation = false;
        (this.afterValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    initWhatIsCvvListeners: function(){
        $$('.cvv-what-is-this').each(function(element){
            Event.observe(element, 'click', toggleToolTip);
        });
    }
};

FireCheckout.Messenger = {
    add: function(message, section, type) {
        var ul = $(section).select('.messages')[0];
        if (!ul) {
            $(section).insert({
                top: '<ul class="messages"></ul>'
            });
            ul = $(section).select('.messages')[0]
        }
        var li = $(ul).select('.' + type + '-msg')[0];
        if (!li) {
            $(ul).insert({
                top: '<li class="' + type + '-msg"><ul></ul></li>'
            });
            li = $(ul).select('.' + type + '-msg')[0];
        }
        $(li).select('ul')[0].insert(
            '<li>' + message + '</li>'
        );
    },

    clear: function(section) {
        var ul = $(section).select('.messages')[0];
        if (ul) {
            ul.remove();
        }
    }
};

FireCheckout.Window = Class.create();
FireCheckout.Window.prototype = {
    initialize: function(config) {
        this.config = Object.extend({
            width       : 'auto',
            height      : 'auto',
            maxWidth    : 500,
            maxHeight   : 400,
            triggers    : null,
            markup: '<div class="d-shadow-wrap">'
                +   '<div class="content"></div>'
                +   '<div class="d-sh-cn d-sh-tl"></div><div class="d-sh-cn d-sh-tr"></div>'
                + '</div>'
                + '<div class="d-sh-cn d-sh-bl"></div><div class="d-sh-cn d-sh-br"></div>'
                + '<a href="javascript:void(0)" class="close"></a>'
        }, config || {});

        this._prepareMarkup();
        this._attachEventListeners();
    },

    show: function() {
        if (!this.centered) {
            this.center();
        }
        $$('select').invoke('addClassName', 'firecheckout-hidden');
        this.window.show();
    },

    hide: function() {
        this.window.hide();
        $$('select').invoke('removeClassName', 'firecheckout-hidden');
    },

    update: function(content) {
        this.content.setStyle({
            width   : isNaN(this.config.width)  ? this.config.width  : this.config.width + 'px',
            height  : isNaN(this.config.height) ? this.config.height : this.config.height + 'px'
        });
        this.content.update(content);
        this.updateSize();
        this.center();
        return this;
    },

    center: function() {
        var viewportSize   = document.viewport.getDimensions();
        var viewportOffset = document.viewport.getScrollOffsets();
        this.setPosition(
            viewportSize.width / 2 - this.window.getWidth() / 2 + viewportOffset.left,
            viewportSize.height / 2 - this.window.getHeight() / 2 + viewportOffset.top
        );
        this.centered = true;
    },

    setPosition: function(x, y) {
        this.window.setStyle({
            left: x + 'px',
            top : y + 'px'
        });
    },

    activate: function(trigger) {
        this.update(this.config.triggers[trigger].window.show()).show();
    },

    updateSize: function() {
        this.window.setStyle({
            visibility: 'hidden'
        }).show();
        var size = this.content.getDimensions();
        if ('auto' === this.config.width && size.width > this.config.maxWidth) {
            this.content.setStyle({
                width: this.config.maxWidth + 'px'
            });
        }
        if ('auto' === this.config.height && size.height > this.config.maxHeight) {
            this.content.setStyle({
                height: this.config.maxHeight + 'px'
            });
        }
        this.window.hide().setStyle({
            visibility: 'visible'
        });
    },

    _prepareMarkup: function() {
        this.window = new Element('div');
        this.window.addClassName('firecheckout-window');
        this.window.update(this.config.markup).hide();
        this.content = this.window.select('.content')[0];
        this.close   = this.window.select('.close')[0];
        $(document.body).insert(this.window);
    },

    _attachEventListeners: function() {
        // close window
        this.close.observe('click', this.hide.bind(this));
        document.observe('keypress', this._onKeyPress.bind(this));
        // show window
        if (this.config.triggers) {
            for (var i in this.config.triggers) {
                this.config.triggers[i].el.each(function(el) {
                    var trigger = this.config.triggers[i];
                    el.observe(this.config.triggers[i].event, function(e) {
                        Event.stop(e);
                        if (!trigger.window) {
                            return;
                        }
                        var oldContent = this.content.down();
                        oldContent && $(document.body).insert(oldContent.hide());
                        this.update(trigger.window.show()).show();
                    }.bind(this));
                }.bind(this));
            }
        }
    },

    _onKeyPress: function(e) {
        var code = e.keyCode;
        if (code == Event.KEY_ESC) {
            this.hide();
        }
    }
};
