create database project;
use project;
create table users( userid int PRIMARY KEY, username VARCHAR (50), password VARCHAR(50), email VARCHAR(100));
create table admin( adminid int PRIMARY KEY, adminname VARCHAR (50), password VARCHAR(50), email VARCHAR(100));
describe users;
describe admin;
insert into users ( name, email, password) values ()

insert into admin (name, email, password) values ()
