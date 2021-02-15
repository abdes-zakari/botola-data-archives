<script>
    import { onMount,afterUpdate  } from "svelte";
    import Chart from 'chart.js';
    import {chartDoughnut,chartPie,processChartStandingPage} from './js/Charts';
    import Loader from './Loader.svelte'
    import SvgImage from './SvgImage.svelte'

    let standing = [];
    let teamsList = [];
    let selectedStage = null;
    let stages = new Array(30);
    let currentTeamChart;
    let dataCurrentTeamChart = [];
    let myCharta ;
    let allGoals = 0;
    let allGames = 0;
    let draws = 0;
    let winLos = 0;
    let getStats = {
      yellowCards:0,
      redCards:0,
      penos:0,
      missedPenos:0
    };

  	const fetchGames = async () => {
		// let response = await fetch("http://127.0.0.1:9000/standing")//python
    let response = await fetch("http://localhost:8000/api/v1/standing")//lumen
        standing = await response.json()
        // standing = getStanding(response)
        standing.forEach(element => {
                // console.log(element)
                // allGoals
                allGoals = allGoals + element.gf;
                draws = draws + element.draw
        });
        allGames = 240;
        draws = draws / 2;
        winLos = 240 - draws;
        draws = parseInt((draws*100)/allGames);
        winLos = parseInt((winLos*100)/allGames);

    // let stats = await fetch("http://localhost:8000/api/v1/stats")//lumen
    //     getStats = await stats.json()
        // await console.log(getStats);
		// await console.log(typeof standing);
    }

    const fetchTeams = async () => {

      let response = await fetch("http://localhost:8000/api/v1/teams")//lumen
          teamsList = await response.json()
          // standing = getStanding(response)
      // await console.log(typeof teamsList);
    }

    const fetchGamesByStage = async (stage) => {
        // console.log(typeof stage);
        standing = [];
        allGoals = 0;
        draws = 0;
        if(typeof stage == 'number'){
            // let response = await fetch("http://127.0.0.1:9000/standing/stage/"+stage)//python
            let response = await fetch("http://localhost:8000/api/v1/standing/stage/"+stage)//lumen
            standing = await response.json()
            standing.forEach(element => {
                // console.log(element)
                // allGoals
                allGoals = allGoals + element.gf;
                draws = draws + element.draw
            });
            
            allGames = 8 * stage
            draws = draws / 2;
            draws = parseInt((draws*100)/allGames);

            let stats = await fetch("http://localhost:8000/api/v1/stats/stage/"+stage)//lumen
                getStats = await stats.json()
        }

        if(stage=='all') fetchGames();
        // standing = getStanding(response)
		// console.log(response);
    }

    const fetchPositionChart = async (team_id) => {
		// let response = await fetch("http://127.0.0.1:9000/standing/positions/864")//python
    let response = await fetch("http://localhost:8000/api/v1/standing/positions/"+team_id)//lumen
        dataCurrentTeamChart = await response.json()
        console.log(dataCurrentTeamChart);
        return dataCurrentTeamChart
        // standing = getStanding(response)
		
    }

// charts Processs

// [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30] //labels
//[0,1,2,3,1,3,2,4,5,1,1,1,1,5,1,1,1,11,1,1,2,1,3,4,1,4,5,6,7,1,2] // data
onMount (async () => {
      // Graphs
  //     setTimeout(async()=>{
    await fetchGames();
    await fetchTeams();
console.log('ON-MOUNT')      
  dataCurrentTeamChart = await fetchPositionChart(currentTeamChart);

   myCharta = new Chart(document.getElementById('myChart'), {
    type: 'line',
    data: {
      labels: [ ...Array(31).keys() ].map( i => i),
      datasets: [{
        data: dataCurrentTeamChart,
        lineTension: 0,
        backgroundColor: 'transparent',
        borderColor: '#990099',
        borderWidth: 3,
        pointBackgroundColor: '#990099',
        pointBorderWidth:0.2,
        pointRadius:4,
        pointRotation:180
      }]
    },
    options: {
      scales: {
        xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Journey'
						}
				}],
        yAxes: [{
          ticks: {
            beginAtZero: false,
            reverse: true,
            min:1,
            stepSize: 1,
            max:16,
            callback: function(value, index, values) {
                        return 'P' + value;
                    }
          },
           scaleLabel: {
            display: true,
            labelString: 'Positions'
          }
        }]
      },
      legend: {
        display: false
      },
      tooltips: {
            callbacks: {
                title: function(tooltipItem, data) {
                   return 'Journey: ' + tooltipItem[0].xLabel;
                },
                label: function(tooltipItem, data) {
                    return 'Position: ' + tooltipItem.yLabel 
                }
            }
      },
      title: {
					display: true,
					text: 'Position by Journey'
			},
    }
  })


// getting Stats and charts Processs

 let stats = await fetch("http://localhost:8000/api/v1/stats")//lumen
     getStats = await stats.json()

processChartStandingPage(getStats) // goal Charts

//chart penalties 
chartDoughnut('penalties-chart',{
    label:[getStats.penos-getStats.missedPenos+" Scored", getStats.missedPenos+" Missed"],
    data : [getStats.penos-getStats.missedPenos,getStats.missedPenos],
    bgColor: ["#8e5ea2","#c45850"],
    titleTxt : 'Total Penalties:'+getStats.penos
})
//chart cards 
chartDoughnut('cards-chart',{
    label:[getStats.yellowCards+" Yellow cards", getStats.redCards+" Red Cards"],
    data : [getStats.yellowCards,getStats.redCards],
    bgColor: ["#ffc400","#b71c1c"],
    titleTxt : 'Total Cards:'+parseInt(getStats.yellowCards+getStats.redCards)
})
//chart Draw/Win/Los
chartPie('draw-win-los-chart',{
    label:[draws+"% Draw", winLos+"% Win/Los"],
    data : [draws,winLos],
    bgColor: ["#b388ff","#311b92"],
    titleTxt : 'Played:'+parseInt(240)
})

  // },2000)

// console.log(myCharta)

});

  const refreshChart = async (team_id) => {
      // currentTeamChart = team_id
      if(typeof team_id == 'number'){


        dataCurrentTeamChart = await fetchPositionChart(team_id);
      
        // var ctx = document.getElementById('myChart')
        console.log(myCharta)
        myCharta.data.datasets[0].data = dataCurrentTeamChart;
        myCharta.update();
      }
    };

</script>

<div id="standing-ctn">
     <h2> Standing </h2>
    <div class="grid-container">
      <div class="grid-item">Games: {allGames}</div>
      <div class="grid-item">Goals: {allGoals}  </div>
      <div class="grid-item">Draws: {draws} % </div> 
      <div class="grid-item">Yellow <SvgImage img="yellow" width="20" height="20"/>: {getStats.yellowCards}  </div>
      <div class="grid-item">Red <SvgImage img="red" width="20" height="20"/>: {getStats.redCards}  </div>
      <div class="grid-item">
        Penalties: {getStats.penos}
        
        / <SvgImage img="goal" width="20" height="20" title="scored"/>: { getStats.penos-getStats.missedPenos} / <SvgImage img="missed" width="20" height="20" title="missed"/>: {getStats.missedPenos}
       </div>  
    </div>
<br>
  <div class="ctn-box">
    <div class="form-group row">
			<label for="stage" class="col-md-2 col-form-label offset-md-4">Standing by Day</label>
			<div class="col-md-1">
			
				<select bind:value={selectedStage}  on:change="{() => fetchGamesByStage(selectedStage)}" class="form-control" id="stage">
					<option value="all">All</option>
                    { #each stages as s, i }
					<option value="{i+1}">{i+1}</option>
					{/each}
				</select>
			</div>
		</div>
      <table class="table table-sm">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Team</th>
            <th scope="col">P</th>
            <th scope="col">W</th>
            <th scope="col">D</th>
            <th scope="col">L</th>
            <th scope="col">GF</th>
            <th scope="col">GA</th>
            <th scope="col">GD</th>
            <th scope="col">Pts</th>
          </tr>
        </thead>
        <tbody>
          {#each standing as s,k}
              <tr>
              <th scope="row">{k+1}</th>
              <td class="td-name"><a href={"team/"+s.id}> {s.name_full} </a></td>
              <!-- <td><a href={"http://127.0.0.1:9000/standing/positions/"+s.id}> {s.name} </a></td> -->
              <td>{s.played}</td>
              <td>{s.win}</td>
              <td>{s.draw}</td>
              <td>{s.los}</td>
              <td>{s.gf}</td>
              <td>{s.ga}</td>
              <td>{s.gd}</td>
              <th scope="row">{s.pts}</th>
              </tr>
          {/each}
        </tbody>
      </table>
    </div>
<Loader show={standing.length>0} />


<!-- <button on:click={renderChart}>Load</button> -->
<!-- <canvas id="myChart"></canvas> -->


<div class="row">
  <div class="col-md-4">
    <div class="ctn-box">
      <canvas id="penalties-chart" width="400" height="300"></canvas> 
    </div>
  </div>
  <div class="col-md-4">
    <div class="ctn-box">
      <canvas id="cards-chart" width="400" height="300"></canvas> 
    </div>
  </div>
  <div class="col-md-4"> 
    <div class="ctn-box">
      <canvas id="draw-win-los-chart" width="400" height="300"></canvas> 
    </div>
  </div>
  
</div>





<div class="form-group row mt-4">
			<label for="stage" class="col-md-2 col-form-label offset-md-4">Choose a Team</label>
			<div class="col-md-2">
				<select bind:value={currentTeamChart}  on:change="{() => refreshChart(currentTeamChart)}" class="form-control" id="team_id">
          <option value="all">--</option>
          { #each teamsList as team, i }
            <option value="{team.id}">{team.name}</option>
          {/each}
        </select>
			</div>
</div>




<div class="ctn-box">
  <canvas id="myChart" width="400" height="200"></canvas>
</div>

<div class="ctn-box">
  <canvas id="chart-goals-by-stage" width="400" height="200"></canvas>
</div>

<div class="ctn-box">
  <canvas id="chart-goals-by-minutes" width="400" height="200"></canvas>
</div>

<!-- <canvas id="penalties-chart" width="400" height="200"></canvas> -->
</div>



<style>
		#standing-ctn{
			/* background-color: #FFF;
			box-shadow: var(--box-shadow-2);
			border-radius: 8px; */
			/* text-align: center; */
		}
        select{
      text-transform: uppercase;
    }
    .grid-container {
  display: grid;
  grid-template-columns: auto auto auto auto auto auto;
  background: linear-gradient(60deg,#ab47bc,#8e24aa);
  padding: 10px;
  box-shadow: 0 4px 20px 0 rgba(0,0,0,.14), 0 7px 10px -5px rgba(156,39,176,.4);
}
.grid-item {
  /* border: 1px solid rgba(0, 0, 0, 0.8); */
  padding: 20px;
  font-size: 15px;
  text-align: center;
  font-weight: 500;
  color:#FFF;
}

thead, .td-name{
  color: #990099;
  font-weight: 500;
}
a {
    color:#000;
}

a:visited {
    color:#000;
}
a:hover {
    color:#990099;
    text-decoration: none;
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
</style>