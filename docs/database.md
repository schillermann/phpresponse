# Database

This document explains how to use **PhpResponse**'s database abstractions, query statements, result objects, and transaction decorators following **OOP** principles (SQL-speaking objects, domain encapsulation, zero `void` side-effects, decorator-based transaction boundaries).

---

## Core Framework Contracts

The database module provides decoupled, generic interfaces and implementations for common relational databases:

1. **[`Statement`](../src/Database/Statement.php)**: Encapsulates SQL queries and bound parameters.
2. **[`Outcome`](../src/Database/Outcome.php)**: Object representing execution results (`size(): int`, `text(): Text`).
3. **[`AffectedRows`](../src/Database/AffectedRows.php)**: Implementation of `Outcome`.
4. **[`Database`](../src/Database/Database.php)**: Database contract ([`PdoDatabase`](../src/Database/PdoDatabase.php), [`Fake`](../src/Database/Fake.php)).
5. **[`Transaction`](../src/Database/Transaction.php)**: Database-agnostic transaction control contract with dialect implementations:
   - **[`SqlTransaction`](../src/Database/Transaction/SqlTransaction.php)**: Standard ANSI SQL (`START TRANSACTION`, `COMMIT`, `ROLLBACK`) for MySQL, PostgreSQL, MariaDB, CockroachDB.
   - **[`SqliteTransaction`](../src/Database/Transaction/SqliteTransaction.php)**: SQLite transaction control (`BEGIN TRANSACTION`, `COMMIT`, `ROLLBACK`).
   - **[`PdoTransaction`](../src/Database/Transaction/PdoTransaction.php)**: Driver-level transaction management using native PDO methods.
6. **[`TransactionalResponse`](../src/Response/TransactionalResponse.php)**: HTTP response decorator managing transactions at the HTTP request boundary.

---

## Low-Level Database Usage

### Creating SQL Statements
To encapsulate an SQL query and its parameters, instantiate `SqlQuery`:

```php
use PhpResponse\Database\SqlQuery;

// Parameterless statement
$allUsersQuery = new SqlQuery("SELECT * FROM users");

// Statement with bound parameter array
$userByIdQuery = new SqlQuery(
    "SELECT id, name, email FROM users WHERE id = :id",
    ['id' => 42]
);
```

### Executing Statements and Retrieving Results

```php
use PhpResponse\Database\Fake;
use PhpResponse\Database\SqlQuery;
use PhpResponse\Database\Outcome;

$db = new Fake();

// outcome() returns an Outcome object (AffectedRows)
$outcome = $db->outcome(
    new SqlQuery("INSERT INTO users (name, email) VALUES (:name, :email)", [
        'name' => 'Alice',
        'email' => 'alice@example.com'
    ])
);

echo $outcome->size(); // Returns affected row count noun (e.g. 1)

// rows() returns a Text object representing the dataset (e.g. JSON string)
$jsonOutput = $db->rows(new SqlQuery("SELECT id, name FROM users"));
echo $jsonOutput->string();
```

---

## 3. Application-Level Encapsulation (Domain Objects)

In true Object-Oriented Programming, **HTTP Response objects should never write raw SQL queries or table names directly inside their handlers**.

Instead, applications define domain objects (such as `Users`) that speak SQL internally:

```php
use PhpResponse\Database\Database;
use PhpResponse\Database\SqlQuery;
use PhpResponse\Database\QueryResult;
use PhpResponse\Text;

// Application domain contract
interface Users
{
    public function add(Text $name, Text $email): QueryResult;
}

// SQL-speaking implementation of the domain contract
final class SqlUsers implements Users
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function add(Text $name, Text $email): Outcome
    {
        return $this->db->outcome(
            new SqlQuery(
                "INSERT INTO users (name, email) VALUES (:name, :email)",
                [
                    'name' => $name->string(),
                    'email' => $email->string(),
                ]
            )
        );
    }
}
```

---

## 4. Full HTTP Request-Response Example

Here is how an application composes the domain object (`SqlUsers`), HTTP response, transaction decorator (`TransactionalResponse`), and resilience decorator (`FailsafeResponse`):

```php
use PhpResponse\Database\Fake;
use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Response\Body;
use PhpResponse\Response\Header\Header;
use PhpResponse\Response\StatusLine\Created;
use PhpResponse\Response\TransactionalResponse;
use PhpResponse\Response\FailsafeResponse;
use PhpResponse\Log\ConsoleLog;
use PhpResponse\Text\LiteralText;

// 1. Instantiate infrastructure
$db = new Fake();
$log = new ConsoleLog();

// 2. Instantiate application domain object
$users = new SqlUsers($db);

// 3. Define HTTP Response that talks to domain objects (No SQL inside HTTP handlers!)
$userResponse = new class($users) implements Response {
    private Users $users;

    public function __construct(Users $users) {
        $this->users = $users;
    }

    public function media(Media $media): Media {
        // Delegate to domain object
        $this->users->add(
            new LiteralText("Bob"),
            new LiteralText("bob@example.com")
        );

        return (new Created(
            new Header(
                new Body(new LiteralText('{"status":"user created"}')),
                'Content-Type',
                'application/json'
            )
        ))->media($media);
    }
};

// 4. Compose decoratively: Failsafe -> Transactional -> Handler
$app = new FailsafeResponse(
    new TransactionalResponse($userResponse, $db),
    $log
);

// 5. Emit response
// The transaction automatically starts, commits (or rolls back on error), and outputs cleanly!
```
