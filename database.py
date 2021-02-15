import mysql.connector
import json
import sys

class Database:
    def __init__(self):
        self.db = mysql.connector.connect(
                        host="localhost",
                        user="root",
                        passwd="*******",
                        database="kooora_botola"
                    )
        self.cursor = self.db.cursor(dictionary=True, buffered=True)
    
    def select(self,table):
        self.cursor.execute('SELECT * FROM '+str(table)+' LIMIT 5')
        result = self.cursor.fetchall()
        # print(result)
        return result

    def query(self,sql):
        self.cursor.execute(sql)
        result = self.cursor.fetchall()
        # print(result)
        return result
    
    def addOne(self,comp_id,game_id,game_date,home_id,home_name,home_score,home_data,away_id,away_name,away_score,away_data):
        sql = "INSERT INTO games (comp_id,game_id,game_date,home_id,home_name,home_score,home_data,away_id,away_name,away_score,away_data) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
        val = (comp_id,game_id,game_date,home_id,home_name,home_score,home_data,away_id,away_name,away_score,away_data)

        self.cursor.execute(sql, val)

        self.db.commit()

        print(self.cursor.rowcount, "record inserted.")

    def addMultiple(self,data):
        sql = "INSERT INTO games3 (comp_id,stage,game_id,game_date,home_id,home_name,home_score,home_data,away_id,away_name,away_score,away_data) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
        data = json.loads(data)
        d = []
        for i in range(0,len(data),1):
            if data[i]:
                home_data =  json.dumps(data[i]["home_data"])
                away_data =  json.dumps(data[i]["away_data"])
                vals = (data[i]["comp_id"],data[i]["stage"],data[i]["game_id"],data[i]["game_date"],data[i]["home_id"],data[i]["home_name"],data[i]["home_score"],home_data,data[i]["away_id"],data[i]["away_name"],data[i]["away_score"],away_data)
                d.append(vals)
        
        
        # print(data[0]["home_data"]["penalties"])
        # print(d)
        # sys.exit()

        self.cursor.executemany(sql, d)

        self.db.commit()

        print(self.cursor.rowcount, "record inserted.")
    
    def addPosition(self,team_id,stage,pos):
        sql = "INSERT  INTO positionteams (team_id,stage,position) VALUES (%s, %s, %s)"
        val = (team_id,stage,pos)

        self.cursor.execute(sql, val)

        self.db.commit()
    
    def __del__(self):
        self.db.close()