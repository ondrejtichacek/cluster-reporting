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

DROP VIEW IF EXISTS q;
CREATE VIEW q(system, system_name,
               queue, queue_name, cpu, ram, scratch, gpu,
              cqload, used, res, avail, total, aoacds, cdsue, recorded)
AS (
SELECT c.name, c.display_name,
       b.name, b.display_name, b.cpu, b.ram, b.scratch, b.gpu,
       a.cqload, a.used, a.res, a.avail, a.total, a.aoacds, a.cdsue, a.recorded
FROM q_occupancy a
    INNER JOIN queue b
        ON a.queue_id = b.id
    INNER JOIN cluster c
        ON b.cluster_id = c.id
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

DROP VIEW IF EXISTS cfs;
CREATE VIEW cfs(system, system_name, size, used, avail, recorded)
AS (
SELECT fs.system, fs.system_name,
       SUM(fs.size), SUM(fs.used), SUM(fs.avail),
        fs.recorded
FROM fs
    GROUP BY fs.recorded,fs.system
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
