#!/bin/bash

source statistics.cfg
source $SGE_settings

df /home > "${NAME}-df.txt"
qstat -g c > "${NAME}-qstat.txt"
qhost -q -xml > "${NAME}-qhost-q.xml"

scp -i $ssh_key \
	"${NAME}-df.txt" \
	"${NAME}-qstat.txt" \
	"${NAME}-qhost-q.xml" \
		$ssh_target
