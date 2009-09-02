/**
 * Free and simple to use loginDialog for ExtJS 2.x
 *
 * @author  Albert Varaksin (ExtJS 2.x)
 * @author  Sumit Madan (ExtJS 3.x)
 * @license LGPLv3 http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0 beta, 07/12/2008 - ExtJS 2.x
 * @version 1.0, 05/03/2009 - ExtJS 3.x
 */

Ext.namespace('Ext.ux.form');

/**
 * Login dialog constructor
 *
 * @param {Object} config
 * @extends {Ext.util.Observable}
 */
Ext.ux.form.LoginDialog = function (config) {
    Ext.apply(this, config);

    // LoginDialog events
    this.addEvents ({
        'show'      : true, // when dialog is visible and rendered
        'cancel'    : true, // When user cancelled the login
        'success'   : true, // on succesfful login
        'failure'   : true, // on failed login
        'submit'    : true  // about to submit the data
    });
    Ext.ux.form.LoginDialog.superclass.constructor.call(this, config);

    // head info panel
    this._headPanel = new Ext.Panel ({
        baseCls : 'x-plain',
        html    : this.message,
        cls     : 'ux-auth-header',
        region  : 'north',
        height  : 60
    });

    // store username id to focus on window show event
    this.usernameId = Ext.id();
    this.passwordId = Ext.id();
    this._loginButtonId = Ext.id();
    this._cancelButtonId = Ext.id();
    this._rememberMeId = Ext.id();

    // form panel
    this._formPanel = new Ext.form.FormPanel ({
        region      : 'center',
        border      : false,
        bodyStyle   : "padding: 10px;",
        waitMsgTarget: true,
        labelWidth  : 75,
        defaults    : { width: 300 },
        items : [{
            xtype           : 'textfield',
            id              : this.usernameId,
            name            : this.usernameField,
            fieldLabel      : this.usernameLabel,
            vtype           : this.usernameVtype,
            validateOnBlur  : false,
            allowBlank      : false
        }, {
            xtype           : 'textfield',
            inputType       : 'password',
            id              : this.passwordId,
            name            : this.passwordField,
            fieldLabel      : this.passwordLabel,
            vtype           : this.passwordVtype,
            validateOnBlur  : false,
            allowBlank      : false
        }, {
            xtype: 'box',
            autoEl: 'div',
            height: 10
        }]
    });

    // Default buttons and keys
    var buttons = [{
        id          : this._loginButtonId,
        text        : this.loginButton,
        iconCls     : 'ux-auth-login',
        width       : 90,
        handler     : this.submit,
        scale       : 'medium',
        scope       : this
    }];
    var keys = [{
        key     : [10,13],
        handler : this.submit,
        scope   : this
    }];

    // if cancel button exists
    if (typeof this.cancelButton == 'string') {
        buttons.push({
            id      : this._cancelButtonId,
            text    : this.cancelButton,
            iconCls : 'ux-auth-close',
            width   : 90,
            handler : this.cancel,
            scale   : 'medium',
            scope   : this
        });
        keys.push({
            key     : [27],
            handler : this.cancel,
            scope   : this
        });
    }


    // create the window
    this._window = new Ext.Window ({
        width       : 420,
        height      : 280,
        closable    : false,
        resizable   : false,
        draggable   : false,
        modal       : this.modal,
        iconCls     : 'ux-auth-header-icon',
        title       : this.title,
        layout      : 'border',
        bodyStyle   : 'padding:5px;',
        buttons     : buttons,
        keys        : keys,
        items       : [this._headPanel, this._formPanel]
    });

    // when window is visible set focus to the username field
    // and fire "show" event
    this._window.on ('show', function () {
        Ext.getCmp(this.usernameId).focus(false, true);
        Ext.getCmp(this.passwordId).setRawValue('');
        this.fireEvent('show', this);
    }, this);
};


// Extend the Observable class
Ext.extend (Ext.ux.form.LoginDialog, Ext.util.Observable, {

    /**
     * LoginDialog window title
     *
     * @type {String}
     */
    title :'Login',

    /**
     * The message on the LoginDialog
     *
     * @type {String}
     */
    message : 'Access to this location is restricted to authorized users only.' +
        '<br />Please type your username and password.',

    /**
     * When login failed and no server message sent
     *
     * @type {String}
     */
    failMessage : 'Unable to log in',

    /**
     * When submitting the login details
     *
     * @type {String}
     */
    waitMessage : 'Please wait...',

    /**
     * The login button text
     *
     * @type {String}
     */
    loginButton : 'Login',

    /**
     * Cancel button
     *
     * @type {String}
     */
    cancelButton : 'Close',

    /**
     * Username field label
     *
     * @type {String}
     */
    usernameLabel : 'Username',

    /**
     * Username field name
     *
     * @type {String}
     */
    usernameField : 'username',

    /**
     * Username validation
     *
     * @type {String}
     */
    usernameVtype : 'alphanum',

    /**
     * Password field label
     *
     * @type {String}
     */
    passwordLabel : 'Password',

    /**
     * Password field name
     *
     * @type {String}
     */
    passwordField : 'password',

    /**
     * Password field validation
     *
     * @type {String}
     */
    passwordVtype : 'alphanum',

    /**
     * Language field name
     *
     * @type {String}
     */
    languageField : 'lang',

    /**
     * Language field label
     *
     * @type {String}
     */
    languageLabel : 'Language',

    /**
     * RememberMe field name
     *
     * @type {String}
     */
    rememberMeField : 'rememberme',

    /**
     * RememberMe field label
     *
     * @type {String}
     */
    rememberMeLabel : 'Remember me on this computer',

    /**
     * Forgot Password field label
     *
     * @type {String}
     */
    forgotPasswordLabel : 'Forgot Password?',

    /**
     * Forgot Password hyperlink
     *
     * @type {String}
     */
    forgotPasswordLink : 'about:blank',

    /**
     * Request url
     *
     * @type {String}
     */
    url : '/auth/login',
    /**
     * Form submit method
     *
     * @type {String}
     */
    method : 'post',

    /**
     * Open modal window
     *
     * @type {Bool}
     */
    modal : false,

    /**
     * CSS identifier
     *
     * @type {String}
     */
    _cssId : 'ux-LoginDialog-css',

    /**
     * Head info panel
     *
     * @type {Ext.Panel}
     */
    _headPanel : null,

    /**
     * Form panel
     *
     * @type {Ext.form.FormPanel}
     */
    _formPanel : null,

    /**
     * The window object
     *
     * @type {Ext.Window}
     */
    _window : null,

    /**
     * Set the LoginDialog message
     *
     * @param {String} msg
     */
    setMessage : function (msg) {
        this._headPanel.body.update(msg);
    },


    /**
     * Show the LoginDialog
     *
     * @param {Ext.Element} el
     */
    show : function (el) {
        this._window.show(el);
    },


    /**
     * Close the LoginDialog and cleanup
     */
    close : function () {
        this._window.hide()
    },


    /**
     * Cancel the login (closes the dialog window)
     */
    cancel : function () {
        if (this.fireEvent('cancel', this))
        {
            this.close();
        }
    },


    /**
     * Submit login details to the server
     */
    submit : function () {
        var form = this._formPanel.getForm();

        if (form.isValid())
        {
            Ext.getCmp(this._loginButtonId).disable();
            if(Ext.getCmp(this._cancelButtonId)) {
                Ext.getCmp(this._cancelButtonId).disable();
            }
            if (this.fireEvent('submit', this, form.getValues()))
            {
                this.setMessage (this.message);
                form.submit ({
                    url     : this.url,
                    method  : this.method,
                    waitMsg : this.waitMessage,
                    success : this.onSuccess,
                    failure : this.onFailure,
                    scope   : this
                });
            }
        }
    },


    /**
     * On success
     *
     * @param {Ext.form.BasicForm} form
     * @param {Ext.form.Action} action
     */
    onSuccess : function (form, action) {
        if (this.fireEvent('success', this, action)) {
            // enable buttons
            Ext.getCmp(this._loginButtonId).enable();
            if(Ext.getCmp(this._cancelButtonId)) {
                Ext.getCmp(this._cancelButtonId).enable();
            }

            this.close();
        }
    },


    /**
     * On failures
     *
     * @param {Ext.form.BasicForm} form
     * @param {Ext.form.Action} action
     */
    onFailure : function (form, action) {
        // enable buttons
        Ext.getCmp(this._loginButtonId).enable();
        if(Ext.getCmp(this._cancelButtonId)) {
            Ext.getCmp(this._cancelButtonId).enable();
        }

        var msg = '';
        if (action.result && action.result.message) msg = action.result.message || this.failMessage;
        else msg = this.failMessage;
        this.setMessage (this.message + '<br /><span class="error">' + msg + '</span>');
        this.fireEvent('failure', this, action, msg);
    }

});
