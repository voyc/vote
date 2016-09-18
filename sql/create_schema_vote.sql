drop schema vote cascade;
create schema vote;

create table vote.election (
	id serial,
	name varchar(50),
	startdate timestamp,
	enddate timestamp
);

create table vote.candidate (
	id serial,
	electionid integer,
	name varchar(50),
	party varchar(50)
);
create index ndx_candidate_electionid on vote.candidate(electionid);

create table vote.vote (
	id serial,
	userid integer,
	electionid integer,
	votes varchar(500)
);
create index ndx_vote_userid on vote.vote(userid);
create index ndx_vote_electionid on vote.vote(electionid);

insert into vote.election (id, name, startdate, enddate) values (1,'USA President', 	timestamp '2016-08-01 01:00', timestamp '2016-11-08 19:00');

insert into vote.candidate (id, electionid, name, party) values (1,1,'Donald Trump', 'Republican');
insert into vote.candidate (id, electionid, name, party) values (2,1,'Hillary Clinton', 'Democrat');
insert into vote.candidate (id, electionid, name, party) values (3,1,'Bernie Sanders', 'Independent');
insert into vote.candidate (id, electionid, name, party) values (4,1,'Gary Johnson', 'Libertarian');
insert into vote.candidate (id, electionid, name, party) values (5,1,'Jill Stein', 'Green');
insert into vote.candidate (id, electionid, name, party) values (6,1,'Mario Rubio', 'Independent');

insert into vote.vote (id,userid,electionid,votes) values ( 1,1,1,'000000100000000000000000000000000000000000');
insert into vote.vote (id,userid,electionid,votes) values ( 2,1,1,'000000200000000000000000000000000000000000');
insert into vote.vote (id,userid,electionid,votes) values ( 3,1,1,'000000300000000000000000000000000000000000');
insert into vote.vote (id,userid,electionid,votes) values ( 4,1,1,'000000400000000000000000000000000000000000');
insert into vote.vote (id,userid,electionid,votes) values ( 5,1,1,'000000500000000000000000000000000000000000');
insert into vote.vote (id,userid,electionid,votes) values ( 5,1,1,'000000600000000000000000000000000000000000');
insert into vote.vote (id,userid,electionid,votes) values ( 6,1,1,'000000200000030000004000000500000000000000');
insert into vote.vote (id,userid,electionid,votes) values ( 7,1,1,'000000300000020000004000000500000000000000');
insert into vote.vote (id,userid,electionid,votes) values ( 8,1,1,'000000400000050000003000000200000000000000');
insert into vote.vote (id,userid,electionid,votes) values ( 9,1,1,'000000500000040000003000000200000000000000');
insert into vote.vote (id,userid,electionid,votes) values (10,1,1,'000000200000030000000000000000000000000000');
insert into vote.vote (id,userid,electionid,votes) values (11,1,1,'000000300000020000000000000000000000000000');
insert into vote.vote (id,userid,electionid,votes) values (12,1,1,'000000100000060000000000000000000000000000');
insert into vote.vote (id,userid,electionid,votes) values (13,1,1,'000000600000040000001000000000000000000000');
select setval('vote.vote_id_seq', 13);
