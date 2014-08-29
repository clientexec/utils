Clientexec Utilities
=======================

This is a collection of utilities for Clientexec for advanced administrators. Currently only holding language-related scripts.

### Requirements

You need to have PHP installed in your machine. These scripts are intended to be run from the command-line, and have only been tested under Posix OS's (Linux, Mac OS X).

### lang_migrate.php

This will migrate old language files from versions previous to 5.1 to the new format based on PO Gettext files.

You need to have the `msggrep` utility installed in your system, which makes part of the Gettext toolset.

#### How to use

- Copy the old language file into the current directory. For example if you have a Swedish translation, this file would be `swedish.txt` 
- Copy the file `/language/core.pot` to your current directory, and rename it accordingly to the language two-letter code, with the extension `po`. In our example that would be `core-se.po` 
- Run the tool like so:
```
php lang_migrate.php swedish.txt core-se.po
```
- You will see output listing the entries that you had in the old file and that are no longer in CE. That's fine.
- Copy the updated `po` file into Clientexec's `language` directory.
- Finally, you need to translate your `po` file into a `mo` file, using the `lang_regenerate_mo.php` utility, explained below.

### lang_migrate_js.php

This is similar to the previous one, but for javascript lang files:
```
php lang_migrate_js.php swedish.js javascript-se.po
```

### lang_regenerate_mo.php

This will pick all the `po` files under your Clientexec `language` directory and update/create the corresponding `mo` files which are their binary versions which Clientexec actually uses.

You need to have the `msgfmt` utility installed in your system, which makes part of the Gettext toolset. Also make sure have cloned this repo (`utils`) directly under your Clientexec installation.

#### How to use

cd into the root path of your Clientexec installation, and run this command:
```
php utils/lang_regenerate_mo.php
```
