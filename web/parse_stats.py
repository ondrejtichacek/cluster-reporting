import os

import time

import pandas as pd

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


def fs_insert_query(record):
    query = """INSERT INTO fs_occupancy (filesystem_id, recorded, used, avail)
        VALUES ((
            SELECT id FROM filesystem
                WHERE (cluster_id = (SELECT id FROM cluster WHERE name = '{cluster}')
                AND path = '{filesystem}')),
            '{recorded}', {used}, {avail});""".format(**record)

    return query

def q_insert_query(record):
    query = """INSERT INTO q_occupancy (queue_id, recorded, cqload, used, res, avail, total, aoacds, cdsue)
        VALUES ((
            SELECT id FROM queue
                WHERE (cluster_id = (SELECT id FROM cluster WHERE name = '{cluster}')
                AND name = '{queue}')),
            '{recorded}', {cqload}, {used}, {res}, {avail}, {total}, {aoacds}, {cdsue});""".format(**record)

    return query

def insert_record(query):

    try:
        db_config = read_config(section='mysql')
        conn = MySQLConnection(**db_config)

        cursor = conn.cursor()
        cursor.execute(query)

        conn.commit()
    except Error as e:
        print('Error:', e)
        raise(e)



    finally:
        cursor.close()
        conn.close()

def main():

    clusters = read_config(section='clusters')['names'].split(',')
    clusters = map(str.strip, clusters) # strip whitespace

    for cluster in clusters:
        f = '{}-df.txt'.format(cluster)
        mtime = os.path.getmtime(f)
        sql_datetime = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(mtime))
        
        #tt = datetime.datetime.strptime(time.ctime(mtime), "%a %b %d %H:%M:%S %Y")

        # Read data from file
        df = pd.read_table(f, \
            header = 0, \
            skiprows = 0, \
            usecols = [0, 2, 3], \
            names = ['filesystem', 'used', 'avail'], \
            delim_whitespace=True)

        for index, row in df.iterrows():
            row['cluster'] = cluster
            row['recorded'] = sql_datetime
            record = row.to_dict()
            query = fs_insert_query(record)
            try:
                insert_record(query)
            except Error as e:
                print('Exception:', e)
                r = {k: record[k] for k in record.keys() & {'cluster', 'filesystem'}};
                q = """INSERT INTO filesystem (cluster_id, path, size, mounted)
                    VALUES ((
                        SELECT id FROM cluster WHERE name = '{cluster}'),
                        '{filesystem}', NULL, NULL);""".format(**r)
                insert_record(q)
                insert_record(query)

        f = '{}-qstat.txt'.format(cluster)
        mtime = os.path.getmtime(f)
        sql_datetime = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(mtime))

        # Read data from file
        df = pd.read_table(f, \
            header = 0, \
            skiprows = 1, \
            names = ['queue', 'cqload', 'used', 'res', 'avail', 'total', 'aoacds', 'cdsue'], \
            delim_whitespace=True)

        for index, row in df.iterrows():
            row['cluster'] = cluster
            row['recorded'] = sql_datetime
            record = row.to_dict()
            if record['cqload'] == '-NA-':
                record['cqload'] = 'NULL'
            query = q_insert_query(record)
            try:
                insert_record(query)
            except Error as e:
                print('Exception:', e)
                r = {k: record[k] for k in record.keys() & {'cluster', 'queue'}};
                q = """INSERT INTO queue (cluster_id, name, display_name, cpu, ram, scratch, gpu)
                    VALUES ((
                        SELECT id FROM cluster WHERE name = '{cluster}'),
                        '{queue}', '{queue}', NULL, NULL, NULL, NULL);""".format(**r)
                insert_record(q)
                insert_record(query)


if __name__ == '__main__':
    main()
