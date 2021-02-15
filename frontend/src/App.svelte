<script>
	// import Router from 'svelte-spa-router'
	import Button from './Button.svelte'
	import GamesStage from './GamesStage.svelte'
	import Standing from './Standing.svelte'
	// import Navbar from './Navbar.svelte'
	// import Sidebar from './Sidebar.svelte'
	import { Router } from '@sveltech/routify';
  	import { routes } from '@sveltech/routify/tmp/routes';

	// import {jsonFetch} from './jsonFetch.js'
	export let name;


	// let date = dayjs.unix(1560096240)
	// console.log(date.format('DD-MM-YYYY HH:mm'));
// 	const routes = {
//     // Exact path
//     '/': GamesStage,
//     // Wildcard parameter
//     '/standing': Standing,
// };
</script>





<!-- <Navbar></Navbar>
<Sidebar></Sidebar> -->
<div id="container">
	<Router {routes}/>
</div>


	



<style>

		body {
		  background: #f3f2f2;
		  font-family: 'Fira Code' ;
		}

		#container{
			/* width: 800px; */
			padding:70px;
			padding-left: 300px;
			/* margin: auto; */
			/* text-align: center; */
		}
	
</style>