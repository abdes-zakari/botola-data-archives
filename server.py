import flask
from flask import request, jsonify
from database import Database
import json
from flask_cors import CORS
import sys
from collections import defaultdict
from itertools import groupby

db = Database()

app = flask.Flask(__name__)
app.config["DEBUG"] = True
CORS(app)

@app.route('/', methods=['GET'])
def home():
    return "<h1>Botola API using Python flask</h1>"


@app.route('/test', methods=['GET'])
def test():
    return "<h1> Test </h1>"

@app.route('/games', methods=['GET'])
def getGames():
    # games = db.select('games')
    games = fetchGames()
    # return json.dumps(games)
    # return jsonify(json.dumps(games))
    print(games)
    return jsonify(games)

@app.route('/standing', methods=['GET'])
def getStanding():
    games = fetchGames()
    teamsList = teamList(games)
    standing = standingHandler(games,teamsList)
    # standing = sorted(standing, key=lambda k: k.get('pts', 0), reverse=True)
    standing = sorted(standing, key=lambda k: (int(k['pts']),int(k["gd"])), reverse=True)
 
    print(teamsList)
    return jsonify(standing)
    # return jsonify(teamsList)

@app.route('/standing/stage/<stage>', methods=['GET'])
def getStandingByStage(stage):
    games = fetchGamesUntilStage(stage)
    teamsList = teamList(games)
    standing = standingHandler(games,teamsList)
    # standing = sorted(standing, key=lambda k: k.get('pts', 0), reverse=True)
    standing = sorted(standing, key=lambda k: (int(k['pts']),int(k["gd"])), reverse=True)
    
    # print(teamsList)
    return jsonify(standing)

@app.route('/standing/positions/<team_id>', methods=['GET'])
def getStandingEveryStage(team_id):
    # allStanding = []
    # positions = [0]
    # for i in range(1,31,1):
    #     games = fetchGamesUntilStage(str(i))
    #     teamsList = teamList(games)
    #     standing = standingHandler(games,teamsList)
    #     standing = sorted(standing, key=lambda k: (int(k['pts']),int(k["gd"])), reverse=True)
    #     # row = {}
    #     # row['day'] = i
    #     # row['standing'] = standing
    #     # allStanding.append(row)
    #     for i in standing:
    #         if i['id'] == int(team_id):
    #             positions.append(standing.index(i)+1)
    
    # print(positions)
    # return jsonify(positions)
    games = fetchGames()
    # games = games[5:15]
    allStanding = []
    gamesParts = []
    
    for i in range(1,31,1):
        gamesPart = games[0:i*8]
        gamesParts.append(gamesPart)
    
    positions = []
    stages = []
    for k in range(0,30,1):
        teamsList = teamList(games)
        standing = standingHandler(gamesParts[k],teamsList)
        standing = sorted(standing, key=lambda k: (int(k['pts']),int(k["gd"])), reverse=True)
        
        for i in standing:
            if i['id'] == int(team_id):
                positions.append(standing.index(i)+1)
                stages.append(k+1)
    print(team_id)
    # print(stages)
    # updateinsertPositionsForTheChartTest(team_id,stages,positions)
    return jsonify(positions)
    # return jsonify(allStanding)
    # return jsonify(allStanding[10])

    # return "getStandingEveryStage"


@app.route('/games/stage/<stage>', methods=['GET'])
def getGamesByStage(stage):
    games = fetchGamesByStage(stage)
    print(games)
    return jsonify(games)


def teamList(games):
    teamsList = []
    for i in range(0,8,1):
        if not list(filter(lambda x:x["name"]==games[i]['home_name'],teamsList)):
            team = {}
            team['name'] = games[i]['home_name']
            team['id'] = games[i]['home_id']
            team['played'] = 0
            team['win'] = 0
            team['draw'] = 0
            team['los'] = 0
            team['pts'] = 0
            team['gf'] = 0
            team['ga'] = 0
            teamsList.append(team)
        if not list(filter(lambda x:x["name"]==games[i]['away_name'],teamsList)):
            team = {}
            team['name'] = games[i]['away_name']
            team['id'] = games[i]['away_id']
            team['played'] = 0
            team['win'] = 0
            team['draw'] = 0
            team['los'] = 0
            team['pts'] = 0
            team['gf'] = 0
            team['ga'] = 0
            teamsList.append(team)
    return teamsList

def standingHandler(games,teamsList):
    for i in range(0,len(games),1):
        if games[i]['home_score'] > games[i]['away_score']:
            for team in teamsList:
                if team['name'] == games[i]['home_name']:
                    team['pts'] = team['pts']+3
                    team['played'] = team['played']+1
                    team['win'] = team['win']+1
                if team['name'] == games[i]['away_name']:
                    team['pts'] = team['pts']+0
                    team['played'] = team['played']+1
                    team['los'] = team['los']+1
        if games[i]['home_score'] < games[i]['away_score']:
            for team in teamsList:
                if team['name'] == games[i]['home_name']:
                    team['pts'] = team['pts']+0
                    team['played'] = team['played']+1
                    team['los'] = team['los']+1
                if team['name'] == games[i]['away_name']:
                    team['pts'] = team['pts']+3
                    team['played'] = team['played']+1
                    team['win'] = team['win']+1
        if games[i]['home_score'] == games[i]['away_score']:
            for team in teamsList:
                if team['name'] == games[i]['home_name']:
                    team['pts'] = team['pts']+1
                    team['played'] = team['played']+1
                    team['draw'] = team['draw']+1
                if team['name'] == games[i]['away_name']:
                    team['pts'] = team['pts']+1
                    team['played'] = team['played']+1
                    team['draw'] = team['draw']+1
        for team in teamsList:
            if team['name'] == games[i]['home_name']:
                team['gf'] = team['gf']+games[i]['home_score']
                team['ga'] = team['ga']+games[i]['away_score']
            if team['name'] == games[i]['away_name']:
                team['gf'] = team['gf']+games[i]['away_score']
                team['ga'] = team['ga']+games[i]['home_score'] 
            
            team['gd'] = team['gf'] - team['ga']
    return teamsList


def fetchGames2():
    # sql = ""
    data = db.query("""\
                    SELECT 
                        id,
                        game_id,
                        stage,
                        CONCAT(home_name, " ", home_score, "-", away_score," ", away_name) AS matcha,
                        home_name,
                        home_score,
                        away_score,
                        away_name
                    FROM games order by stage """
                    )
    
    return data

def fetchGames():
    # sql = ""
    data = db.query("""\
                    SELECT 
                        id,
                        game_id,
                        comp_id,
                        stage,
                        game_date,
                        home_id,
                        home_name,
                        home_score,
                        home_data,
                        home_data->'$.goals' home_goals,
                        away_id,
                        away_name,
                        away_score,
                        away_data,
                        away_data->'$.goals' away_goals
                    FROM games order by stage """
                    )
    
    return data

def fetchGamesByStage(stage):
    # sql = ""
    data = db.query("""\
                    SELECT 
                        id,
                        game_id,
                        comp_id,
                        stage,
                        game_date,
                        home_id,
                        home_name,
                        home_score,
                        home_data,
                        home_data->'$.goals' home_goals,
                        away_id,
                        away_name,
                        away_score,
                        away_data,
                        away_data->'$.goals' away_goals
                    FROM games
                    WHERE stage = '"""+stage+"""\'"""
                    )
    
    return data


def fetchGamesUntilStage(stage):
    sql = """
                    SELECT 
                        id,
                        game_id,
                        comp_id,
                        stage,
                        game_date,
                        home_id,
                        home_name,
                        home_score,
                        home_data,
                        home_data->'$.goals' home_goals,
                        away_id,
                        away_name,
                        away_score,
                        away_data,
                        away_data->'$.goals' away_goals
                    FROM games
                    WHERE stage <= """+stage

    data = db.query(sql)
    
    return data
    #positionteams
def insertPositionsForTheChart(team_id,stages,positions):
    print(positions)
    print(stages)
    
    for i in range(0,30,1):
        db.addPosition(team_id,stages[i],positions[i])

app.run(port='9000', threaded=True)





#MUSTER 
# data = db.query("""\
#                     SELECT 
#                         id,
#                         game_id,
#                         comp_id,
#                         stage,
#                         game_date,
#                         home_id,
#                         home_name,
#                         home_score,
#                         home_data,
#                         home_data->'$.goals' home_goals,
#                         away_id,
#                         away_name,
#                         away_score,
#                         away_data,
#                         away_data->'$.goals' away_goals
#                     FROM games
#                     WHERE home_name = '"""+str(test)+"""\'"""
#                     )


# https://www.facebook.com/groups/pycommunity/permalink/2678333059117486/
# la = [
#         {"item_id": 1294},
#         {"item_id": 3453},
#         {"item_id": 7612},
#         {"item_id": 9871},
#         {"item_id": 4311},
#         {"item_id": 3212},
#     ]
#     for i in la:
#         if i['item_id'] == 4311:
#             print('ja')
#             print(la.index(i))