import requests as req
import json
import re
import sys 

# get games ids from specific items (competition , year and month ect...)
def getGamesIds(url):
    grabGame = req.get(url)
    jsonGame = json.loads(grabGame.text)
    grabGame = jsonGame['matches_list']
    ids = []
    for i in range(3,len(grabGame),20):
      ids.append(grabGame[i])
    return ids

# get games list by date and competition 
def getGamesList(competition,date):
    response = getGamesIds("https://www.goalzz.com/?c="+str(competition)+"&stage=1&smonth="+str(date)+"&ajax=true")
    return response

# return one game infos [goals ,cards, time, id,ect.....]
def getGame(url):
    grabGame = req.get(url)
    jsonGame = json.loads(grabGame.text)
    grabGame = jsonGame['matches_list']
    if grabGame[11]!= "":
        home_id = grabGame[7]
        away_id = grabGame[12]
        home_name = grabGame[9]
        away_name = grabGame[14]
        homeTeam = grabGame[10] #index 10
        awayTeam = grabGame[15] #index 15
        score = grabGame[11]
        score = score.split("~")
        score = score[0].split("|")
        gameInfo = {}
        gameInfo['comp_id'] = grabGame[0]
        gameInfo['game_id'] = grabGame[3]
        gameInfo['game_date'] = grabGame[4]
        stage = re.findall(r'w\|(\d+)\~', grabGame[19])
        gameInfo['stage'] = stage[0] if len(stage)>0 else None
        gameInfo['home_id'] = home_id
        gameInfo['home_name'] = home_name
        gameInfo['home_score'] = score[0]
        gameInfo['home_data'] = getInfosTeam(homeTeam)
        # gameInfo['home_data']  = homeTeam
        gameInfo['away_id'] = away_id
        gameInfo['away_name'] = away_name
        gameInfo['away_score'] = score[1]
        gameInfo['away_data'] = getInfosTeam(awayTeam)
        # gameInfo['away_data']  = awayTeam
        

        return gameInfo

#get infos by team  (use this later to parse infos after save it in the local Database)
def getInfosTeam(team):

    infos = team.split('~')
    allInfos = {}
    goals = []
    ownGoals = []
    yellowCards=[]
    redCards=[]
    penalties = []
    missedPenalties = []
    for i in range(0,len(infos),1):
      row = infos[i]
      d = row.split('|')
      if(d[0]=='g'): goals.append(d[1]+"min,"+d[3])
      if(d[0]=='y'): yellowCards.append(d[1]+"min,"+d[3])
      if(d[0]=='r'): redCards.append(d[1]+"min,"+d[3])
      if(d[0]=='p'): penalties.append(d[1]+"min,"+d[3])
      if(d[0]=='o'): ownGoals.append(d[1]+"min,"+d[3])
      if(d[0]=='m'): missedPenalties.append(d[1]+"min,"+d[3])
    
    allInfos['goals'] = goals
    allInfos['yellowCards'] = yellowCards
    allInfos['redCards'] = redCards
    allInfos['penalties'] = penalties
    allInfos['ownGoals'] = ownGoals
    allInfos['missedPenalties'] = missedPenalties
    return allInfos