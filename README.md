# cluster-reporting
Push SGE cluster statistics to web server and visualize them as Chart.js graphs and DokuWiki tables.

![screenshot](https://cloud.githubusercontent.com/assets/8862529/19417108/37ca4b56-93a4-11e6-91f7-5171de80e756.png)

## Usage
 * Copy contents of the ``cluster`` folder to your cluster.
 * Create ``statistics.cfg`` config file using the template.
 * Periodically run the ``statistics.sh`` bash script e.g. via cron
 
   ```bash
 */5 * * * * cd /home/myuser/reporting/; ./statistics.sh
   ```
 * Copy contents of the ``web`` folder to your web server.
 * Modify the script according to your needs (cluster names, output formating, etc.)
 * Periodically run the ``parse_stats.py`` python3 script e.g. via cron
 
   ```bash
 */5 * * * * cd /var/www/myweb/statistics/; python3 parse_stats.py >> stats.log
   ```
