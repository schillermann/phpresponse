# HTTP Headers, Status Lines & Redirects

This section covers how to represent HTTP status lines, response headers, and redirects in `PhpResponse`.

---

## Status Line Decorators

In `PhpResponse`, HTTP status codes are represented as decorators implementing the [Response](../src/Response/Response.php) interface.
They wrap an inner response to inject the HTTP status code and text.

The following status line decorators are available under the `PhpResponse\Response\StatusLine` namespace:

* **[Ok](../src/Response/StatusLine/Ok.php)**: `200 OK`
* **[Created](../src/Response/StatusLine/Created.php)**: `201 Created`
* **[NoContent](../src/Response/StatusLine/NoContent.php)**: `204 No Content`
* **[BadRequest](../src/Response/StatusLine/BadRequest.php)**: `400 Bad Request`
* **[Unauthorized](../src/Response/StatusLine/Unauthorized.php)**: `401 Unauthorized`
* **[Forbidden](../src/Response/StatusLine/Forbidden.php)**: `403 Forbidden`
* **[NotFound](../src/Response/StatusLine/NotFound.php)**: `404 Not Found`
* **[MethodNotAllowed](../src/Response/StatusLine/MethodNotAllowed.php)**: `405 Method Not Allowed`
* **[InternalServerError](../src/Response/StatusLine/InternalServerError.php)**: `500 Internal Server Error`

### Example Usage

```php
<?php

use PhpResponse\Response\Body;
use PhpResponse\Response\StatusLine\NotFound;
use PhpResponse\Text\LiteralText;

$response = new NotFound(
    new Body(new LiteralText("The requested resource was not found on this server."))
);
```

---

## Headers & Specialized Formatting

HTTP headers are applied using response decorators.

* **[Header](../src/Response/Header.php)**: A generic decorator that sets any custom header on the response.
* **[JsonHeader](../src/Response/JsonHeader.php)**: A specialized header decorator that sets the `Content-Type: application/json` header.

### Example Usage

```php
<?php

use PhpResponse\Response\Body;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\JsonHeader;
use PhpResponse\Text\Json\JsonObject;
use PhpResponse\Text\Json\JsonMember;

$response = new JsonHeader(
    new Ok(
        new Body(
            new JsonObject(
                new JsonMember("status", "ok")
            )
        )
    )
);
```

---

## Redirects

Redirect response objects redirect the client to a target URL (represented as a [Text](../src/Text.php) object) with an HTTP status line and `Location` header.

The following autonomous redirect classes are available under the `PhpResponse\Response\Redirect` namespace:

* **[MovedPermanently](../src/Response/Redirect/MovedPermanently.php)**: `301 Moved Permanently`
* **[Found](../src/Response/Redirect/Found.php)**: `302 Found`
* **[SeeOther](../src/Response/Redirect/SeeOther.php)**: `303 See Other`
* **[TemporaryRedirect](../src/Response/Redirect/TemporaryRedirect.php)**: `307 Temporary Redirect`
* **[PermanentRedirect](../src/Response/Redirect/PermanentRedirect.php)**: `308 Permanent Redirect`

### Example Usage

```php
<?php

use PhpResponse\Response\Redirect\Found;
use PhpResponse\Response\Redirect\MovedPermanently;
use PhpResponse\Text\LiteralText;

// Found redirect (302 Found)
$redirect = new Found(new LiteralText("/dashboard"));

// Permanent redirect (301 Moved Permanently)
$permanentRedirect = new MovedPermanently(new LiteralText("/new-location"));
```

---

## JSON Responses

To parse incoming JSON request payloads or build structured JSON responses, see the dedicated **[JSON Handling](json.md)** documentation.
