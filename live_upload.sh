#!/bin/sh

#live_upload.sh

#Egress Network Monitoring
#ECSE 477

#Jake Levine			260206403
#Eubene Sa 				260271182
#Frédéric Weigand-Warr	260191111

scp -r ../EgressNetwork/ bgp@hansonbros.ece.mcgill.ca:~
ssh bgp@hansonbros.ece.mcgill.ca 'sudo cp -r EgressNetwork/ /var/www/'
