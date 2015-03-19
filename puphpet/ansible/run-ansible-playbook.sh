#!/bin/bash

export PYTHONUNBUFFERED=1

playbooks=(
/vagrant/puphpet/ansible/apache.yml \
/vagrant/puphpet/ansible/php.yml \
/vagrant/puphpet/ansible/php-xdebug.yml \
/vagrant/puphpet/ansible/mysql.yml \
/vagrant/puphpet/ansible/memcached.yml \
/vagrant/puphpet/ansible/composer.yml \
/vagrant/puphpet/ansible/drush.yml \
)

for i in "${playbooks[@]}"
do
   echo "Install "${i}
   ansible-playbook ${i}
   if [ "$?" == "0" ]; then
    echo "Finished installing "${i}
   else
    echo "Failed installing "${i}
    exit 1
   fi
done
