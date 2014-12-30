#!/bin/bash
cd drupal
chmod u+w -R sites/
rm -rf sites/default/files/
rm -rf sites/default/settings.php
rm -rf sites/default/services.yml
drush si --db-url=mysql://drupalua:drupalua@localhost/drupalua --site-name='Drupal.UA' -y --account-pass=admin --writable pp
