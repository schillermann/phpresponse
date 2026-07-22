# Authentication & Checkpoints

This section covers how authentication checkpoints and authorization strategies are composed using **Response Decorators** in `PhpResponse`.

---

In `PhpResponse`, authentication is not handled by global middleware or procedural session state. Instead, authentication checks are composed declaratively through immutable **Authentication Checkpoints** (`Checkpoint` under `PhpResponse\Auth`) and **Authentication Strategies** (`PhpResponse\Auth\Header`).

## Authentication Checkpoint (`Checkpoint`)

`Checkpoint` decorates a target response with an `Authentication` strategy. If the authentication check succeeds (`$auth->valid() === true`), execution proceeds to the target response; otherwise, it falls back to an unauthorized or error response.

```php
use PhpResponse\Auth\Checkpoint;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\StatusLine\Unauthorized;
use PhpResponse\Response\Body;
use PhpResponse\Auth\Header\BearerToken;

$protectedRoute = new Checkpoint(
    new Ok(new Body("Secret dashboard data")),
    new Unauthorized(new Body("Invalid or missing Bearer token")),
    new BearerToken("secret-app-token")
);
```

---

## Built-in Authentication Strategies (`PhpResponse\Auth\Header`)

`PhpResponse` provides several immutable `Authentication` implementations:

### `BearerToken`
Validates the `Authorization` header against an expected bearer token string or `Text` object.

```php
use PhpResponse\Auth\Header\BearerToken;

$auth = new BearerToken("my-secret-token");
```

### `ApiKey`
Validates that an HTTP header matches an expected API key value.

```php
use PhpResponse\Auth\Header\ApiKey;

$auth = new ApiKey("my-api-key", "X-Api-Key");
```

### `Fake`
Fake authentication object for testing or mocking authentication results.

```php
use PhpResponse\Auth\Fake;

$allowAll = new Fake(true);  // Always returns true
$denyAll = new Fake(false); // Always returns false
```

---

## Combining Authentication with Headers & Routing

Example of wrapping a protected route inside CORS and path matching:

```php
use PhpResponse\Response\Header\Cors;
use PhpResponse\Auth\Checkpoint;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\StatusLine\Unauthorized;
use PhpResponse\Response\StatusLine\NotFound;
use PhpResponse\Response\Body;
use PhpResponse\Auth\Header\BearerToken;
use PhpResponse\Route\ExactPath;

$notFound = new NotFound(new Body("Not Found"));
$unauthorized = new Unauthorized(new Body("Unauthorized"));

$app = new Cors(
    new ExactPath(
        "/api/protected",
        new Checkpoint(
            new Ok(new Body('{"data":"confidential"}')),
            $unauthorized,
            new BearerToken("secure-token-123")
        ),
        $notFound
    )
);
```
