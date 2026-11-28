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

## HW4 verification

The report above records the original HW3 run. Before submission, rerun the card CRUD tests with an Admin session and a CSRF token from the current form. Replace each Pending result below with the outcome observed in Codespaces.

| ID | Steps and input | Expected | Actual | Status |
| --- | --- | --- | --- | --- |
| T09 | Signed in as `bsmith` with the assigned Admin password. | The gift-card list opens with Admin management links. | The gift-card list opened and **Add Customer** was visible. | Pass |
| T10 | Signed in as `pjones` and opened My Account. | The Customer can sign in and view their rewards balance. | My Account displayed 8,500 points before redemption. | Pass |
| T11 | Tried signing in as `bsmith` with an incorrect password. | The app displays a generic login error. | The app displayed “Invalid username or password.” | Pass |
| T12 | Opened `card-list.php` while signed out. | The app redirects to login. | The login page opened. | Pass |
| T13 | Signed in as `pjones` and opened an Admin-only page directly. | The server denies access. | The page displayed “You do not have permission to access this page.” | Pass |
| T14 | As Admin, created `hw4test`, then tried to create the same username again. | The app creates one Customer with a hashed password and a zero-point account, and rejects the duplicate. | SQLite showed one Customer account with a bcrypt hash and 0 points. The app displayed “That username is already in use” on the second attempt. | Pass |
| T15 | Signed in as `pjones` and redeemed Airport Cafe $10. | One redemption record is created and the balance drops by 1,000 points. | The app confirmed redemption. SQLite showed one Airport Cafe $10 redemption for 1,000 points and a balance of 7,500. | Pass |
| T16 | With 7,500 points, attempted to redeem Hotel Voucher $100 for 10,000 points. | The app rejects the redemption without changing the balance or adding a record. | The app displayed an insufficient-points message. SQLite still showed a balance of 7,500 and only the Airport Cafe redemption. | Pass |
| T17 | Logged out and reopened `card-list.php`. | The protected page redirects to login. | The app redirected to login. | Pass |
| T18 | Signed in as `pjones` and opened `customer-profile.php?userId=1`. | The profile still shows the signed-in customer’s account. | The page showed P. Jones, username `pjones`, and 7,500 available points. | Pass |
| T19 | Opened `/database/rewards.db` directly through the forwarded app URL. | The server blocks access to the database file. | The app displayed “Not found.” | Pass |

### HW3 gift-card retest in HW4

| Test | Steps | Expected result | Actual result | Status |
| --- | --- | --- | --- | --- |
| Gift-card CRUD as Admin | Signed in as Admin, added a temporary gift card, opened its details, edited it, and deleted it. | The card can be created, viewed, updated, and removed. | The card appeared after creation, its details opened, the edits saved, and the card was removed from the list after deletion. | Pass |
