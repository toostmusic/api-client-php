# Niland API PHP Wrapper

Setup
-------------
To setup your project, follow these steps:

1. Install the package via [Composer](https://getcomposer.org/doc/00-intro.md):
```bash
composer require niland/api-client-php
```
2. Next you'll have to initialize the client with your API-Key. You can find it on [your Niland API account](https://api.niland.io/2.0/dashboard/your-account).

```php
// composer autoload
require __DIR__ . '/vendor/autoload.php';
$client = new \NilandApi\Client('YourAPIKey');
```

Quick Start
-------------

List tracks using pagination
```php
$response = $client->get('tracks', array('page_size' => 10, 'page' => 2));
```

Retrieve a track by its reference
```php
$response = $client->get('tracks/reference/foobar');
```

Find tracks by similarity and/or tags
```php
$response = $client->get('tracks', array(
    'similar_ids' => array(1234),
    'tag_ids'     => array(21, 41)
));
```

Post a track
```php
$response = $client->post('tracks', array(
    'title'     => 'foobar',
    'reference' => 'foobar',
    'tags'      => array(21, 41),
    'audio'     => fopen('/path/to/your/audio/file.mp3', 'r')
));
```
