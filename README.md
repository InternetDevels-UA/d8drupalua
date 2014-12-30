# Vagrant profile for Drupal.ua project

## Background

Vagrant and VirtualBox (or some other VM provider) can be used to quickly build or rebuild virtual servers.

## Getting Started

This README file is inside a folder that contains a `Vagrantfile` (and all commands mentioned here should be executed
from this directory), which tells Vagrant how to set up your virtual machine in VirtualBox.

To use the vagrant file, you will need to have done the following:

  1. Download and Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
  2. Download and Install [Vagrant](http://downloads.vagrantup.com/)

Once all of that is done, you can simply type in `vagrant up` and Vagrant will create a new VM, install the base box
and configure it.

Once the new VM is up and running (after `vagrant up` is complete and you're back at the command prompt), you can log
into it via SSH by typing in `vagrant ssh`. Windows users encouraged to use [Cygwin](http://www.cygwin.com/) instead of
standard command prompt.

You should add the line `192.168.111.111  drupalua.local` to your `/etc/hosts` file.
Windows users could look here: c:\windows\system32\drivers\etc\hosts.

After this manipulations you be able to access your new Drupal site at the [http://drupalua.local](http://drupalua.local/).
Also the same site will be available here - [192.168.111.111/drupal/](192.168.111.111/drupal/) if for some reasons you
will not be able to modify your hosts file, but be aware that, due to [PuPHPet](https://puphpet.com/) limitations, in
this case you will not be able to use Drupal's clean URLs feature and will need to access your installation this way -
[http://192.168.111.111/drupal/index.php](http://192.168.111.111/drupal/index.php)
and [http://192.168.111.111/drupal/index.php/user](http://192.168.111.111/drupal/index.php/user) to log in.

Also you will need to install Drupal yourself. You can do it from shell by executing `sh drupal/reinstall.sh` or by
visiting [http://drupalua.local](http://drupalua.local/).

Adminer is available here - [192.168.111.111/adminer/](192.168.111.111/adminer/).

Mailcatcher is available here - [192.168.111.111:1080](192.168.111.111:1080).
