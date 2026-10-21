# AirAsia Rewards - HW2

Static HTML and CSS, saved as PHP files. Start at `login.php`.

## GitHub Codespaces

Create a repository containing these files and open **Code > Codespaces > Create codespace on main**. In the Codespaces terminal, run:

```sh
php -S 0.0.0.0:8000
```

Open port 8000 in the Ports panel, then navigate to `/login.php`. Use sample credentials only. Verify all six workflow transitions, then commit and push your work. Submit the repository link in Canvas and ensure the instructor can access it.

## Pages and workflow

- Login form -> `card-list.php`
- Gift card image or name -> `card-details.php`
- Add gift card link -> `card-add.php`
- Update gift card link -> `card-update.php`
- Add form submission -> `card-list.php`
- Update form submission -> `card-list.php`

All card links intentionally share the single sample details page, as HW2 permits. The details and update pages use the same Retail gift card sample data. Forms use POST and do not save or authenticate. Every page loads `css/styles.css`. Original sample gift-card illustrations are in `images/`.

## Verification status

Local source checks completed: required filenames, local destinations, CSS/image paths, labels, prepopulated update values, and absence of PHP logic/JavaScript. All six required workflow transitions passed in a local browser preview, including Login, Add, and Update POST submissions.
