import os

import re

import time

from configparser import ConfigParser

from mysql.connector import MySQLConnection, Error

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
        conn = MySQLConnection(**db_config)

        cursor = conn.cursor()
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

        cursor = conn.cursor()
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

    if res['fs_avail_perc'] >= 40:
        res['fs_color'] = "@lightgreen:"
    elif res['fs_avail_perc'] <= 20:
        res['fs_color'] = "@pink:"
    else:
        res['fs_color'] = ""


    if res['c_avail_perc'] >= 20:
        res['c_color'] = "@lightgreen:"
    elif res['c_avail_perc'] <= 10:
        res['c_color'] = "@pink:"
    else:
        res['c_color'] = ""

    row = """^ [[computing:{cluster}|{cluster}]] | \
                {c_color} {c_avail}/{c_total} ({c_avail_perc} %) \
                    <abbr>\u25F7[Updated {c_recorded}]</abbr>| \
                {fs_color} {fs_avail} GB / {fs_total} GB ({fs_avail_perc} %) \
                    <abbr title='Updated #{fs_recorded}'>\u25F7</abbr>| \
                    """.format(**res)
    return re.sub(r'\s+', " ", row)

def main():

    clusters = read_config(section='clusters')['names'].split(',')
    clusters = map(str.strip, clusters) # strip whitespace

    out = "|            ^ available cores  ^ free space  ^\n"
    for cluster in clusters:
        record = {'cluster': cluster}

        c = cq_select(record)
        fs = cfs_select(record)

        r = format_data_row({**record, **c, **fs})
        out += r + "\n"

    dokuwiki_config = read_config(section='dokuwiki')

    f = open(dokuwiki_config['path'] + '/data/pages/stats/summary.txt', 'w')
    f.write(out)
    f.close()


if __name__ == '__main__':
    main()
