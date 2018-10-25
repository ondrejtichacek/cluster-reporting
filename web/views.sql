DROP VIEW IF EXISTS fs;
CREATE VIEW fs(system, system_name, path, mounted, size, used, avail, recorded)
AS (
SELECT c.name, c.display_name, b.path, b.mounted, b.size, a.used, a.avail, a.recorded
FROM fs_occupancy a
    INNER JOIN filesystem b
        ON a.filesystem_id = b.id
    INNER JOIN cluster c
        ON b.cluster_id = c.id
);

DROP VIEW IF EXISTS fs_recent;
CREATE VIEW fs_recent(system, system_name, path, mounted, size, used, avail, recorded)
AS (
SELECT c.name, c.display_name, b.path, b.mounted, b.size, a.used, a.avail, a.recorded
FROM fs_occupancy a
    INNER JOIN (
        SELECT filesystem_id, MAX(recorded) AS last_recorded
        FROM fs_occupancy
        GROUP BY filesystem_id
    ) r
    ON a.recorded >= DATE_SUB(r.last_recorded, INTERVAL 7 DAY)
    AND a.filesystem_id = r.filesystem_id
    INNER JOIN filesystem b
        ON a.filesystem_id = b.id
    INNER JOIN cluster c
        ON b.cluster_id = c.id
);


DROP VIEW IF EXISTS fs_most_recent;
CREATE VIEW fs_most_recent(system, system_name, path, mounted, size, used, avail, recorded)
AS (
SELECT c.name, c.display_name, b.path, b.mounted, b.size, a.used, a.avail, a.recorded
FROM fs_occupancy a
    INNER JOIN (
        SELECT filesystem_id, MAX(recorded) AS last_recorded
        FROM fs_occupancy
        GROUP BY filesystem_id
    ) r
    ON a.recorded = r.last_recorded
    AND a.filesystem_id = r.filesystem_id
    INNER JOIN filesystem b
        ON a.filesystem_id = b.id
    INNER JOIN cluster c
        ON b.cluster_id = c.id
);

DROP VIEW IF EXISTS q;
CREATE VIEW q(system, system_name,
               queue, queue_name, cpu, ram, scratch, gpu,
              cqload, used, used_p, res, avail, total, aoacds, cdsue, recorded)
AS (
SELECT c.name, c.display_name,
       b.name, b.display_name, b.cpu, b.ram, b.scratch, b.gpu,
       a.cqload, a.used, a.used / a.total, a.res, a.avail, a.total, a.aoacds, a.cdsue, a.recorded
FROM q_occupancy a
    INNER JOIN queue b
        ON a.queue_id = b.id
    INNER JOIN cluster c
        ON b.cluster_id = c.id
WHERE b.deprecated = 0
);

DROP VIEW IF EXISTS q_recent;
CREATE VIEW q_recent(system, system_name,
               queue, queue_name, cpu, ram, scratch, gpu,
              cqload, used, used_p, res, avail, total, aoacds, cdsue, recorded)
AS (
SELECT c.name, c.display_name,
       b.name, b.display_name, b.cpu, b.ram, b.scratch, b.gpu,
       a.cqload, a.used, a.used / a.total, a.res, a.avail, a.total, a.aoacds, a.cdsue, a.recorded
FROM q_occupancy a
    INNER JOIN queue b
        ON a.recorded >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        AND a.queue_id = b.id
    INNER JOIN cluster c
        ON b.cluster_id = c.id
WHERE b.deprecated = 0
);

DROP VIEW IF EXISTS q_most_recent;
CREATE VIEW q_most_recent(system, system_name,
               queue, queue_name, cpu, ram, scratch, gpu,
              cqload, used, used_p, res, avail, total, aoacds, cdsue, recorded)
AS (
SELECT c.name, c.display_name,
       b.name, b.display_name, b.cpu, b.ram, b.scratch, b.gpu,
       a.cqload, a.used, a.used / a.total, a.res, a.avail, a.total, a.aoacds, a.cdsue, a.recorded
FROM q_occupancy a
    INNER JOIN (
        SELECT queue_id, MAX(recorded) AS last_recorded
        FROM q_occupancy
        GROUP BY queue_id
    ) r
    ON a.recorded = r.last_recorded
    AND a.queue_id = r.queue_id
    INNER JOIN queue b
        ON a.queue_id = b.id
    INNER JOIN cluster c
        ON b.cluster_id = c.id
WHERE b.deprecated = 0
);

DROP VIEW IF EXISTS cq;
CREATE VIEW cq(system, system_name, used, res, avail, total, aoacds, cdsue, recorded)
AS (
SELECT q.system, q.system_name,
       SUM(q.used), SUM(q.res), SUM(q.avail),
       SUM(q.total), SUM(q.aoacds), SUM(q.cdsue),
        q.recorded
FROM q
    GROUP BY q.recorded,q.system
);

DROP VIEW IF EXISTS cq_recent;
CREATE VIEW cq_recent(system, system_name, used, res, avail, total, aoacds, cdsue, recorded)
AS (
SELECT a.system, a.system_name,
       SUM(a.used), SUM(a.res), SUM(a.avail),
       SUM(a.total), SUM(a.aoacds), SUM(a.cdsue),
        a.recorded
FROM q_recent AS a
    GROUP BY a.recorded,a.system
);

DROP VIEW IF EXISTS cfs;
CREATE VIEW cfs(system, system_name, size, used, avail, recorded)
AS (
SELECT fs.system, fs.system_name,
       SUM(fs.size), SUM(fs.used), SUM(fs.avail),
        fs.recorded
FROM fs
    GROUP BY fs.recorded,fs.system
);

DROP VIEW IF EXISTS cfs_recent;
CREATE VIEW cfs_recent(system, system_name, size, used, avail, recorded)
AS (
SELECT a.system, a.system_name,
       SUM(a.size), SUM(a.used), SUM(a.avail),
        a.recorded
FROM fs_recent as a
    GROUP BY a.recorded,a.system
);

DROP VIEW IF EXISTS cq_most_recent;
CREATE VIEW cq_most_recent(system, system_name, used, res, avail, total, aoacds, cdsue, recorded)
AS (
SELECT system, system_name,
       SUM(used), SUM(res), SUM(avail),
       SUM(total), SUM(aoacds), SUM(cdsue),
        recorded
FROM q_most_recent
    GROUP BY system, recorded
);

DROP VIEW IF EXISTS cfs_most_recent;
CREATE VIEW cfs_most_recent(system, system_name, size, used, avail, recorded)
AS (
SELECT system, system_name,
       SUM(size), SUM(used), SUM(avail),
        recorded
FROM fs_most_recent
    GROUP BY system, recorded
);

DROP VIEW IF EXISTS c;
CREATE VIEW c(system, system_name, q_used, q_res, q_avail, q_total, q_aoacds, q_cdsue, fs_size, fs_used, fs_avail, recorded)
AS (
SELECT cq.system, cq.system_name,
       cq.used, cq.res, cq.avail,
       cq.total, cq.aoacds, cq.cdsue,
       cfs.size, cfs.used, cfs.avail,
        cq.recorded
FROM cq
INNER JOIN cfs
ON cq.system = cfs.system AND cq.recorded = cfs.recorded
    GROUP BY cq.recorded,cq.system
);

DROP VIEW IF EXISTS c_recent;
CREATE VIEW c_recent(system, system_name, q_used, q_res, q_avail, q_total, q_aoacds, q_cdsue, fs_size, fs_used, fs_avail, recorded)
AS (
SELECT a.system, a.system_name,
       a.used, a.res, a.avail,
       a.total, a.aoacds, a.cdsue,
       b.size, b.used, b.avail,
        a.recorded
FROM cq_recent AS a
INNER JOIN cfs_recent AS b
ON a.system = b.system AND a.recorded = b.recorded
    GROUP BY a.recorded,a.system
);

DROP VIEW IF EXISTS queue_details;
CREATE VIEW queue_details(system, system_name,
                          name, display_name, cpu, ram, scratch, gpu)
AS (
SELECT c.name, c.display_name,
       b.name, b.display_name, b.cpu, b.ram, b.scratch, b.gpu
FROM queue b
    INNER JOIN cluster c
        ON b.cluster_id = c.id
);

DROP VIEW IF EXISTS c_occupancy_weekdays;
CREATE VIEW c_occupancy_weekdays(system, system_name, used, res, avail, total, aoacds, cdsue, weekday, wdno)
AS (
SELECT q.system, q.system_name,
       AVG(q.used), AVG(q.res), AVG(q.avail),
       AVG(q.total), AVG(q.aoacds), AVG(q.cdsue),
       DAYNAME(q.recorded) AS weekday,
       (case DAYOFWEEK(q.recorded) WHEN 1 THEN 8 else DAYOFWEEK(q.recorded) END) - 1 AS wdno
FROM q
    GROUP BY q.system, weekday, wdno
    ORDER BY wdno
);

DROP VIEW IF EXISTS c_occupancy_hours;
CREATE VIEW c_occupancy_hours(system, system_name, used, res, avail, total, aoacds, cdsue, hour)
AS (
SELECT q.system, q.system_name,
       AVG(q.used), AVG(q.res), AVG(q.avail),
       AVG(q.total), AVG(q.aoacds), AVG(q.cdsue),
       HOUR(q.recorded) AS hhour
FROM q
    GROUP BY q.system, hhour
    ORDER BY hhour
);

DROP VIEW IF EXISTS node_details;
CREATE VIEW node_details(system, system_name,
                          name, num_proc, mem_total, swap_total)
AS (
SELECT c.name, c.display_name,
       b.name, b.num_proc, b.mem_total, b.swap_total
FROM nodes b
    INNER JOIN cluster c
        ON b.cluster_id = c.id
);
