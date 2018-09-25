## Customize `php-gitlab-api`

### How to set custom headers (including `User-Agent`)?

By providing a `Gitlab\HttpClient\Builder` to the `Gitlab\Client` constructor, you can customize the HTTP client.

```php
$plugin = new Http\Client\Common\Plugin\HeaderSetPlugin([
    'User-Agent' => 'Foobar',
]);

$builder = new Gitlab\HttpClient\Builder();
$builder->addPlugin($plugin);

$client = new Gitlab\Client($builder);
```
Read more about [HTTPlug plugins here](http://docs.php-http.org/en/latest/plugins/introduction.html#how-it-works).

### How to customize the HTTP client timeout?
As timeout configuration is not compatible with HTTP client abstraction, you have to create the `Gitlab\Client` with
an explicit HTTP client implementation.

```php
$httpClient = Http\Adapter\Guzzle6\Client::createWithConfig([
    'timeout' => 1.0
]);
$client = Gitlab\Client::createWithHttpClient($httpClient);
```
