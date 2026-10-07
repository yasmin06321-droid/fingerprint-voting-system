-- Fingerprint Voting System Database
-- Database: voting_db

CREATE DATABASE IF NOT EXISTS voting_db;

USE voting_db;

-- Voter information
CREATE TABLE users (
    voterid VARCHAR(250) NOT NULL,
    age INT NOT NULL,
    PRIMARY KEY (voterid)
);

-- Voting information
CREATE TABLE votes (
    voterid VARCHAR(250) NOT NULL,
    partyname VARCHAR(250) NOT NULL,
    PRIMARY KEY (voterid)
);
