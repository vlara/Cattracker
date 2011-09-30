-- scripts/data.sql
--
-- You can begin populating the database with the following SQL statements.

INSERT INTO line(name) VALUES
("A-B");

INSERT INTO line(name) VALUES
("C1 Blue");

INSERT INTO line(name) VALUES
("C2 Express");

INSERT INTO line(name) VALUES
("E");

INSERT INTO line(name) VALUES
("F (Fast Cat)");

INSERT INTO session(description, active) VALUES
("Summer", TRUE);

INSERT INTO location(lat,lng,`name`, description) VALUES
(37.374234134358325,-120.57707526834105,"Castle Air Park","Hospital Ave. north of Wallace Rd.");

INSERT INTO location(lat,lng,`name`, description) VALUES
(37.36649099398204,-120.42475798280333,"Kolligian Library on Ranchers Road","the turnaround north of the building (departure)");

INSERT INTO location(lat,lng,`name`, description) VALUES
(37.36378576910481,-120.43070444018934,"Emigrant Pass at Scholars Lane","asphalt cutout east of the ECEC");

INSERT INTO location(lat,lng,`name`, description) VALUES
(37.33447611432309,-120.47798105628584,"Scholars Lane at Mammoth Lakes Rd.","between Lake Lot 1 and the recreation field");

INSERT INTO location(lat,lng,`name`, description) VALUES
(37.3655935267217,-120.42691179664229,"Gallo Recreation & Fitness Center","on Muir Pass at Scholars Lane");