# Ansible Vagrant profile for Drupal.ua project

## Background

Vagrant and VirtualBox (or some other VM provider) can be used to quickly build or rebuild virtual servers.

This Vagrant profile installs [Drupal](https://drupal.org/) using the [Ansible](http://www.ansible.com/) provisioner.

## Getting Started

This README file is inside a folder that contains a `Vagrantfile` (hereafter this folder shall be called the [vagrant_root]), which tells Vagrant how to set up your virtual machine in VirtualBox.

To use the vagrant file, you will need to have done the following:

  1. Download and Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
  2. Download and Install [Vagrant](http://downloads.vagrantup.com/)
  3. Install [Ansible]([guide for installing Ansible](http://docs.ansible.com/intro_installation.html)).
  4. Install [NFS server]  (sudo apt-get install nfs-server nfs-client)
  5. Open a shell prompt and cd into the folder containing the `Vagrantfile`.
  6. Deploy mysql dump file db.sql the folder containing the `Vagrantfile`.
  7. This Ansible playbook uses a variety of roles to configure the Drupal enviroment.

Once all of that is done, you can simply type in `vagrant up`, and Vagrant will create a new VM, install the base box, and configure it.

Once the new VM is up and running (after `vagrant up` is complete and you're back at the command prompt), you can log into it via SSH if you'd like by typing in `vagrant ssh`.

You should be able to access your new Drupal site at either the  http://drupalua.local/ if you add the line `192.168.33.5  drupalua.local` to your `/etc/hosts` file.
 site http://drupalua.local/
 phpmyadmin http://drupalua.local/phpmyadmin
 xhprof http://drupalua.local/?debug=all
 mail http://drupalua.local:1080
