# AirAsia Rewards | Gift Card Management

A PHP and SQLite classroom app for gift-card management and rewards redemption. The supplied database includes five users, five accounts, and twenty gift cards.

## Run in GitHub Codespaces

From the project folder, start the PHP development server with the included router. The router blocks browser downloads of the database and its SQL files.

```bash
php -S 0.0.0.0:8000 router.php
```

Open forwarded port 8000 and visit `/login.php`. The included database is `database/rewards.db`. Use PHP 8 or later with PDO SQLite.

| Grading role | Username | Test password |
| --- | --- | --- |
| Admin | `bsmith` | `mysecret` |
| Customer | `pjones` | `acrobat` |

Both roles start on the gift-card list. The requirements document lists both roles for redemption, so both may redeem against their own account. Only Customers have the account profile page. A card with redemption history is retained to preserve the required relationship.

## Build a fresh database

Run these commands against a new database file if you need to recreate the sample data.

```bash
sqlite3 -bail database/rewards-fresh.db \
  '.read database/schema.sql' '.read database/seed.sql' 'PRAGMA foreign_key_check;'
```

## Use Case Traceability

| Use case | PHP page or pages | Database table | Test evidence |
| --- | --- | --- | --- |
| View a List of Gift Cards | `card-list.php` | `GIFTCARD` | T02 confirmed that the list displayed images, names, and points, and that the card name linked to its details. |
| View Details of One Gift Card | `card-details.php` | `GIFTCARD` | T03 confirmed that the selected card’s image and details appeared, and T08 checked malformed and missing IDs. |
| Add a Gift Card | `card-add.php`, `card-list.php` | `GIFTCARD` | T04 confirmed that QA Test Card appeared in the list after submission. |
| Update a Gift Card | `card-update.php`, `card-details.php` | `GIFTCARD` | T05 confirmed that QA Test Card’s detail page displayed 3,000 points after saving. |
| Delete a Gift Card | `card-delete.php`, `card-list.php` | `GIFTCARD` | T06 confirmed that QA Test Card was absent from the list after confirming deletion. |

| Login | `login.php`, `authenticate.php` | `USER` | T09–T11 |
| Log out of the system | `logout.php` | Session only | T17 |
| Add a customer | `cust-add.php` | `USER`, `ACCOUNT` | T14 |
| View customer profile and points | `customer-profile.php` | `USER`, `ACCOUNT` | T18 |
| Redeem gift cards | `redeem.php`, `process-redemption.php` | `ACCOUNT`, `GIFTCARD`, `REDEMPTION` | T15–T16 |

## Validation and testing

Run `php tests/validation-tests.php` to check the shared validation functions. See [the test report](test-report.md) for the original HW3 results and the HW4 Codespaces test checklist.
