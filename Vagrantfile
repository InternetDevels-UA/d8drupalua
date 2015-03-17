Vagrant.configure("2") do |config|
  config.vm.box = "debian7.3.x64"
  config.vm.box_url = "http://puppet-vagrant-boxes.puppetlabs.com/debian-73-x64-virtualbox-puppet.box"

  # Enable host-only access to the machine using a specific IP.
  config.vm.network :private_network, ip: "192.168.33.5"
  config.vm.network :forwarded_port, host: 8080, guest: 80, auto_correct: true
  config.vm.network :forwarded_port, host: 2200, guest: 22, auto_correct: true

  config.vm.provider :virtualbox do |v|
    v.customize ["modifyvm", :id, "--name", "drupalua"]
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    v.customize ["modifyvm", :id, "--memory", 2048]
    v.customize ["modifyvm", :id, "--cpus", 2]
    v.customize ["modifyvm", :id, "--ioapic", "on"]
  end
 
  config.vm.synced_folder ".", "/vagrant", :disabled => true
  config.vm.synced_folder ".", "/var/www", :nfs => true

  # Provisioning configuration for Ansible.
  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "provisioning/playbook.yml"
    ansible.inventory_path = "provisioning/inventory"
    # Run commands as root.
    ansible.sudo = true
    # ansible.raw_arguments = ['-v']
    ansible.limit = 'all'
  end

  # Set the name of the VM. See: http://stackoverflow.com/a/17864388/100134
  config.vm.define :drupalua do |drupalua|
    drupalua.vm.hostname = "drupalua"
  end

end
