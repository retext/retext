casper.start(casper.cli.get('url'), function (self) {
    this.click('a[href="#register"]');
    self.test.assertExists('form#register-form', 'Formular zum Registrieren ist vorhanden.');
    self.test.assertExists('form#register-form input[type=email]', 'E-Mail-Formularfeld ist vorhanden.');
    self.test.assertExists('form#register-form button[type=submit]', 'Button zum Abschicken ist vorhanden.');
    this.fill('form#register-form', {
        'email':'casperjs@retext.it'
    }, true);
    // TODO
    /*
     self.test.assertExists('div.alert-success', 'Info mit Text existiert.');
    this.waitFor(function() {
        return self.fetchText('div.alert');
    }, function() {
        self.test.assertTrue(/dein Postfach/.test(self.fetchText('div[alert alert]')), 'Bestätigung ist da.');
    });
    */
});

casper.run(function () {
    this.echo('message sent').exit();
    casper.test.done();
});
