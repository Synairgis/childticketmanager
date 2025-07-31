## 3.0.2

### Bugfix

- As it was before 3.0.x, assign current user as requester if template has no requester when creating a child ticket.

## 3.0.1

### Security

- Validate inputs in AJAX code.

## 3.0.0

### Features

- Updated the code to work with GLPI 10.0.X. Use previous versions for use with previous GLPI versions. 

## 2.0.4

- Correction pour régler le bogue d'insertion d'image lors de l'ajout d'un suivi dans un ticket

## 2.0.3

- Added a verification to make sure we're actually on page ticket.form.php before doing anything
- Minor code cleanup

## 2.0.2

- Made plugin compatible with new GLPI 9.3 mecanics that stored ticket solution not as a field, but as an entry in a separate table
- Fixed a glitch that broke cascading resolution/closing of tickets if you went from main ticket page to ticket followup page

## 2.0.1

- Fixed incorrect binding that kept cascading resolution/closing of tickets from working
- Reset list value when the ticket type changes to avoid problems with potentially impossible combinations

## 2.0.0

- Made the plugin compatible with GLPI 9.3.X

## 1.2.7

- Fixed a glitch that prevented the list from correctly updating if ticket type had not previously been opened in browser (because condition was
  not yet saved in session)

## 1.2.6

### Bugfix

- Fixed a glitch with the binding of a "change" event and removed some unused debugging code
- Fixed a glitch that desynched the list's filter if a user changed the type of ticket while the list is hidden

## 1.2.5

### Bugfix

- Fixed a glitch in the dropdown that made it display categories that should have been forbidden

## 1.2.2

### Bugfix

- Fixed a bug with the redirection after creating a ticket

## 1.2.1 (2018-04-27)

### Features

- Add popups that more explicitely describe what happened when doing a cascading closure/resolution

## 1.1.1 (2018-04-25)

### Bugfix

- Fixed a bug with the french locale

## 1.1.0 (2018-04-25)

### Features

- Add english language support