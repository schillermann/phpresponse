# JSON Handling (Parsing & Building)

This section covers how to work with JSON payloads in `PhpResponse`—both parsing incoming JSON request data and building structured JSON responses.

---

## 1. Building JSON Responses (Writing)

To output JSON data from your application, use the specialized JSON builders under the `PhpResponse\Text\Json` namespace. These builders let you compose JSON structures declaratively using objects instead of dealing with raw associative arrays or manually concatenated strings.

### Key Components

* **[ObjectValue](../src/Text/Json/ObjectValue.php)**: Represents a JSON object (`{...}`) composed of `Member` instances.
* **[ArrayValue](../src/Text/Json/ArrayValue.php)**: Represents a JSON array (`[...]`) of values.
* **[Member](../src/Text/Json/Member.php)**: Represents a key-value member (`"key":value`) in an object.
* **[FromText](../src/Text/Json/FromText.php)**: A decorator that escapes and quotes any generic text value to comply with the JSON string literal specification.
  * *Note: Any raw PHP string passed directly to `Member` or `ArrayValue` is automatically wrapped in `FromText` under the hood.*
* **[NumberValue](../src/Text/Json/NumberValue.php)**: Represents unquoted integer or float values (e.g. `30` or `9.99`).
* **[Literal](../src/Text/Literal.php) (or any generic `Text` object)**: Since the builders accept `Json|Text|string`, passing a `Text` object directly will output its string raw and untouched. Use this to embed pre-serialized JSON blocks.
* **[TrueValue](../src/Text/Json/TrueValue.php) & [FalseValue](../src/Text/Json/FalseValue.php)**: Zero-state decorators representing boolean constant literals.

### Example: Composing an Outgoing JSON Response

```php
<?php

use PhpResponse\Response\Body;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Json;
use PhpResponse\Text\Json\ObjectValue;
use PhpResponse\Text\Json\Member;
use PhpResponse\Text\Json\ArrayValue;
use PhpResponse\Text\Json\NumberValue;
use PhpResponse\Text\Json\TrueValue;
use PhpResponse\Text\Literal;

$response = new Json(
    new Ok(
        new Body(
            new ObjectValue(
                new Member("name", "Alice"),                     // String: "Alice"
                new Member("age", new NumberValue(30)),           // Number: 30
                new Member("active", new TrueValue()),            // Boolean: true
                new Member("tags", new ArrayValue("dev", "oop")),  // Array: ["dev", "oop"]
                new Member("settings", new Literal('{"theme":"dark"}')) // Pre-serialized JSON raw
            )
        )
    )
);
```

---

## 2. Parsing JSON Requests (Reading)

To extract and parse JSON data sent by clients in incoming request payloads, wrap the request body using the decorators under the `PhpResponse` namespace:

### Key Components

* **[SubTree](../src/Text/Json/SubTree.php)**: Extracts a specific nested JSON node/subtree by key and returns it as a JSON-encoded string.
* **[StringValue](../src/Text/Json/StringValue.php)**: Extracts a string field by key from a JSON string. Throws `DomainException` if the value is missing or not a string.
* **[JsonInt](../src/JsonInt.php)**: Extracts an integer field by key from a JSON string, implementing the [Number](../src/Number.php) interface. Throws `DomainException` if the value is missing or not an integer.

### Example: Reading an Incoming JSON Request

Given the incoming JSON request:
```json
{
  "user": {
    "name": "Alice",
    "age": 30
  }
}
```

You can extract the properties declaratively as follows:

```php
<?php

use PhpResponse\Request\Body;
use PhpResponse\Text\Json\SubTree;
use PhpResponse\Text\Json\StringValue;
use PhpResponse\JsonInt;

// 1. Extract the nested "user" sub-object
$userSubTree = new SubTree(new Body(), 'user');

// 2. Extract string and integer fields from the user sub-object
$name = new StringValue($userSubTree, 'name');
$age = new JsonInt($userSubTree, 'age'); // Implements Number

echo $name->string(); // Outputs: Alice
echo $age->int();     // Outputs: 30
```
