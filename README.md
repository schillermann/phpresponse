# PhpResponses

A simple web framework in PHP that respects OOP.

Inspired by pure OOP, Alan Kay with [Smalltalk](https://en.wikipedia.org/wiki/Smalltalk), and Yegor Bugayenko's [Cactoos](https://github.com/yegor256/cactoos), [Takes](https://github.com/yegor256/takes), and [JPages](https://github.com/yegor256/jpages).

I’ve also created the web framework in other languages that you can check out.
- [Java](https://github.com/schillermann/jresponses)
- [JavaScript](https://github.com/schillermann/jsresponses)

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
use PhpResponses\MediaToWire;

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody("<h1>Hello from PhpResponses!</h1>"),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```

## Request Example

You can also use classes from `PhpResponses\Request` to extract data from the request environment:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponses\Request\BodyFromEnv;
use PhpResponses\Request\HeaderFromEnv;
use PhpResponses\Request\MethodFromEnv;
use PhpResponses\Request\PathFromEnv;
use PhpResponses\ResponseStatusLineOk;
use PhpResponses\ResponseHeader;
use PhpResponses\ResponseBody;
use PhpResponses\MediaToWire;

try {
    $agent = (new HeaderFromEnv("User-Agent"))->string();
} catch (\OutOfBoundsException $e) {
    $agent = "Unknown";
}
$body = (new BodyFromEnv())->string();
$method = (new MethodFromEnv())->string();
$path = (new PathFromEnv())->string();

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody(
            sprintf(
                "<html><body><h1>Your Browser: %s</h1><p>Method: %s</p><p>Path: %s</p><p>Body: %s</p></body></html>",
                $agent,
                $method,
                $path,
                $body
            )
        ),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```

Now just open this file in your browser via a web server (like `php -S localhost:8000`).

## JSON Request Example

You can parse JSON requests declaratively using templates.

### Inline Template Example

If you want to render the response using an in-memory template string:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponses\Request\BodyFromEnv;
use PhpResponses\ResponseStatusLineOk;
use PhpResponses\ResponseHeader;
use PhpResponses\ResponseBody;
use PhpResponses\MediaToWire;
use PhpResponses\JsonSubTree;
use PhpResponses\JsonString;
use PhpResponses\JsonInt;
use PhpResponses\Template;
use PhpResponses\LiteralText;
use PhpResponses\TextOfNumber;

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody(
            new Template(
                new LiteralText("<html><body><h1>Hello, ${name}!</h1><p>You are ${age} years old.</p></body></html>"),
                [
                    "name" => new JsonString(
                        new JsonSubTree(new BodyFromEnv(), 'user'),
                        'name'
                    ),
                    "age" => new TextOfNumber(
                        new JsonInt(
                            new JsonSubTree(new BodyFromEnv(), 'user'),
                            'age'
                        )
                    )
                ]
            )
        ),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```

### External Template Example

If you prefer to separate layout from logic by keeping the HTML structure in an external file (e.g., `template.html`):

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponses\Request\BodyFromEnv;
use PhpResponses\ResponseStatusLineOk;
use PhpResponses\ResponseHeader;
use PhpResponses\ResponseBody;
use PhpResponses\MediaToWire;
use PhpResponses\JsonSubTree;
use PhpResponses\JsonString;
use PhpResponses\JsonInt;
use PhpResponses\Template;
use PhpResponses\TextOfFile;
use PhpResponses\TextOfNumber;

(new ResponseStatusLineOk(
    new ResponseHeader(
        new ResponseBody(
            new Template(
                new TextOfFile("template.html"),
                [
                    "name" => new JsonString(
                        new JsonSubTree(new BodyFromEnv(), 'user'),
                        'name'
                    ),
                    "age" => new TextOfNumber(
                        new JsonInt(
                            new JsonSubTree(new BodyFromEnv(), 'user'),
                            'age'
                        )
                    )
                ]
            )
        ),
        "Content-Type", "text/html"
    )
))->media(new MediaToWire());
```
