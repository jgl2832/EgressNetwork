#!/bin/sh

scp -r ../EgressNetwork/ bgp@hansonbros.ece.mcgill.ca:~
ssh bgp@hansonbros.ece.mcgill.ca 'sudo cp -r EgressNetwork/ /var/www/'
