# Ansible Vagrant profile for Drupal.ua project

## Background

Vagrant and VirtualBox (or some other VM provider) can be used to quickly build or rebuild virtual servers.

This Vagrant profile installs [Drupal](https://drupal.org/) using the shell provisioner 
with ansible within Ubuntu 12.04LTS 64bit guest OS.

## Getting Started

You should clone this repo with all git submodules by command
```sh
git clone --recursive REPO_URL
```

Where REPO_URL is an url of your personal fork of current repo.

This README file is inside a folder that contains a `Vagrantfile` 
(here after this folder shall be called the [vagrant_root]), 
which tells Vagrant how to set up your virtual machine in VirtualBox.

To use the vagrant file, you will need to have done the following:

  1. Download and Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
  2. Download and Install [Vagrant](http://downloads.vagrantup.com/)
  3. Install [NFS server]  (sudo apt-get install nfs-server nfs-client)
  5. Open a shell prompt and cd into the folder containing the `Vagrantfile`.
  6. run ```vagrant up --provider=virtualbox```
  7. login to the vm with a command ```vagrant ssh```


You should be able to access your new Drupal site at either the  
http://drupal.192.168.56.132.xip.io/ if you add the line `192.168.56.132  drupal.192.168.56.132.xip.io` to your `/etc/hosts` file.

 site http://drupal.192.168.56.132.xip.io/
 adminer http://drupal.192.168.56.132.xip.io/adminer.php
 phpinfo http://drupal.192.168.56.132.xip.io/phpinfo.php
