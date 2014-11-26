vidme-api
=========

Limited implementation of the vid.me API in PHP.

So far, it supports logging into the API, retrieving profile information and uploading videos.

Usage
=====

```php
// set up the API with a Guzzle client
$client = new GuzzleHttp\Client(['base_url' => 'https://api.vid.me']);
$authStorage = new Vidme\Storage\AuthStorage('cache/auth.php');

// we're using username/password authentication for now. OAuth support will follow soon.
$auth = new Vidme\Api\Auth($client, $authStorage, $username, $password);

// check login information from cache, or initialize it if it's not there
if (!$auth->check()) {
    $auth->create();
}

// after POSTing a form with a video and optional title and description...

// put the original extension on the upload tmp file, or the API will reject it
$newTmpName = $_FILES['video']['tmp_name'] . '.' . pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);

rename($_FILES['video']['tmp_name'], $newTmpName);

// be sure to remove the file after it's been uploaded
register_shutdown_function(function () use ($newTmpName) {
    unlink($newTmpName);
});

$responseUpload = $client->post('/video/upload', ['body' => [
    'filedata' => fopen($newTmpName, 'r'),
    'token' => $auth->authData['auth']['token'],
    'filename' => $_FILES['video']['name'],
    'title' => $_POST['title'],
    'description' => $_POST['description'],
]]);

$responseData = $responseUpload->json();

if (true === $responseData['status']) {
    echo "Upload complete !</p>";
    echo "<p>Duration: {$responseData['duration']}</p>";
    echo "<p><a href=\"{$responseData['video']['full_url']}\" target=\"_blank\">Open it !</a></p>";
} else {
    // something went wrong
}
```