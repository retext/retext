<?php

error_reporting(-1);
ini_set('display_errors', 1);
setlocale(LC_ALL, 'de_DE.utf8');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/silex.phar';
require_once __DIR__ . '/../vendor/php-markdown/markdown.php';

function listFiles()
{
    if (!apc_exists('files-ttl')) {
        apc_store('files-ttl', '-', 60 * 60);
        $files = array();
        foreach (new IteratorIterator(new DirectoryIterator(DROPBOX)) as $file) {
            if (is_dir($file->getPathname())) continue;
            if (substr($file->getPathname(), -1) == '~') continue;
            $type = substr($file->getPathname(), -3);
            $mtime = filemtime($file->getPathname());
            $files[] = array('type' => $type, 'file' => $file->getFilename(), 'label' => substr($file->getFilename(), 0, -4), 'modified' => round((time() - $mtime) / 86400));
            $sort[] = $mtime;
        }
        array_multisort($sort, SORT_DESC, $files);
        apc_store('files', $files);
    }
    return apc_fetch('files');
}

function filterList(array $list)
{
    return array_map(function($item) { return $item->screen_name; }, $list);
}

function listFriends()
{
    if (!apc_exists('friends-ttl')) {
        apc_store('friends-ttl', '-', 60 * 60 * 24);
        $friendsList = json_decode(file_get_contents('https://api.twitter.com/1/lists/members.json?slug=friends&owner_screen_name=retext&skip_status=1'));
        $teamList = json_decode(file_get_contents('https://api.twitter.com/1/lists/members.json?slug=team&owner_screen_name=retext&include_entities=0&skip_status=1'));
        $friends = array_merge(filterList($friendsList->users), filterList($teamList->users));
        $friends[] = 'retext';
        apc_store('friends', $friends);
    }
    return apc_fetch('friends');
}

$friends = listFriends();

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/templates',
    'twig.class_path' => __DIR__ . '/../vendor/twig/lib',
));
$app->register(new Silex\Provider\SessionServiceProvider());

$app->before(function () use ($app, $friends)
{
    $app['session']->set('vip', in_array(strtolower($app['session']->get('username')), $friends));
});

$app->get('/', function() use($app)
{
    return $app['twig']->render('home.twig', array('username' => $app['session']->get('username'), 'vip' => $app['session']->get('vip'), 'DEV' => DEV, 'files' => $app['session']->get('vip') ? listFiles() : array()));
});

$app->get('/file/{filename}', function($filename) use($app)
{
    if (!$app['session']->get('vip')) $app->abort(403, 'You are not my friend.');
    $files = listFiles();
    $file = array_filter($files, function($item) use($filename)
    {
        return $item['file'] == $filename;
    });
    if (empty($file)) $app->abort(404, 'The file ' . $filename . ' could not be found.');
    $file = array_pop($file);

    if ($file['type'] !== 'txt') {
        $stream = function () use ($file)
        {
            readfile(DROPBOX . $file['file']);
        };
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($fi, DROPBOX . $file['file']);
        finfo_close($fi);
        return $app->stream($stream, 200, array('Content-Type' => $mime));
    } else {
        $content = file_get_contents(DROPBOX . $file['file']);
        $structure = array();
        preg_match_all('/^(#+) (.+)/m', $content, $matches, PREG_SET_ORDER);
        $md = Markdown($content);
        foreach ($matches as $match) {
            $lvl = strlen($match[1]);
            if ($lvl != 2) continue;
            $h = 'h' . $lvl;
            $id = preg_replace('/[^0-9a-z]/', '', strtolower($match[2]));
            $structure[] = array(
                'id' => $id,
                'label' => $match[2],
            );
            $md = str_replace('<' . $h . '>' . $match[2] . '</' . $h . '>', '<' . $h . ' id="' . $id . '">' . $match[2] . '</' . $h . '>', $md);
        }
        return $app['twig']->render('file.twig', array('username' => $app['session']->get('username'), 'vip' => $app['session']->get('vip'), 'DEV' => DEV, 'files' => $files, 'file' => $file, 'content' => $md, 'structure' => $structure));
    }
});

$app->get('/login', function () use ($app)
{
    // check if the user is already logged-in
    if (null !== ($username = $app['session']->get('username'))) {
        return $app->redirect('/');
    }

    $oauth = new OAuth(CONS_KEY, CONS_SECRET, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
    $request_token = $oauth->getRequestToken('https://twitter.com/oauth/request_token');

    $app['session']->set('secret', $request_token['oauth_token_secret']);

    return $app->redirect('https://twitter.com/oauth/authenticate?oauth_token=' . $request_token['oauth_token']);
});

$app->get('/logout', function () use ($app)
{
    $app['session']->invalidate();
    return $app->redirect('/');
});

$app->get('/oauth', function() use ($app)
{
    // check if the user is already logged-in
    if (null !== ($username = $app['session']->get('username'))) {
        return $app->redirect('/');
    }

    $oauth_token = $app['request']->get('oauth_token');

    if ($oauth_token == null) {
        $app->abort(400, 'Invalid token');
    }

    $secret = $app['session']->get('secret');

    $oauth = new OAuth(CONS_KEY, CONS_SECRET, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
    $oauth->setToken($oauth_token, $secret);

    try {
        $oauth_token_info = $oauth->getAccessToken('https://twitter.com/oauth/access_token');
        // retrieve Twitter user details
        $oauth->setToken($oauth_token_info['oauth_token'], $oauth_token_info['oauth_token_secret']);
        $oauth->fetch('https://twitter.com/account/verify_credentials.json');
        $json = json_decode($oauth->getLastResponse());
        $app['session']->set('username', $json->screen_name);

        // Mail me
        $msg = sprintf('Login to %s from @%s', $_SERVER['HTTP_HOST'], $json->screen_name);
        mail('m@retext.it', $msg, $msg);

        return $app->redirect('/');
    } catch (OAuthException $e) {
        $app->abort(401, $e->getMessage());
    }
});

$app->run();
