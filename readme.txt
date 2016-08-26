
Instant-runoff voting (IRV)
aka
	alternative vote (AV)
	transferable vote (single-seat)
	Ranked Choice Voting (RCV)
	Preferential Voting 

Each elector ranks all candidates in order of preference.

Begin
	Ballots are counted for each elector's top choice.
	If one candidate gets a majority, that candidate wins.
	If not, the last ranked candidate is eliminated from the race.
	On any ballot ranking this defeated candidate, all the candidates ranked behind him or her are moved up one ranking.
Repeat

Ballots are counted for each elector's top choice.
While no candidate has a majority {
	The last ranked candidate is eliminated from the race.
	On any ballot ranking this defeated candidate, all the candidates ranked behind him or her are moved up one ranking.
	Ballots are counted for each elector's top choice.
}
The candidate with the majority wins.

---------------------
Principles

Political parties are illegal.  Exist only for electioneering.
Is electioneering legal?
	constitution
	Federal Election Commission (FEC)

Insisting on only two candidates is "black-and-white thinking" which is a form of faulty reasoning.

If a bank can keep secure online accounts for its customers, a government can provide a secure online voting system for its citizens.

Anonymity?  How is citizen privacy guaranteed?
	Open source code.
	Crowd sourced system admin?
	User login with username and password.
	UserID is associated with his email, and with his votes.  Votes are encrypted, same as password.

Verifiability.
	Open source code.
	Crowd sourced system admin?
	Voter's votes can be emailed to him by request at any time.

Eligibility
	federal: must be USA citizen, soc sec# ?
	state: drivers license?
	municipal: ?
	private:
	must be member of group for private vote
		list of email addresses of each group
		code provided by group


	
----------------------
Current systems as of 22 August 2016

Helios Voting
https://vote.heliosvoting.org/
have to login with google or facebook

Scantegrity
verify optical scan voting systems

STARVote
development has not begun


-------------------
Sponsors
EFF
GitHub

----------------
requirements

roles
	voter
	election manager
	admin

election manager
	create election
	start voting - open polls
	stop voting - close polls
	count votes
	delete election
	duplicate election
	recount
	settings
		name
		open date/time
		close date/time
		continuous count y/n - redisplay results after every vote
		offices or referenda
			candidates for each office
			number of positions for each office
		write-ins allowed y/n
		
		
	As an administrator, you have the power to designate who can vote, when the election starts, when the election stops, and when the results are released. Other than that, you have no power beyond what voters in your election have. This is by design.

write-ins
	as soon as 5 voters have written-in the same name, this name is added as a candidate
	display a list of write-ins?
	there are requirements to be elected potus, therefore only certain declared candidates can be in
	
user
	register
	login
	edit profile
	vote
		generate ballot
	change my vote
		any time up to poll closing
	verify my vote
		display it to me
		email it to me
	register to vote - prove eligibility at this time

	
-------------------------	

The 2016 USA Presidential Election 
Using Instant Runoff Voting
https://en.wikipedia.org/wiki/Instant-runoff_voting

Please register or sign-in and vote now. See instant results.
Please vote now.  See instant results.
Vote Now!  See instant results.

In order of preference:
#1 First Choice
#2 Second Choice
#3 Third Choice
#4 Fourth Choice
#5 Fifth Choice

Bernie Sanders, Independent
Hillary Clinton, Democratic
Donald Trump, Republican
Jill Stein, Green
Gary Johnson, Libertarian

draw ballot, random ordering
drag and drop into order, activate submit button when all five dragged
submit button
show instant results, including results of each run-off round

Total number of voters:
Average number of ranked candidates per voter:

Two Tabs: Vote, Results
Let users see results, even if they don't vote.

Do I have a project named "Vote" already?

AirAsia X Flight 183