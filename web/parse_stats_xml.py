import os

import time

import pandas as pd

from configparser import ConfigParser

from mysql.connector import MySQLConnection, Error

import json

import xmltodict


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

def node_insert_query(record):
    query = """INSERT INTO nodes (cluster_id, name, arch_string, num_proc, mem_total, swap_total)
        VALUES ((
             SELECT id FROM cluster
                 WHERE name = '{cluster}'),
            '{name}', '{arch_string}', '{num_proc}', '{mem_total}', '{swap_total}');""".format(**record)

    return query

def insert_record(query):

    try:
        db_config = read_config(section='mysql')
        conn = MySQLConnection(**db_config)

        cursor = conn.cursor()
        cursor.execute(query)

        conn.commit()
    except Error as e:

        print(query)

        print('Error:', e)
        raise(e)



    finally:
        cursor.close()
        conn.close()

def main():

    clusters = read_config(section='clusters')['names'].split(',')
    clusters = map(str.strip, clusters) # strip whitespace

    for cluster in clusters:
        f = '{}-qhost-q.xml'.format(cluster)
        mtime = os.path.getmtime(f)
        sql_datetime = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(mtime))

        #tt = datetime.datetime.strptime(time.ctime(mtime), "%a %b %d %H:%M:%S %Y")


        with open(f) as fd:
            d = xmltodict.parse(fd.read())
            nodedata = dict()

            for host in d['qhost']['host']:
                if host['@name'] != 'global':

                    nodedata['cluster'] = cluster
                    nodedata['name'] = host['@name']

                    for hostvalue in host['hostvalue']:
                        nodedata[hostvalue['@name']] = hostvalue['#text']

                    query = node_insert_query(nodedata)
#                    print(query)
                    try:
                        insert_record(query)
                    except Error as e:
                        print(e)



if __name__ == '__main__':
    main()
