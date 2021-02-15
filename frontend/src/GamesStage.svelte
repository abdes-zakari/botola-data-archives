<script>
	// import {jsonFetch} from './jsonFetch.js'
	import Loader from './Loader.svelte';
	import { parseDate }  from './js/Helpers';
	import Modal from './Modal.svelte';
  	import ModalGameContent from './ModalGameContent.svelte';
	
	let games = [];
	let stages = new Array(30);
	let selectedStage = 1;
	let ModalGameId;
	let showModal = false;

	const getGames = async (stage) => {
		// let response = await fetch("http://127.0.0.1:9000/games/stage/"+stage)//python
		games = [];
		let response = await fetch("http://localhost:8000/api/v1/games/stage/"+stage)//lumen
		games = await response.json()
		console.log(games);
	}

	getGames(selectedStage);

	// let date = dayjs.unix(1560096240)
	// console.log(date.format('DD-MM-YYYY HH:mm'));

</script>


	<h3> Games </h3>
	<div id="games-list">
		<!-- <div class="form-group row">
			<label for="stage" class="col-md-1 col-form-label offset-md-5">Stage</label>
			<div class="col-md-1">
			
				<select bind:value={selectedStage}  on:change="{() => getGames(selectedStage)}" class="form-control" id="stage">
					{ #each stages as s, i }
					<option value="{i+1}">{i+1}</option>
					{/each}
				</select>
			</div>
		</div> -->
		<div class="form-group mx-auto d-flex" style="width:210px">
			<div class="p-2">
				<label for="stage" class="col-form-label">Stage</label>
			</div>
			<div class="p-2">
				<select bind:value={selectedStage}  on:change="{() => getGames(selectedStage)}" class="form-control" id="stage">
					{ #each stages as s, i }
						<option value="{i+1}">{i+1}</option>
					{/each}
				</select>
			</div>
		</div>
		
		<!-- {#each games as g}
			<div class="row-ctn">
				<div class="date-game"> {parseDate(g.game_date)} </div>
				<div class="home-name">  {g.home_name} </div>
				<div class="home-score"> {g.home_score}  </div>
				<div class="away-score"> {g.away_score}  </div>
				<div class="away-name">  {g.away_name} </div>
			</div>
		{/each} -->
<div class="row">
    <div class="col-md-12">
      <div class="ctn-box">
	  	<Loader show={games.length>0} />
		<div class="list-game-ctn">
			{#each games as g,key}
				<div class="game-ctn" on:click={()=>{ModalGameId = g.id;showModal = true;}}>
					<div class="date-game"> {parseDate(g.game_date)} </div>
					<div class="home-name">  {g.home_name} </div>
					<div class="home-score"> {g.home_score}  </div>
					<div class="away-score"> {g.away_score}  </div>
					<div class="away-name">  {g.away_name} </div>   
				</div>
			{/each}
		</div>
      </div>
    </div>
</div>

		
	</div>
   
{#if showModal}
	<Modal on:close="{() => showModal = false}">
		<ModalGameContent gameId={ModalGameId} ></ModalGameContent>
	</Modal>
{/if}


<style>
:root {
  			--box-shadow-1: 0px 2px 16px 0 rgba(0,0,0,0.16);
  			--box-shadow-2: 0 10px 25px rgba(0,0,0,.15);
		}


		#games-list{
			/* background-color: #FFF;
			box-shadow: var(--box-shadow-2);
			border-radius: 8px; */
			text-align: center;
		}
		/* #container{
			width: 800px;
			margin: auto;
			text-align: center;
		} */
		#standing-ctn{
			padding-top:30px;
		}
		.row-ctn{
			display: flex;
			justify-content: center;
			text-align: left;

			
		}
		/* .date-game{
			margin: 5px;
			padding: 5px;
			margin-top:10px;
			padding-top:10px;
			font-size: 14px;
		}
		.home-name{
			width: 220px;
			margin: 10px;
			padding: 12px;

		 	box-shadow: var(--box-shadow-1);
		 	border-radius: 8px;
		 	font-size: 13px;
		 	font-weight: 500;
		}
		.away-name{
			width: 220px;
			margin: 10px;
			padding: 12px;
			box-shadow: var(--box-shadow-1);
			border-radius: 8px;
		 	font-size: 13px;
		 	font-weight: 500;
		}
		.home-score{
			width: 50px;
			margin: 10px;
			padding: 12px;
			box-shadow: var(--box-shadow-1);
			border-radius: 8px;
		 	font-size: 13px;
		 	font-weight: 500;
		 	text-align: center;
		}
		.away-score{
			width: 50px;
			margin: 10px;
			padding: 12px;
			box-shadow: var(--box-shadow-1);
			border-radius: 8px;
		 	font-size: 13px;
		 	font-weight: 500;
		 	text-align: center;
		} */
		.ctn-box{
			border-radius: 8px;
			background-color: #fff;
			box-shadow: 0 2px 16px rgba(0,0,0,.06);
			/* margin-top: 15px; */
			-webkit-transition: -webkit-transform 1s;
			transition: -webkit-transform 1s;
			transition: transform 1s;
			transition: transform 1s,-webkit-transform 1s;
			-webkit-transform-style: preserve-3d;
			transform-style: preserve-3d;
			padding:16px;
		}

		.game-ctn{
display: flex;
/* text-align: center; */
margin:auto;
border-bottom: 1px solid #efefef;
width: 870px;
/* background-color: blanchedalmond; */
/* justify-content: center; */

}
.game-ctn:hover{
  background-color: #efefef;
  cursor: pointer;
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
	.borda{
   border:1px solid red;

}
</style>