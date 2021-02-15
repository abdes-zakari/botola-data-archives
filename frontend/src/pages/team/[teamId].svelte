<script>
  import { params } from '@sveltech/routify';
  import { onMount,afterUpdate  } from "svelte";
  import Loader from '../../Loader.svelte'
  import { parseDate}  from '../../js/Helpers';
  import {chartPie,processChartTeamIdPage,chartDoughnut} from '../../js/Charts';
  import Pagination from '../../Pagination.svelte'
  import Modal from '../../Modal.svelte';
  import ModalGameContent from '../../ModalGameContent.svelte';
  import { EyeIcon } from 'svelte-feather-icons'
  export let teamId;
  let showModal = false;
  let ModalGameId;
  let games = [];
  let getgames = {};
  let stats = {
    name_full:""
  };
  let scoredListName = [];
  let yellowListName = [];
  let redListName = [];
  let limita = 7;
  let currentPage = 1;
  let ShowMoreVal = true;
  
  console.log(teamId)
  console.log($params)

   const getGames = async (page) => {
     let response = await fetch("http://localhost:8000/api/v1/games/team/"+teamId+"?page="+page)//lumen
         getgames =  await response.json();
         games = getgames.data
   }
   const getTeamStats = async(teamId) => {
     let response = await fetch("http://localhost:8000/api/v1/stats/team/"+teamId);
         stats = await response.json()
         stats = alterStats(stats);
   }

  const alterStats = (stats) => {

      stats.totalWins = stats.homeWins + stats.awayWins;
      stats.totalDraws = stats.homeDraw + stats.awayDraw;
      stats.totalLoss = stats.homeLos + stats.awayLos;
      stats.totalGoalsF = stats.homeGoals + stats.awayGoals;
      stats.totalGoalsA = stats.homeGoalsAgainst + stats.awayGoalsAgainst;
      stats.goalsDiff = stats.totalGoalsF - stats.totalGoalsA;
      return stats;
  }

  const parseListObject = (obj) =>{

    let keys = Object.keys(obj);
    let values = Object.values(obj);
    // console.log(keys);
    // console.log(values);
    let final = [];
    for (let i = 0; i < values.length; i++) {
      // const element = array[i];
      let obja = {
          name:keys[i],
          count:values[i]
      }
      final.push(obja)
      
    }
    //  console.log(typeof final);
    // console.log(final);

    return final;

    

  }



  
  onMount (async () => {
     await getGames(currentPage);
    // console.log(await getgames)
    // console.log(Object.keys(getgames).length)
    await getTeamStats(teamId);
    
    // await console.log(stats)
    console.log('scoredListName')
    console.log(stats.scoredListName)
    scoredListName = parseListObject(stats.scoredListName);
    yellowListName = parseListObject(stats.yellowCardListName);
    redListName = parseListObject(stats.redCardListName);
    console.log(games)
    console.log(scoredListName)
    chartPie('games-chart',{
      label:[stats.totalWins+" Win", stats.totalDraws+" Draw", stats.totalLoss+" Los"],
      data : [stats.totalWins,stats.totalDraws,stats.totalLoss],
      bgColor: ["#311b92","#b388ff","#a393eb"],
      titleTxt : 'Played: '+parseInt(stats.played)
    })

    chartPie('penos-team-chart',{
      label:[stats.allPenos+" Scored", stats.allMissedPenos+" Missed"],
      data : [stats.allPenos,stats.allMissedPenos],
      bgColor: ["#311b92","#b388ff"],
      titleTxt : 'Penalties for '+stats.name_short.toUpperCase()+': '+parseInt(stats.allPenos+stats.allMissedPenos)
    })

    chartPie('penos-team-against-chart',{
      label:[stats.penosAgainst+" Scored", stats.penosMissedAgainst+" Missed"],
      data : [stats.penosAgainst,stats.penosMissedAgainst],
      bgColor: ["#311b92","#b388ff"],
      titleTxt : 'Penalties Against '+stats.name_short.toUpperCase()+': '+parseInt(stats.penosAgainst+stats.penosMissedAgainst)
    })

    chartDoughnut('cards-team-chart',{
      label:[stats.homeYellowCards+" Yellow Home", stats.homeRedCards+" Red Home", stats.awayYellowCards+" Yellow Away",stats.awayRedCards+" Red Away"],
      data : [stats.homeYellowCards,stats.homeRedCards,stats.awayYellowCards,stats.awayRedCards],
      bgColor: ["#ffa726","#e53935","#c79100","#7f0000"],
      titleTxt : 'Cards '+stats.name_short.toUpperCase()+': '+parseInt(stats.homeYellowCards+stats.awayYellowCards+stats.homeRedCards+stats.awayRedCards)
    })


  console.log(stats.goalsByMinutes)
    processChartTeamIdPage(stats);
   
  });

  const showMore = () => {
    // alert('HOA')
    if(yellowListName.length>limita){
      limita = yellowListName.length
      ShowMoreVal = false;
    }
  };

   function getDataByPage(event) {
        // alert(event.detail.text);
        // alert('salama');
        // alert(event.detail.currentpage);
        games = [];
        // getgames = {};
        getGames(event.detail.currentpage);
    }

 

</script>



<h2> Team : {stats.name_full} </h2>
<Loader show={games.length>0 && stats} />


<!-- Start ROW-->
  <div class="row mt-5">
    <div class="col-md-4 col-sm-12">
      <div class="ctn-box">
        <canvas id="games-chart" width="400" height="450"></canvas> 
      </div>
    </div>
    <div class="col-md-4 col-sm-12">
      <div class="ctn-box">
        <canvas id="games-home-away-chart" width="400" height="450"></canvas> 
      </div>
    </div>
    <div class="col-md-4 col-sm-12">
      <div class="ctn-box">
        <canvas id="goals-bar-chart" width="400" height="450"></canvas> 
      </div>
    </div>
  </div>
<!-- END ROW-->
 
<!-- Start ROW-->
  <div class="row mt-5">
    <div class="col-md-12">
      <div class="ctn-box">
      <h4> List Games</h4>
      <div class="list-game-ctn">
        <Loader show={games.length>0} />
        {#each games as g,key}
          <div class="game-ctn" on:click={()=>{ModalGameId = g.id;showModal = true;}}>
            <div class="eyeIc" > <EyeIcon size="26"/> </div>
            <div class="date-game"> {parseDate(g.game_date)} </div>
            <div class="infos-game"> J{g.stage}  </div>
            <div class="home-name">  {g.home_name} </div>
            <div class="home-score"> {g.home_score}  </div>
            <div class="away-score"> {g.away_score}  </div>
            <div class="away-name">  {g.away_name} </div>   
          </div>
        {/each}
      </div>
    <!-- </div>
    <div class="col-md-12"> -->
        {#if  Object.keys(getgames).length>0}
          {#await getgames then value}
            <Pagination data={value} on:clickPaginate={getDataByPage}></Pagination>
          {/await}
        {/if}
      </div>
    </div>
  </div>
<!-- END ROW-->
 
<!-- Start ROW-->
  <div class="row mt-5">

    <div class="col-md-4">
      <div class="ctn-box">
        <canvas id="cards-team-chart" width="400" height="400"></canvas> 
      </div>
    </div>

    <div class="col-md-4">
    <div class="ctn-box">
      <h4> List yellow cards</h4>
      <table class="table table-sm">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Cards</th>
          </tr>
        </thead>
        <tbody>
          {#each yellowListName as list,key}
            {#if key <= limita}
              <tr>
                <th scope="row">{key+1}</th>
                <td>{list.name}</td>
                <td>{list.count}</td>
              </tr>
            {/if}
          {/each}
        </tbody>
      </table>
      </div>
    </div>

    <div class="col-md-4">
    <div class="ctn-box">
      <h4> List red cards</h4>
      <table class="table table-sm">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Cards</th>
          </tr>
        </thead>
        <tbody>
          {#each redListName as list,key}
            {#if key <= limita}
              <tr>
                <th scope="row">{key+1}</th>
                <td>{list.name}</td>
                <td>{list.count}</td>
              </tr>
            {/if}
          {/each}
        </tbody>
      </table>
      </div>
    </div>
<div class="col-md-12 text-center" style="display: {ShowMoreVal ? 'inline' : 'none'}">  <button on:click={()=> { showMore() }}>Show more </button></div>
  </div>
  <!-- END ROW-->

 <!-- Start ROW-->
  <div class="row mt-5">
    <div class="col-md-3" >
      <div class="ctn-box">
        <h4> Scorers list</h4>
        <table class="table table-sm">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Goals</th>
            </tr>
          </thead>
          <tbody>
            {#each scoredListName as list,key}
              <tr>
                <th scope="row">{key+1}</th>
                <td>{list.name}</td>
                <td>{list.count}</td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-md-9">
      <div class="row">
        <div class="col-md-6">
          <div class="ctn-box">
            <canvas id="chart-goals-by-minutes-team-page" width="400" height="500"></canvas> 
          </div>
        </div>
        <div class="col-md-6">
          <div class="ctn-box">
            <canvas id="chart-goals-by-minutes-against-team-page" width="400" height="500"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END ROW-->
  <div class="row mt-5">
    <div class="col-md-6">
      <div class="ctn-box">
        <canvas id="penos-team-chart" width="400" height="300"></canvas> 
      </div>
    </div>
    <div class="col-md-6">
      <div class="ctn-box">
        <canvas id="penos-team-against-chart" width="400" height="300"></canvas> 
      </div>
    </div>
    
  </div>
   
{#if showModal}
	<Modal on:close="{() => showModal = false}">
		<ModalGameContent gameId={ModalGameId} ></ModalGameContent>
	</Modal>
{/if}

  <!-- <div class="row-ctn">
    
		<div class="left-side">
      dsad
		</div>
    <div class="right-side">

      <div class="row">
  
        <div class="col-md-12">
          
        </div>

        <div class="col-md-12">
          
        </div>
        
        <div class="col-md-12">

        </div>

      </div>
    </div>
  </div> -->




<style>

.eyeIc{
  padding-top:10px;
}

.ctn-box{
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 2px 16px rgba(0,0,0,.06);
    margin-top: 15px;
    -webkit-transition: -webkit-transform 1s;
    transition: -webkit-transform 1s;
    transition: transform 1s;
    transition: transform 1s,-webkit-transform 1s;
    -webkit-transform-style: preserve-3d;
    transform-style: preserve-3d;
    padding:16px;
}

.row-ctn{
  display: flex;

}
.game-ctn{
display: flex;
/* text-align: center; */
margin:auto;
border-bottom: 1px solid #efefef;
width: 1010px;
/* background-color: blanchedalmond; */
/* justify-content: center; */

}
.game-ctn:hover{
  background-color: #efefef;
  cursor: pointer;
}
h4{
  /* padding-left: 20px; */
  color: #3e95cd;
}
.left-side{
			
			/* justify-content: center; */
			
      
			
    }
.right-side{
      /* background-color: aqua; */
      width: 100%;
      margin-left: 15px;
      border: 1px solid #efefef;
       border-radius: 8px; 
       /* text-align: center; */
			
		}
		.date-game{
      padding: 5px;
      margin: 5px;
      width: 130px;
      color: #666;
			/* margin-top:10px; */
			padding-top:10px;
			/* border: 1px solid red; */
      font-size: 13px;
      /* float: left !important;
      text-align: left; */
		}
		.home-name{
			width: 237px;
			margin: 5px;
			padding: 12px;
		 	font-size: 13px;
       font-weight: 500;
       text-align: right;
		}
		.away-name{
			/* width: 220px; */
			margin: 5px;
			padding: 12px;
		 	font-size: 13px;
		 	font-weight: 500;
		}
		.home-score{
      width: 40px;
      height: 40px;
			margin: 5px;
			padding: 8px;
			/* box-shadow: var(--box-shadow-1);*/
      border-radius: 8px; 
      border: 1px solid #efefef;
      background-color: #efefef;
		 	font-size: 13px;
		 	font-weight: 600;
		 	text-align: center;
		}
		.away-score{
      width: 40px;
      height: 40px;
			margin: 5px;
			padding: 8px;
			/* box-shadow: var(--box-shadow-1);*/
      border-radius: 8px; 
      border: 1px solid #efefef;
      background-color: #efefef;
		 	font-size: 13px;
		 	font-weight: 600;
		 	text-align: center;
    }
    .infos-game{
      
			margin-top: 10px;
      /* padding: 12px; */
      padding-left: 10px;
      padding-right: 10px;
      /* background-color: red; */
      width: 50px;
			/* box-shadow: var(--box-shadow-1);
      border-radius: 8px; */
      font-size: 20px;
		 	font-weight: 500;
		 	text-align: center;
       color:#9c27b0;
    }

    .list-game-ctn{
        min-height: 580px;
    }
    </style>