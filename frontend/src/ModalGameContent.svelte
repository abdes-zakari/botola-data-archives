<script>
import Loader from './Loader.svelte'
import { parseDate}  from './js/Helpers';
import SvgImage from './SvgImage.svelte'
export let gameId;
       let game = {};

   const getGames = async (id) => {
     let response = await fetch("http://localhost:8000/api/v1/games/"+id)//lumen
         game =  await response.json();
   };

   getGames(gameId);

   const getIconEvent = (type) => {

      if(type =='goal') return "goal"
      if(type =='yellow') return "yellow"
      if(type =='red') return "red"
      if(type =='owngoal') return "owngoal"
      if(type =='penGoal') return "goal"
      if(type =='penMiss') return "missed"
      
      // return "owngoal"

   };
   const  getTitleEvent = (type) => {

      if(type =='goal') return "Goal"
      if(type =='yellow') return "Yellow Card"
      if(type =='red') return "Red Card"
      if(type =='owngoal') return "Own Goal"
      if(type =='penGoal') return "Goal (Penalty) "
      if(type =='penMiss') return "Missed Penalty"
      
      // return "owngoal"

   };
   
</script>


<svelte:head>
 <style>
  body {
    overflow: hidden;
    padding-right: 17px;
  }
  .bg-dark{
    /* background: red !important; */
    margin-right: 17px;
  }
 </style>
</svelte:head>

<!-- <h3 slot="header">Game: Botola</h3> -->

<Loader show={Object.keys(game).length>0} />

{#if Object.keys(game).length>0}

<h3>Botola  <small>- Round {game.stage}</small></h3>
<hr> 
   <div class="ctn-date">
   <!-- <img src="/imgs/goal.png"/> -->
      {parseDate(game.game_date)}
   </div>
   <div class="row">
      <div class="offset-md-2 col-md-3">
         <div class="float-right mt-3">
            {game.home_name}
         </div>
      </div>
      <div class="col-md-2">
         <div class="ctn-score"> {game.home_score} - {game.away_score} </div>
      </div>
      <div class="col-md-3">
      <div class="mt-3">
         {game.away_name}
      </div>
      </div>
   </div>
   <hr>
   {#each game.events as event}
      <div class="row ctn-event">
         <div class="offset-md-1 col-md-4 borda1">
            <div class="float-right mt-3 event-list">
               {#if event.team == "home"}
                  <div class="div-event-name">{event.name} {#if event.type == "penGoal" || event.type == "penMiss"}(P) {/if} </div>
                  <div class="div-event-type"><SvgImage img={getIconEvent(event.type)} width="20" height="20" title={getTitleEvent(event.type)}></SvgImage></div>
                  <div class="div-event-min">{event.min}' </div>
               {/if}
            </div>
         </div>
         <div class="col-md-1 mid-line left-line"> 
            <!-- <div class="mid-line"> sdada</div> -->
            {#if event.team == "home"} <hr> {/if}
         </div>
         <div class="col-md-1 right-line">

         {#if event.team == "away"} <hr> {/if}
         </div>
         <div class="offset-md-0 col-md-4 borda1">
         <div class="mt-3 event-list">
            {#if event.team == "away"}
               <div class="div-event-min">{event.min}' </div>
               <div class="div-event-type"><SvgImage img={getIconEvent(event.type)} width="20" height="20" title={getTitleEvent(event.type)}></SvgImage></div>
               <div class="div-event-name">{event.name} {#if event.type == "penGoal" || event.type == "penMiss" }(P) {/if}</div>
               <!-- {event.min}' <img src={getIconEvent(event.type)}/> {event.name} -->
            {/if}
         </div>
         </div>
      </div>
   {/each}
{/if}



<style>
.ctn-score{
   margin: 5px;
   padding: 8px;
   border-radius: 8px;
   border: 1px solid #efefef;
   background-color: #efefef;
   font-size: 25px;
   font-weight: 600;
   text-align: center;
}

.borda{
   border:1px solid red;

}

.ctn-date{
font-size:14px;
text-align:center;
color:#9c27b0;
font-weight:500;
}

.ctn-event{
   font-size:14px;
}

.div-event-min{
   width:24px;
   /* background:blue; */
   padding:2px;
   text-align:center;
}
.div-event-name{
   /* width:100px; */
   padding:2px;
}
.div-event-type{
   width:20px;
   padding:2px;
   /* background:red; */
}
.event-list{
   display:flex;

}
.mid-line{
   border-right: 2px solid rgba(0,0,0,.3);
   /* height: 40px; */
}
.left-line{
   padding-right:0px !important;
   padding-left:0px !important;
   padding-top:12px;
}

.right-line{
   padding-right:0px !important;
   padding-left:0px !important;
   padding-top:12px;
}

</style>
		




