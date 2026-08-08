# Authentication, Passports & CSRF Protection

This section covers how authentication checkpoints, stateless **Passports**, **Identity** management, and **CSRF Guard Decorators** are composed in `PhpResponse`.

---

In `PhpResponse`, authentication and security are not handled by global middleware or procedural session state. Instead, security checks are composed declaratively through immutable **Response Decorators** (`Checkpoint`, `GuardedResponse`), **Passports** (`Pass`), and **Tokens** (`CsrfToken`).

---

## CSRF Protection (`PhpResponse\Auth\Csrf`)

CSRF protection prevents unauthorized, cross-site state-modifying requests (POST, PUT, DELETE, PATCH).

### `GuardedResponse`
A response decorator that automatically allows safe HTTP methods (`GET`, `HEAD`, `OPTIONS`) to pass through to `$target`, while verifying CSRF tokens for state-modifying requests.

```php
use PhpResponse\Auth\Csrf\GuardedResponse;
use PhpResponse\Auth\Csrf\SameToken;
use PhpResponse\Auth\Csrf\RequestCandidate;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\StatusLine\Forbidden;
use PhpResponse\Response\Body;
use PhpResponse\Text\Literal;

$guardedRoute = new GuardedResponse(
    new Ok(new Body(new Literal("Resource updated successfully."))),
    new SameToken(new Literal("expected-session-csrf-secret")),
    new Forbidden(new Body(new Literal("CSRF verification failed."))),
    new RequestCandidate() // Automatically inspects X-CSRF-Token, X-XSRF-Token, csrf_token, _token
);
```

### CSRF Token Representations
- **`SameToken`**: Validates a candidate against an expected token using constant-time string comparison (`hash_equals`).
- **`RandomToken`**: Cryptographically secure random CSRF token generator.
- **`RequestCandidate`**: Extracts candidate CSRF tokens from incoming HTTP headers or form parameters.
- **`CsrfCandidate`**: Composite `Text` object for custom fallback chains of token candidates.

---

## Stateless Passports & Identity (`PhpResponse\Auth\Pass`)

In pure OOP, identity is encapsulated into stateless **Passport (`Pass`)** objects that inspect incoming requests (from headers, cookies, or tokens) and return an **`Identity`** object without reliance on mutable server session state.

### Identity Contracts
- **`Identity`**: Interface defining `name(): Text` and `authenticated(): bool`.
- **`AnonymousIdentity`**: Null Object implementation returned when no valid credentials are found (`authenticated() === false`).
- **`UserIdentity`**: Authenticated identity wrapper containing user information (`authenticated() === true`).

### Passport Strategies (`Pass`)
- **`Cookie`**: Extracts identity from an HTTP cookie (`new Cookie("ps_identity")`).
- **`Header`**: Extracts identity from a custom HTTP header (`new Header("X-Identity")`).
- **`Bearer`**: Extracts identity from `Authorization: Bearer <token>` headers (`new Bearer()`).
- **`Fake`**: Mock passport for testing (`new Fake(new UserIdentity("alice"))`).

### Combining Passports with Checkpoints
Adapt any `Pass` into an `Authentication` strategy using `IdentityAuthentication`:

```php
use PhpResponse\Auth\Checkpoint;
use PhpResponse\Auth\Pass\Bearer;
use PhpResponse\Auth\Pass\IdentityAuthentication;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\StatusLine\Unauthorized;
use PhpResponse\Response\Body;
use PhpResponse\Text\Literal;

$protectedDashboard = new Checkpoint(
    new Ok(new Body(new Literal("Welcome to the Admin Panel"))),
    new Unauthorized(new Body(new Literal("Please log in"))),
    new IdentityAuthentication(
        new Bearer("Authorization")
    )
);
```

---

## Header-Based Authentication (`PhpResponse\Auth\Header`)

For simple API key or bearer token authentication checks:

### `BearerToken`
```php
use PhpResponse\Auth\Header\BearerToken;

$auth = new BearerToken("my-secret-token");
```

### `ApiKey`
```php
use PhpResponse\Auth\Header\ApiKey;

$auth = new ApiKey("my-api-key", "X-Api-Key");
```

### `Fake`
```php
use PhpResponse\Auth\Fake;

$allowAll = new Fake(true);  // Always passes
$denyAll = new Fake(false); // Always fails
```

---

## Full Example: Combining CSRF, Passports, and Routing

```php
use PhpResponse\Route\ExactPath;
use PhpResponse\Auth\Csrf\GuardedResponse;
use PhpResponse\Auth\Csrf\SameToken;
use PhpResponse\Auth\Pass\Cookie;
use PhpResponse\Auth\Pass\IdentityAuthentication;
use PhpResponse\Auth\Checkpoint;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\StatusLine\Forbidden;
use PhpResponse\Response\StatusLine\Unauthorized;
use PhpResponse\Response\StatusLine\NotFound;
use PhpResponse\Response\Body;
use PhpResponse\Text\Literal;

$app = new ExactPath(
    "/user/settings",
    new Checkpoint(
        new GuardedResponse(
            new Ok(new Body(new Literal("Settings updated."))),
            new SameToken(new Literal("secret-csrf-token")),
            new Forbidden(new Body(new Literal("403 Forbidden")))
        ),
        new Unauthorized(new Body(new Literal("401 Unauthorized"))),
        new IdentityAuthentication(
            new Cookie("auth_user")
        )
    ),
    new NotFound(new Body(new Literal("404 Not Found")))
);
```
