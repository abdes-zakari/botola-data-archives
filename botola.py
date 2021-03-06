import requests as req
import urllib.request
import re
import os
import json
import sys 
import shutil
import time
import mysql.connector
from database import Database
from manager import getGamesList,getGame

db = Database()
# res = db.select("tata")
# res = db.query(" SELECT id, away_data->'$.goals' testa from games")
# resa = json.loads(res[0][1]) 
# print(len(resa))
# sys.exit()

# start process hier 
# 1-Action: get IDS
competition = "16056"  #Botola saison 2018/2019
# date = "201808"
dateList =["201808","201809","201810","201811","201812","201901","201902","201903","201904","201905","201906"]
# print(dateList[2])
# sys.exit()
allIds = getGamesList(competition,dateList[0])
print("############# IDs ###############")
print(allIds)


# 2-Action: get Games
allGame = []

for i in range(0,len(allIds),1):
# for i in range(16,18,1):
    d = getGame("https://www.goalzz.com/?m="+str(allIds[i])+"&ajax=true")
    allGame.append(d)

print("############# Games ###############")
print(allGame)
print("############# addMultiple ###############")
# print(json.loads(allGame))

#save in database
# db.addMultiple(json.dumps(allGame))





# allGame = getGame("https://www.goalzz.com/?m=1792802&ajax=true")
# home_data =  json.dumps(allGame["home_data"])
# away_data =  json.dumps(allGame["away_data"])
# db.addOne(allGame["comp_id"],allGame["game_id"],allGame["game_date"],allGame["home_id"],allGame["home_name"],allGame["home_score"],home_data,allGame["away_id"],allGame["away_name"],allGame["away_score"],away_data)
# # print(allGame["comp_id"])
# print(allGame)

with open('data.json', 'w', encoding='utf-8') as f:
    json.dump(allGame, f, ensure_ascii=False, indent=4)
