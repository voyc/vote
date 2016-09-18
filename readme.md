# Vote

http://vote.voyc.com/

## Preferential Voting 

AKA

 * alternative vote (AV)
 * transferable vote (single-seat)
 * Ranked Choice Voting (RCV)

Each elector ranks candidates in order of preference.

## Instant-Runoff Voting (IRV)

Vote-counting rounds continue until one candidate wins with a majority 50% of the votes.

For each round:

 * Ballots are counted for each elector's top choice.
 * If one candidate gets a majority, that candidate wins.
 * If not, the last ranked candidate is eliminated from the race.
 * On any ballot ranking this defeated candidate, all the candidates ranked behind him or her are moved up one ranking.

##Development Principles

The system must be open to public scrutiny.
 * Open source code.
 * Crowd-sourced system admin.

###Security
If a bank can keep secure online accounts for its customers, a government can provide a secure online voting system for its citizens.

###Anonymity
Each voter's privacy must be guaranteed.

 * User login with username and password.
 * UserID is associated with his email, and with his votes.  Votes are encrypted, same as password.

###Verifiability
Any voter must be able to verify that his vote was recorded accurately.

 * Voter's votes can be emailed to him by request at any time.

The community of voters must be able to verify the integrity of vote storage and counting.

###Eligibility
The system must identify each voter and ascertain he is eligible to vote.

 * USA Federal: identified by Social Security number.
 * USA State: identified by drivers license id.
 * USA municipal: ?
 * private organization: identified by an id provided by the organization.

