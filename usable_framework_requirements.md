# Usable Web Framework in Pure OOP

To make **PhpResponse** a production-ready, general-purpose web framework, it needs to handle real-world concerns (persistence, security, complex routing, cookie/session state, uploaded files, and robust error handling) without compromising on the strict principles of **Elegant Objects** (no nulls, no getters/setters, immutability, small classes, and composition via decorators).

Below is a detailed analysis of what is missing and how we can design these additions.

---

## 1. Advanced Routing & Parameter Extraction (Done)

Currently, the framework supports simple path matching (`ExactPath`, `RegexPath`, `Method`).
For real-world use, we need dynamic parameter extraction without mutable context bags.

### Path Parameter Extraction
Rather than passing route arguments inside a request dictionary or global context, parameters can be extracted directly from the request uri by matching regex capturing groups.

```php
// An object representing a parameter extracted from the URI path
final class PathParam implements Text
{
    private string $name;
    private Text $path;

    public function __construct(string $name)
    {
        $this->name = $name;
        // Defaults to reading from the standard Path
        $this->path = new Path(); 
    }

    public function string(): string
    {
        // Extracts the parameter by parsing $_SERVER['REQUEST_URI']
        // using the active route registration configuration
    }
}
```

### Route Groups
Grouping routes with common prefixes (e.g. `/api/v1`) or applying common middleware decorators to them:

```php
$apiRoutes = new PrefixPath("/api/v1",
    new Method("GET",
        new ExactPath("/users", $listUsersResponse, $notFound),
        $notFound
    )
);
```

---

## 2. Robust Input Parsing (Done)

For a complete web framework, parsing various client input structures (Forms, File Uploads, Cookies) is required.

### Form & POST Parameters
Currently, query parameters and JSON properties are covered. We need support for form submissions:

```php
final class FormParam implements Text
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function string(): string
    {
        if (!isset($_POST[$this->name])) {
            throw new \OutOfBoundsException("Form parameter '{$this->name}' is missing.");
        }
        return (string) $_POST[$this->name];
    }
}
```

### Cookie Parsing
Retrieving state stored on the client side:

```php
final class Cookie implements Text
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function string(): string
    {
        if (!isset($_COOKIE[$this->name])) {
            throw new \OutOfBoundsException("Cookie '{$this->name}' is missing.");
        }
        return $_COOKIE[$this->name];
    }
}
```

### Multipart/File Uploads
Handling files without exposing temporary paths or file details as data containers. Instead, the uploaded file is an object you instruct to save:

```php
interface File
{
    public function saveTo(Text $destination): void;
}

final class RequestFile implements File
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function saveTo(Text $destination): void
    {
        if (!isset($_FILES[$this->key])) {
            throw new \RuntimeException("File '{$this->key}' was not uploaded.");
        }
        // Validate upload and move it safely
        move_uploaded_file($_FILES[$this->key]['tmp_name'], $destination->string());
    }
}
```

---

## 3. Standard HTTP Responses & Status Lines

A fully-featured framework requires native representations for a wide variety of HTTP statuses and control flows like redirection.

### HTTP Status Objects (Done)
Common HTTP status code response decorators:
* [x] `Created` (201)
* [x] `NoContent` (204)
* [x] `BadRequest` (400)
* [x] `Unauthorized` (401)
* [x] `Forbidden` (403)
* [x] `NotFound` (404)
* [x] `MethodNotAllowed` (405)
* [x] `InternalServerError` (500)

### Redirection (Done)
Redirects are response decorators that set the `Location` header and status line:

```php
final class Redirect implements Response
{
    private Text $target;
    private int $code;

    public function __construct(Text $target, int $code = 302)
    {
        $this->target = $target;
        $this->code = $code;
    }

    public function media(Media $media): Media
    {
        return $media
            ->status($this->code, "Found")
            ->header("Location", $this->target->string());
    }
}
```

### JSON Body Formatting (Done - implemented via Json and JSON builders under src/Text/Json)
A specialized body wrapper that encodes structural objects (which implement serialization to json) and sets the `Content-Type: application/json` header:

```php
final class JsonResponse implements Response
{
    private Response $origin;

    public function __construct(Response $origin)
    {
        $this->origin = $origin;
    }

    public function media(Media $media): Media
    {
        return $this->origin->media(
            new HeaderMedia($media, "Content-Type", "application/json")
        );
    }
}
```

---

## 4. Middleware & Request-Response Pipeline (Done)

In Elegant Objects, "middleware" does not exist as a procedural layer. Instead, it is naturally composed using nested **Decorators**.

### CORS Headers
```php
final class CorsResponse implements Response
{
    private Response $origin;
    private Text $originHeader;

    public function __construct(Response $origin, Text $originHeader)
    {
        $this->origin = $origin;
        $this->originHeader = $originHeader;
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media)
            ->header("Access-Control-Allow-Origin", $this->originHeader->string())
            ->header("Access-Control-Allow-Headers", "Content-Type, Authorization");
    }
}
```

### Authentication Guards
A decorator that verifies user authentication before proceeding to the actual target response, returning an unauthorized response if credentials fail:

```php
final class GuardedResponse implements Response
{
    private Response $target;
    private Response $fallback;
    private Authentication $auth;

    public function __construct(Response $target, Response $fallback, Authentication $auth)
    {
        $this->target = $target;
        $this->fallback = $fallback;
        $this->auth = $auth;
    }

    public function media(Media $media): Media
    {
        if ($this->auth->valid()) {
            return $this->target->media($media);
        }
        return $this->fallback->media($media);
    }
}
```

---

## 5. Session State Management (Done)

HTTP is stateless, but web apps need session state. In pure OOP, a session is a state-saving object, not a mutable global dictionary.

```php
interface Session
{
    public function value(string $key): Text;
    public function with(string $key, Text $value): Session;
    public function has(string $key): bool;
}

final class ArraySession implements Session
{
    // Immutable in-memory session implementation
}

final class EncryptedCookieSession implements Session
{
    // Immutable encrypted cookie session implementation
}

final class JoinedSession implements Session
{
    // Composite session decorator for bulk overriding without chaining
}
```

---

## 6. Resilience & Exception Handling (Done)

We need safe boundary points in the tree to catch runtime failures and output a clean error response instead of crashing the PHP process or exposing debug information.

```php
final class FailsafeResponse implements Response
{
    private Response $origin;
    private Log $log;

    public function __construct(Response $origin, Log $log)
    {
        $this->origin = $origin;
        $this->log = $log;
    }

    public function media(Media $media): Media
    {
        try {
            return $this->origin->media($media);
        } catch (\Throwable $ex) {
            $this->log->write(new ExceptionEntry($ex));
            return (new InternalServerError(
                new Header(
                    new Body(new Literal("Something went wrong.")),
                    "Content-Type", "text/plain"
                )
            ))->media($media);
        }
    }
}
```

---

## 7. Dynamic View Rendering (Iterative Rendering) (Done)

To render collections of data (e.g. databases rows or arrays of models) without static loops inside templates, objects must render themselves.

```php
final class Joined implements Text
{
    private Text $separator;

    /** @var array<Text> */
    private array $items;

    public function __construct(Text $separator, Text ...$items)
    {
        $this->separator = $separator;
        $this->items = $items;
    }

    public function string(): string
    {
        return implode(
            $this->separator->string(),
            array_map(fn(Text $t) => $t->string(), $this->items)
        );
    }
}
```

---

## 8. Database Integration (SQL-Speaking Objects) (Done)

Web frameworks need a way to fetch and persist data. ORMs or Query Builders leak arrays/data maps. Instead, we use SQL-speaking objects.

```php
interface Database
{
    public function execute(string $sql, array $params): void;
    public function select(string $sql, array $params): Text; // returns e.g. JSON or XML representation
}

// Transaction Boundary Decorator
final class TransactionalResponse implements Response
{
    private Response $origin;
    private Database $db;

    public function __construct(Response $origin, Database $db)
    {
        $this->origin = $origin;
        $this->db = $db;
    }

    public function media(Media $media): Media
    {
        $this->db->execute("START TRANSACTION", []);
        try {
            $result = $this->origin->media($media);
            $this->db->execute("COMMIT", []);
            return $result;
        } catch (\Throwable $ex) {
            $this->db->execute("ROLLBACK", []);
            throw $ex;
        }
    }
}
```

---

## 9. Security Hardening & CSRF (Passports & Tokens) (Done)

In Elegant Objects, security and authentication are not handled by global middleware pipelines, central auth services, or static security contexts. Instead, identity is encapsulated into stateless **Passports**, and CSRF safety is enforced using **Guarded Decorators**.

### CSRF Protection via Decorators

State-modifying requests (POST, PUT, DELETE) are wrapped in a `GuardedResponse` decorator. The decorator verifies a CSRF token provided in headers or form data against an expected token object (e.g. stored in an encrypted cookie or session token).

```php
interface CsrfToken extends Text
{
    public function valid(Text $candidate): bool;
}

final class GuardedResponse implements Response
{
    private Response $target;
    private Response $fallback;
    private CsrfToken $token;
    private Text $candidate;

    public function __construct(
        Response $target,
        CsrfToken $token,
        Response $fallback,
        Text $candidate
    ) {
        $this->target = $target;
        $this->token = $token;
        $this->fallback = $fallback;
        $this->candidate = $candidate;
    }

    public function media(Media $media): Media
    {
        if ($this->token->valid($this->candidate)) {
            return $this->target->media($media);
        }
        return $this->fallback->media($media);
    }
}
```

### Stateless Passports (`Pass`)

Authentication is represented by `Pass` objects that inspect incoming requests (from headers, cookies, or authorization tokens) and return an `Identity` object without mutable server-side session reliance.

```php
interface Identity
{
    public function name(): Text;
    public function authenticated(): bool;
}

interface Pass
{
    public function identity(): Identity;
}

final class Cookie implements Pass
{
    private Text $cookieName;
    private Text $secretKey;

    public function __construct(Text $cookieName, Text $secretKey)
    {
        $this->cookieName = $cookieName;
        $this->secretKey = $secretKey;
    }

    public function identity(): Identity
    {
        // Decrypts authentication payload from cookie without static session access
    }
}
```

---

## 10. Transparent Caching Decorators

In Elegant Objects, caching is never performed by invoking a central `CacheManager::get($key)` or static cache pool. Caching is achieved by wrapping existing objects with transparent caching decorators.

### Cached Response Decorator

A `CachedResponse` decorates a `Response` and memoizes the resulting `Media` structure in memory or persistent storage (e.g., Redis, disk file) for a specified TTL.

```php
interface CacheStorage
{
    public function save(string $key, Media $media, int $ttlSeconds): void;
    public function has(string $key): bool;
    public function read(string $key): Media;
}

final class CachedResponse implements Response
{
    private Response $origin;
    private CacheStorage $storage;
    private string $cacheKey;
    private int $ttlSeconds;

    public function __construct(
        Response $origin,
        CacheStorage $storage,
        string $cacheKey,
        int $ttlSeconds
    ) {
        $this->origin = $origin;
        $this->storage = $storage;
        $this->cacheKey = $cacheKey;
        $this->ttlSeconds = $ttlSeconds;
    }

    public function media(Media $media): Media
    {
        if ($this->storage->has($this->cacheKey)) {
            return $this->storage->read($this->cacheKey);
        }
        $result = $this->origin->media($media);
        $this->storage->save($this->cacheKey, $result, $this->ttlSeconds);
        return $result;
    }
}
```

### Cached Text Decorator

Similarly, expensive text computations (such as rendering HTML templates or parsing JSON strings) are wrapped in a `CachedText` decorator.

```php
final class CachedText implements Text
{
    private Text $origin;
    private ?string $memo = null;

    public function __construct(Text $origin)
    {
        $this->origin = $origin;
    }

    public function string(): string
    {
        if ($this->memo === null) {
            $this->memo = $this->origin->string();
        }
        return $this->memo;
    }
}
```
