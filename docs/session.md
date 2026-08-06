# Session State Management

This document covers session state management in **PhpResponse** following **Elegant Objects** principles (immutable objects, zero `null` return values, zero procedural `has()` permission checks, and decorator composition via `JoinedSession`).

---

## 1. Core Abstractions

Session management in `PhpResponse` relies on four key classes and interfaces:

1. **[`Session`](../src/Session/Session.php)**: The core session contract providing `value(Text|string $key, Text $default): Text` and `with(Text|string $key, Text $value): Session`.
2. **[`ArraySession`](../src/Session/ArraySession.php)**: Immutable in-memory session implementation.
3. **[`EncryptedCookieSession`](../src/Session/EncryptedCookieSession.php)**: Tamper-proof encrypted cookie session state representation.
4. **[`JoinedSession`](../src/Session/JoinedSession.php)**: Composite session decorator for bulk overriding values without method chaining.

---

## 2. Basic Session Usage

### Initializing and Reading Session Values
In `PhpResponse`, session values are read using `value(key, default)`. If the key is missing, the default [`Text`](../src/Text.php) object is returned. There are zero `null` values or procedural `has()` checks:

```php
use PhpResponse\Session\ArraySession;
use PhpResponse\Text\LiteralText;

// Initialize session with initial data known at request time
$session = new ArraySession([
    'user_id' => new LiteralText('42'),
    'role'    => new LiteralText('admin')
]);

$fallback = new LiteralText('guest');

// Reads 'user_id' -> returns LiteralText('42')
$userId = $session->value('user_id', $fallback);
echo $userId->string(); // "42"

// Reads missing key -> returns fallback LiteralText('guest')
$theme = $session->value('theme', new LiteralText('light'));
echo $theme->string(); // "light"
```

---

## 3. Spawning Updated Sessions (`with()`)

Sessions are **100% immutable**. Calling `with()` spawns a brand-new instance of `Session` containing the updated value without mutating the original object:

```php
$initialSession = new ArraySession();

// Spawns a new Session instance with 'role' set to 'admin'
$updatedSession = $initialSession->with('role', new LiteralText('admin'));

// $initialSession remains unchanged
```

---

## 4. Bulk Overriding via `JoinedSession` (Avoiding Method Chains)

To avoid method chaining (`->with()->with()->with()`), compose multiple sessions using the **[`JoinedSession`](../src/Session/JoinedSession.php)** decorator:

```php
use PhpResponse\Session\JoinedSession;
use PhpResponse\Session\ArraySession;
use PhpResponse\Text\LiteralText;

// Combine new session entries with an existing session in 1 single object instantiation
$session = new JoinedSession(
    new ArraySession([
        'user_id' => new LiteralText('42'),
        'role'    => new LiteralText('admin'),
        'theme'   => new LiteralText('dark')
    ]),
    $existingSession
);
```

When querying `$session->value($key, $default)`, `JoinedSession` checks the primary session first, falling back to the secondary session if missing.

---

## 5. Encrypted Cookie Sessions

For production environments, session state can be stored in encrypted, tamper-proof client cookies:

```php
use PhpResponse\Session\EncryptedCookieSession;
use PhpResponse\Text\LiteralText;

$session = new EncryptedCookieSession(
    new LiteralText('app_session'),
    new LiteralText('your-secret-encryption-key'),
    $payloadFromCookie
);
```
