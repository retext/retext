var casper = require('casper').create({
    logLevel:"debug"
});

casper.start(casper.cli.get('url'), function (self) {
    self.test.assertTitle('re:text – text workflow done right', 'Titel prüfen');
});

casper.then(function (self) {
    this.click('a[href="#status"]');
    self.test.assertExists('#api-server-time', 'Server-Zeit muss vorhanden sein');
    self.test.assertExists('#api-server-version', 'Server-Version muss vorhanden sein');
    this.waitFor(function() {
        return self.fetchText('#api-server-version') != 'unknown' && self.fetchText('#api-server-time') != 'unknown';
    }, function() {
        self.test.assertEquals(self.fetchText('#api-server-version'), '1', 'API-Version muss 1 sein.');
    });
});

casper.run(function () {
    this.test.renderResults(true, 0, this.cli.get('save') || false);
});