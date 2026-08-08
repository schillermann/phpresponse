# Caching

This section covers how to cache results for optimal performance in `PhpResponse` using the `Sticky` decorator.

---

## Caching and Performance with Sticky

The request body stream or a text resource should only be read once.
If you want to add a caching feature, use `Sticky` as a decorator.

You should use `Sticky` whenever reading the original `Text` (such as `PhpResponse\Request\Body` or another custom `Text` object) has side effects or a high performance cost (e.g., repeatedly reading input streams, querying databases/files, or calling external APIs).

```php
<?php

use PhpResponse\Request\Body;
use PhpResponse\Text\Sticky;

// The request body is read only once upon the first call to string()
$body = new Sticky(new Body());

// First call: reads from stream and caches the result
$content = $body->string();

// Second call: retrieves from memory instantly without reading the stream again
$contentAgain = $body->string();
```
