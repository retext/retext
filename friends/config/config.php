<?php

if ($_SERVER['HTTP_HOST'] == 'friends.retext.it') {
    define('CONS_KEY', 'oWTWUJd6E1olMYDQlhTRjg');
    define('CONS_SECRET', 'oHOPRr4PX5Rip3CGBMk93dlB5cZkBVFM7uGINOROA');
    define('DEV', false);
    define('DROPBOX', false);
} else {
    define('CONS_KEY', 'sCCto2dNIcydwhuumNEsVg');
    define('CONS_SECRET', 'wlcI9bqPYdOERi8j0hxZV8b73RZ0BRDRCbtyTvymQ');
    define('DEV', true);
    define('DROPBOX', '/mnt/extra/Dropbox/Projekte/retext/Dokumente/');
}