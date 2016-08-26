# cluster-reporting
Push cluster statistics to web server and display them in DokuWiki.

## Usage
 * Copy contents of the ``cluster`` folder to your cluster.
 * Create ``statistics.cfg`` config file using the template.
 * Periodically run the ``statistics.sh`` bash script e.g. via cron
 
   ```bash
 */5 * * * * /home/myuser/reporting/statistics.sh
   ```
 * Copy contents of the ``web`` folder to your web server.
 * Modify the script according to your needs (cluster names, output formating, etc.)
 * Periodically run the ``parse_stats.rb`` ruby script e.g. via cron
 
   ```bash
 */5 * * * * cd /var/www/myweb/statistics/; sudo -u www-data ruby2.0 parse_stats.rb >> stats.log
   ```
