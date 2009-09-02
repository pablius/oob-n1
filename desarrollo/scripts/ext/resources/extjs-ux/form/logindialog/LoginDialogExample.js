Ext.BLANK_IMAGE_URL = '../../resources/images/default/s.gif';

Ext.onReady(function() {
	Ext.QuickTips.init();

    var loginDialog = new Ext.ux.form.LoginDialog({
        modal : true,
        forgotPasswordLink : 'http://www.microsoft.com/protect/yourself/password/create.mspx'
    });

    loginDialog.show();
});