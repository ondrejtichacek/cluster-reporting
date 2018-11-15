import os

import re

import time

from configparser import ConfigParser

import mysql.connector
from mysql.connector import MySQLConnection, Error

lightgreen = ["#A6FFD0", "#C5FFED", "#E4FFFF"]
lightpink = ["#FFB4EB", "#FFD2FF", "#FFF0FF"]

def read_config(section, filename='config.ini'):
    """ Read configuration file and return a dictionary object
    :param filename: name of the configuration file
    :param section: section of the configuration
    :return: a dictionary of parameters
    """
    # create parser and read ini configuration file
    parser = ConfigParser()
    parser.read(filename)

    # get section
    db = {}
    if parser.has_section(section):
        items = parser.items(section)
        for item in items:
            db[item[0]] = item[1]
    else:
        raise Exception('{0} not found in the {1} file'.format(section, filename))

    return db

def cq_select(record):
    query = """SELECT avail, total, recorded
                FROM cq_most_recent
                WHERE system = "{cluster}";""".format(**record)

    try:
        db_config = read_config(section='mysql')
        conn = mysql.connector.connect(**db_config)
        #conn = MySQLConnection(**db_config)

        cursor = conn.cursor(buffered=True)
        cursor.execute(query)

        for (avail, total, recorded) in cursor:
            res = {'c_avail': int(avail),
                    'c_total': int(total),
                    'c_avail_perc': int(100 * avail / total),
                    'c_recorded': recorded}
            return res

    except Error as e:

        print(query)

        print('Error:', e)
        raise(e)


    finally:
        cursor.close()
        conn.close()

def cfs_select(record):
    query = """SELECT avail, used, recorded
                FROM cfs_most_recent
                WHERE system = "{cluster}";""".format(**record)

    try:
        db_config = read_config(section='mysql')
        conn = MySQLConnection(**db_config)

        cursor = conn.cursor(buffered=True)
        cursor.execute(query)

        for (avail, used, recorded) in cursor:
            res = {'fs_avail': int(avail / 10**6),
                    'fs_total': int((avail + used) / 10**6),
                    'fs_avail_perc': int(100 * avail / (avail + used)),
                    'fs_recorded': recorded}
            return res

    except Error as e:

        print(query)

        print('Error:', e)
        raise(e)


    finally:
        cursor.close()
        conn.close()

def format_data_row(res):

    if res['fs_avail_perc'] >= 80:
        res['fs_color'] = "@" + lightgreen[0] + ":"
    elif res['fs_avail_perc'] >= 60:
        res['fs_color'] = "@" + lightgreen[1] + ":"
    elif res['fs_avail_perc'] >= 40:
        res['fs_color'] = "@" + lightgreen[2] + ":"
    elif res['fs_avail_perc'] <= 10:
        res['fs_color'] = "@" + lightpink[0] + ":"
    elif res['fs_avail_perc'] <= 20:
        res['fs_color'] = "@" + lightpink[1] + ":"
    elif res['fs_avail_perc'] <= 30:
        res['fs_color'] = "@" + lightpink[2] + ":"
    else:
        res['fs_color'] = ""


    if res['c_avail_perc'] >= 75:
        res['c_color'] = "@" + lightgreen[0] + ":"
    elif res['c_avail_perc'] >= 50:
        res['c_color'] = "@" + lightgreen[1] + ":"
    elif res['c_avail_perc'] >= 25:
        res['c_color'] = "@" + lightgreen[2] + ":"
    elif res['c_avail_perc'] <= 5:
        res['c_color'] = "@" + lightpink[0] + ":"
    elif res['c_avail_perc'] <= 10:
        res['c_color'] = "@" + lightpink[1] + ":"
    elif res['c_avail_perc'] <= 15:
        res['c_color'] = "@" + lightpink[2] + ":"
    else:
        res['c_color'] = ""

    row = """^ [[computing:{cluster}|{cluster}]] | \
                {c_color} {c_avail}/{c_total} ({c_avail_perc} %) \
                    <abbr>\u25F7[Updated {c_recorded}]</abbr>| \
                {fs_color} {fs_avail} GB / {fs_total} GB ({fs_avail_perc} %) \
                    <abbr title='Updated #{fs_recorded}'>\u25F7</abbr>| \
                    """.format(**res)

    res['font_size'] = '<fs small>'
    res['font_size_end'] = '</fs>'

    row_min = """^{font_size} [[computing:{cluster}|{cluster_min}]] {font_size_end} \
                    <fs x-small><abbr>?[Updated {c_recorded}, {fs_recorded}]</abbr> {font_size_end}| \
                {c_color} {font_size}{c_avail} ({c_avail_perc} %) {font_size_end}| \
                {fs_color} {font_size}{fs_avail} GB ({fs_avail_perc} %) {font_size_end}| \
                    """.format(**res)

    return re.sub(r'\s+', " ", row), re.sub(r'\s+', " ", row_min)

def main():

    clusters = read_config(section='clusters')['names'].split(',')
    clusters = map(str.strip, clusters) # strip whitespace

    clusters_min = read_config(section='clusters')['names_min'].split(',')
    clusters_min = map(str.strip, clusters_min) # strip whitespace

    out = "|            ^ available cores  ^ free space  ^\n"
    out_min = "| ^ <fs small>cores</fs>  ^ <fs small>space</fs>  ^\n"
    for cluster, cluster_min in zip(clusters, clusters_min):
        record = {'cluster': cluster, 'cluster_min': cluster_min}

        c = cq_select(record)
        fs = cfs_select(record)

        r, r_min = format_data_row({**record, **c, **fs})
        out += r + "\n"
        out_min += r_min + "\n"

    dokuwiki_config = read_config(section='dokuwiki')

    f = open(dokuwiki_config['path'] + '/data/pages/stats/summary.txt', 'w')
    f.write(out)
    f.close()

    f = open(dokuwiki_config['path'] + '/data/pages/stats/summary-min.txt', 'w')
    f.write(out_min)
    f.close()


if __name__ == '__main__':
    main()
