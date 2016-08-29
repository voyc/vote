select votes, 
to_number(substring(votes,1,7), '9999999'),
to_number(substring(votes,8,7), '9999999'),
to_number(substring(votes,15,7), '9999999'),
to_number(substring(votes,22,7), '9999999'),
to_number(substring(votes,29,7), '9999999'),
to_number(substring(votes,36,7), '9999999')
from vote
where electionid = 1