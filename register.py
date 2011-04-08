#! /usr/bin/python2.4
import sys

f = open('login_info.php', 'w')

f.write('<?php\n')

print("Enter Username: ")
u = sys.stdin.readline()

print("Enter Password: ")
p = sys.stdin.readline()

f.write('$username="'+str(u.strip())+'";\n')
f.write('$password="'+str(p.strip())+'";')

f.write('\n?>')
f.close()
