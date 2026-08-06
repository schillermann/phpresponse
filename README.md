# PhpResponse

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

use PhpResponse\Response\Body;
use PhpResponse\Response\Header;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Media\Wire;
use PhpResponse\Text\LiteralText;

(new Ok(
    new Header(
        new Body(new LiteralText("<h1>Hello from PhpResponse!</h1>")),
        "Content-Type", "text/html"
    )
))->media(new Wire());
```

---

## Documentation

Detailed topics are split into separate files for easy overview:

- **[Authentication](docs/authentication.md)**: Covers authentication checkpoints (`Checkpoint`) and auth strategies (`BearerToken`, `ApiKey`, `Fake`).
- **[Caching](docs/caching.md)**: Covers caching results for performance using the `StickyText` decorator.
- **[Database](docs/database.md)**: Covers database integration (`Database`, `Statement`, `Outcome`, `PdoDatabase`, `Fake`) and transaction boundary decorators (`TransactionalResponse`, `SqlTransaction`, `SqliteTransaction`, `PdoTransaction`).
- **[Headers](docs/headers.md)**: Covers HTTP status codes, response headers, redirects, and specialized content-type headers.
- **[JSON](docs/json.md)**: Covers parsing incoming JSON requests and declaratively building JSON responses.
- **[Logging](docs/logging.md)**: Covers SRP-respecting logger decoration, timestamp/JSON logging entry options, and targets like `TeeLog`, `FileLog`, `LevelLog`, and `MemoryBuffer`.
- **[Request](docs/requests.md)**: Covers declarative request parsing (header, method, path, body, protocol, query/form parameters, cookies, file uploads).
- **[Routing](docs/routing.md)**: Explains declarative route matching using decorators like `Method`, `ExactPath`, and `RegexPath`.
- **[Session](docs/session.md)**: Covers immutable session state management (`Session`, `ArraySession`, `EncryptedCookieSession`) and composite session decoration (`JoinedSession`).
- **[Templating](docs/templating.md)**: Covers rendering view templates using variables, external files, and OOP view encapsulation.
