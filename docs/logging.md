# Logging

This section covers the logging capabilities of `PhpResponse`, using elegant decorator patterns rather than global variables or injection of logging services into business classes.

---

We do not inject a `Log` dependency directly into our business classes (such as `UserRegistration`), as this violates the Single Responsibility Principle.
Instead, we keep our business logic pure and add logging using the **Decorator Pattern**.

## Registration Example

First, define the core domain contract:

```php
interface Registration
{
    public function register(string $username): void;
}
```

Implement the pure business logic:

```php
final class UserRegistration implements Registration
{
    public function register(string $username): void
    {
        // Core business logic only (no logging)
    }
}
```

Decorate the business logic with logging behavior:

```php
use PhpResponse\Log\Log;
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\Level\InfoLevel;

final class LoggedRegistration implements Registration
{
    private Registration $origin;
    private Log $log;

    public function __construct(Registration $origin, Log $log)
    {
        $this->origin = $origin;
        $this->log = $log;
    }

    public function register(string $username): void
    {
        $this->origin->register($username);
        $this->log->write(
            new PlainEntry(
                new InfoLevel(),
                new Formatted(
                    new Literal("User registered: %s"),
                    new Literal($username)
                )
            )
        );
    }
}
```

At runtime, compose the object graph:

```php
use PhpResponse\Log\FileLog;
use PhpResponse\Text\ProjectPath;

$registration = new LoggedRegistration(
    new UserRegistration(),
    new FileLog(new ProjectPath('app.log'))
);

$registration->register("john_doe");
```

### Extended Registration Example With Routing

First, define the response representing the action of a successful user registration:

```php
use PhpResponse\Response\Response;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Header;
use PhpResponse\Response\Body as ResponseBody;
use PhpResponse\Response\Media\Media;
use PhpResponse\Text;
use PhpResponse\Text\Literal;
use PhpResponse\Text\Formatted;

final class RegistrationResponse implements Response
{
    private Registration $registration;
    private Text $username;

    public function __construct(Registration $registration, Text $username)
    {
        $this->registration = $registration;
        $this->username = $username;
    }

    public function media(Media $media): Media
    {
        // Execute the registration action (side-effect)
        $this->registration->register($this->username->string());

        // Construct the success response
        return (new Ok(
            new Header(
                new ResponseBody(
                    new Formatted(
                        new Literal("User '%s' registered successfully!"),
                        $this->username
                    )
                ),
                "Content-Type", "text/plain"
            )
        ))->media($media);
    }
}
```

Now, wire the route to this response:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Response\Header;
use PhpResponse\Response\Body as ResponseBody;
use PhpResponse\Response\Media\Wire;
use PhpResponse\Text\Literal;
use PhpResponse\Route\Method;
use PhpResponse\Route\ExactPath;
use PhpResponse\Request\Body as RequestBody;
use PhpResponse\Text\Json\StringValue;
use PhpResponse\Log\FileLog;
use PhpResponse\Text\ProjectPath;

// 1. Instantiate the logging-decorated registration service
$registration = new LoggedRegistration(
    new UserRegistration(),
    new FileLog(new ProjectPath('app.log'))
);

// 2. Define fallback response
$notFound = new Header(
    new ResponseBody(new Literal("Not Found")),
    "Content-Type", "text/plain"
);

// 3. Match a POST request to '/register', extract the username parameter from JSON body,
// and trigger the registration response
(new Method("POST",
    new ExactPath("/register",
        new RegistrationResponse(
            $registration,
            new StringValue(new RequestBody(), "username")
        ),
        $notFound
    ),
    $notFound
))->media(new Wire());
```

---

## Log With Timestamp

```php
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\TimestampedEntry;
use PhpResponse\Log\UtcEpoch;
use PhpResponse\Log\Level\InfoLevel;
use PhpResponse\Text\Literal;

$timestamped = new TimestampedEntry(
    new PlainEntry(new InfoLevel(), new Literal('Action triggered')),
    new UtcEpoch()
);
```

---

## Log With JSON Format

Serialize log entry metadata (level and message) into a JSON string:

```php
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\JsonEntry;
use PhpResponse\Log\Level\ErrorLevel;
use PhpResponse\Text\Literal;

$jsonEntry = new JsonEntry(
    new PlainEntry(new ErrorLevel(), new Literal('Database down'))
);
```

---

## Log Exception Entries

Serialize PHP `Throwable` exceptions into autonomous [`LogEntry`](../src/Log/LogEntry.php) objects ([`ExceptionEntry`](../src/Log/ExceptionEntry.php)):

```php
use PhpResponse\Log\ExceptionEntry;
use PhpResponse\Log\FileLog;
use PhpResponse\Text\ProjectPath;

try {
    // Risky operation
} catch (\Throwable $ex) {
    $log = new FileLog(new ProjectPath('errors.log'));
    $log->write(new ExceptionEntry($ex));
}
```

---

## In-Memory Log Buffers (`LogBuffer` & `MemoryBuffer`)

For testing or in-memory log aggregation ([`LogBuffer`](../src/Log/LogBuffer.php) and [`MemoryBuffer`](../src/Log/MemoryBuffer.php)):

```php
use PhpResponse\Log\MemoryBuffer;
use PhpResponse\Log\BufferLog;
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\Level\InfoLevel;
use PhpResponse\Text\Literal;

$buffer = new MemoryBuffer();
$log = new BufferLog($buffer);

$log->write(new PlainEntry(new InfoLevel(), new Literal('Action completed')));

// Inspect stored log string entries
foreach ($buffer->entries() as $entryString) {
    echo $entryString;
}
```

---

## Log Targets

* `StreamLog`: Writes entries to standard resources (e.g., standard output, files).
* `ConsoleLog`: Writes entries directly to standard output (`php://stdout`).
* `FileLog`: Appends entries directly to a specified file path.
* `BufferLog`: Appends entries to an in-memory `LogBuffer` (`MemoryBuffer`) without exposing getters.
* `TeeLog`: Replicates entries to multiple log targets simultaneously.
* `LevelLog`: Filters entries, only writing those matching allowed levels.
* `FailsafeLog`: Catches and logs internal exceptions silently, preventing logging errors from crashing the application.

```php
use PhpResponse\Log\ConsoleLog;
use PhpResponse\Log\FileLog;
use PhpResponse\Log\TeeLog;
use PhpResponse\Log\LevelLog;
use PhpResponse\Log\FailsafeLog;
use PhpResponse\Log\PlainEntry;
use PhpResponse\Log\Level\ErrorLevel;
use PhpResponse\Log\Level\WarningLevel;
use PhpResponse\Text\ProjectPath;
use PhpResponse\Text\Literal;

// Setup a failsafe, level-filtered log writing to both console and file
$log = new FailsafeLog(
    new LevelLog(
        new TeeLog(
            new ConsoleLog(),
            new FileLog(new ProjectPath('app.log'))
        ),
        new ErrorLevel(),
        new WarningLevel()
    )
);

// This entry will be written to stdout and app.log
$log->write(new PlainEntry(new ErrorLevel(), new Literal('Disk almost full')));
```
