# AirAsia Rewards | Gift Card Management

A PHP application for listing, viewing, adding, updating, and deleting gift cards. It uses PDO to store gift cards in SQLite. The database includes five sample accounts and twenty sample gift cards.

## Run in GitHub Codespaces

From the project folder, start the PHP server.

```bash
php -S 0.0.0.0:8000
```

Open forwarded port 8000 and visit `/card-list.php`. The included database is `database/rewards.db`.

## Build a fresh database

Run these commands against a new database file if you need to recreate the sample data.

```bash
sqlite3 -bail database/rewards.db < database/schema.sql &&
sqlite3 -bail database/rewards.db < database/seed.sql
```

## Use Case Traceability

| Use case | PHP page or pages | Database table | Test evidence |
| --- | --- | --- | --- |
| View a List of Gift Cards | `card-list.php` | `GIFTCARD` | T02 confirmed that the list displayed images, names, and points, and that the card name linked to its details. |
| View Details of One Gift Card | `card-details.php` | `GIFTCARD` | T03 confirmed that the selected card’s image and details appeared, and T08 checked malformed and missing IDs. |
| Add a Gift Card | `card-add.php`, `card-list.php` | `GIFTCARD` | T04 confirmed that QA Test Card appeared in the list after submission. |
| Update a Gift Card | `card-update.php`, `card-details.php` | `GIFTCARD` | T05 confirmed that QA Test Card’s detail page displayed 3,000 points after saving. |
| Delete a Gift Card | `card-delete.php`, `card-list.php` | `GIFTCARD` | T06 confirmed that QA Test Card was absent from the list after confirming deletion. |

## Validation and testing

Run `php tests/validation-tests.php` to check the shared validation functions. See [the test report](test-report.md) for the browser workflow results and the direct POST test for an altered card type.
