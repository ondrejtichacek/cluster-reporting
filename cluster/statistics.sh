#!/bin/bash

source statistics.cfg
source $SGE_settings

df -h /home > "${NAME}-df.txt"
qstat -g c > "${NAME}-qstat.txt"

scp -i $ssh_key "${NAME}-df.txt" "${NAME}-qstat.txt" $ssh_target
