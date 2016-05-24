#!/usr/bin/env bash

{ # this ensures the entire script is downloaded and run #

# Update all software & install new
sudo apt-get update && sudo apt-get upgrade -y && sudo apt-get install -y git python

# Clone repository
git clone https://github.com/$GHUSER/foodtrucks.git /root/foodtrucks

} # this ensures the entire script is downloaded and run #
