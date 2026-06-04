# PhpResponses

A web framework in PHP that respects OOP fully, following the principles of "Elegant Objects" by Yegor Bugayenko.

## Core Principles
- **Strictly OOP**: No getters, no setters, no nulls.
- **Composition over Inheritance**: Functionality is built using decorators.
- **Immutability**: Responses are defined through nested objects.

## Quick Start

Create an `index.php` and use decorators to compose your response:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponses\ResponseBody;
use PhpResponses\ResponseHeader;
use PhpResponses\ResponseStatusLineOk;
use PhpResponses\WireMedia;

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody("<h1>Hello from PhpResponses!</h1>"),
        "Content-Type", "text/html"
    )
))->media(new WireMedia());
```

Now just open this file in your browser via a web server (like `php -S localhost:8000`).
