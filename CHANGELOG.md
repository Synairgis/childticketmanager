<a name="3.0.0"></a>

### Features

- Updated the code to work with GLPI 10.0.X. Use previous versions for use with previous GLPI versions. 

<a name="2.0.4"></a>

- Correction pour régler le bogue d'insertion d'image lors de l'ajout d'un suivi dans un ticket

<a name="2.0.3"></a>

- Added a verification to make sure we're actually on page ticket.form.php before doing anything
- Minor code cleanup

<a name="2.0.2"></a>

- Made plugin compatible with new GLPI 9.3 mecanics that stored ticket solution not as a field, but as an entry in a separate table
- Fixed a glitch that broke cascading resolution/closing of tickets if you went from main ticket page to ticket followup page

<a name="2.0.1"></a>

- Fixed incorrect binding that kept cascading resolution/closing of tickets from working
- Reset list value when the ticket type changes to avoid problems with potentially impossible combinations

<a name="2.0.0"></a>

- Made the plugin compatible with GLPI 9.3.X

<a name="1.2.7"></a>

- Fixed a glitch that prevented the list from correctly updating if ticket type had not previously been opened in browser (because condition was
  not yet saved in session)

<a name="1.2.6"></a>

### Bugfix

- Fixed a glitch with the binding of a "change" event and removed some unused debugging code
- Fixed a glitch that desynched the list's filter if a user changed the type of ticket while the list is hidden

<a name="1.2.5"></a>

### Bugfix

- Fixed a glitch in the dropdown that made it display categories that should have been forbidden

<a name="1.2.2"></a>

### Bugfix

- Fixed a bug with the redirection after creating a ticket

<a name="1.2.1"></a>

## 1.2.1 (2018-04-27)

### Features

- Add popups that more explicitely describe what happened when doing a cascading closure/resolution

<a name="1.1.1"></a>
## 1.1.1 (2018-04-25)

### Bugfix

- Fixed a bug with the french locale

<a name="1.1.0"></a>
## 1.1.0 (2018-04-25)

### Features

- Add english language support