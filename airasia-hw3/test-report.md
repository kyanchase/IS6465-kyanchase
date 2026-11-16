# AirAsia Rewards | HW3 Test Report

Tests run in GitHub Codespaces against the SQLite database `database/rewards.db`. Before browser testing, SQLite reported **5 accounts** and **20 gift cards**, and PHP read all 20 gift cards through PDO.

## Test results

| ID | Test | Steps and input | Expected result | Actual result | Status |
| --- | --- | --- | --- | --- | --- |
| T01 | Validation test harness | Run `tests/validation-tests.php` with valid data, a blank name, an altered type, a nonpositive value, and invalid points. | All five checks report PASS. | All five checks reported PASS. See the terminal output below. | Pass |
| T02 | View Gift Card List | Open `card-list.php` and click a card name. | The list displays images, names, and points. The selected name links to the detail page with the card ID. | The list displayed images, names, and points. Clicking a name opened the detail URL with `card_id` in the query string. | Pass |
| T03 | View Gift Card Details | Open a seeded card from the list. | The selected card's image and details appear. | The selected card's image and details appeared. | Pass |
| T04 | Add Gift Card | Submit a temporary card named QA Test Card with type Travel, value 25, and 2500 points. | The list opens and shows the new card. | After submission, QA Test Card appeared in the gift card list. | Pass |
| T05 | Update Gift Card | Open QA Test Card, change its points to 3000, and save. | The detail page shows 3000 points for the same card ID. | After saving, QA Test Card’s detail page displayed 3000 points. | Pass |
| T06 | Delete Gift Card | Open the delete page for QA Test Card, confirm the card name, and submit the confirmation. | The page asks for confirmation before removing the card. After confirmation, the list opens and the card is gone. | After submitting the deletion, QA Test Card was gone from the gift card list. | Pass |
| T07 | Invalid or altered input handling | Sent a direct POST to `card-add.php` for QA Invalid Card with `cardType=Unknown`, value 25, and 2500 points. Queried SQLite for that card name. | The server rejects the altered type and saves no card. | HTTP 422 displayed “Choose Dining, Retail, or Travel.” SQLite returned 0 matching rows. | Pass |
| T08 | Invalid Gift Card identifier handling | Open `card-details.php?card_id=abc` and `card-details.php?card_id=999999`. | Malformed IDs receive a validation message. A valid ID with no matching record receives a not-found message. | `abc` displayed “Enter a valid gift card ID.” `999999` displayed “Gift card not found.” | Pass |

## T01 validation harness output

Command run in Codespaces

```bash
XDEBUG_MODE=off php tests/validation-tests.php
```

```text
PASS — Valid gift card
PASS — Blank card name
PASS — Altered card type
PASS — Zero card value
PASS — Fractional points
```
