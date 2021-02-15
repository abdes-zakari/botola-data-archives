<script>
    let standing = [];
    let teamsList = [];
	const fetchGames = async () => {
		let response = await fetch("http://127.0.0.1:9000/games")
        response = await response.json()
        standing = getStanding(response)
		// console.log(response);
    }
    const fetchGames2 = async () => {
		let response = await fetch("http://127.0.0.1:9000/games2")
        response = await response.json()
        // standing = getStanding(response)
		console.log(response);
    }
    const getTeams = (games) => {
        for (let i = 0; i < 30; i++) {
            if(!teamsList.some(t => t.name === games[i].home_name)){
                let team = {
                    name: games[i].home_name,
                    pts:0,
                    gf:0,
                    ga:0,
                    gd:0
                }
                teamsList.push(team);
            }
            
        }
        // games.forEach(game => {
        //     let team = {
        //         name
        //     }
        // })
    }

    const getStanding = (games) => {
        getTeams(games);
        console.log(teamsList);
        for (let i = 0; i < games.length; i++) {
            // const element = games[i];
            // console.log(games[i]);
            let game = games[i]
            if(games[i].home_score > games[i].away_score){
                teamsList.forEach(team => {
                    if(team.name == games[i].home_name) team.pts = team.pts+3;
                    if(team.name == games[i].away_name) team.pts = team.pts+0;
                })
                games[i].home_pts = 3;
                games[i].away_pts = 0;
            }
            if(games[i].home_score < games[i].away_score){
                teamsList.forEach(team => {
                    if(team.name == games[i].home_name) team.pts = team.pts+0;
                    if(team.name == games[i].away_name) team.pts = team.pts+3;
                })
                games[i].home_pts = 0;
                games[i].away_pts = 3;
            }
            if(games[i].home_score == games[i].away_score){
                teamsList.forEach(team => {
                    if(team.name == games[i].home_name) team.pts = team.pts+1;
                    if(team.name == games[i].away_name) team.pts = team.pts+1;
                })
                games[i].home_pts = 1;
                games[i].away_pts = 1;
            }
            
            teamsList.forEach(team => {
                if(team.name == games[i].home_name){
                    team.gf = team.gf+games[i].home_score
                    team.ga = team.ga+games[i].away_score
                }
                if(team.name == games[i].away_name){
                    team.gf = team.gf+games[i].away_score
                    team.ga = team.ga+games[i].home_score
                }
                
            })

            // console.log(games[i]);
        }
        // console.log(games);
        teamsList = teamsList.sort(compareValues('pts', 'desc'))
        console.log(teamsList);
    }

    function compareValues(key, order = 'asc') {
        return function innerSort(a, b) {
            if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
            // property doesn't exist on either object
            return 0;
            }

            const varA = (typeof a[key] === 'string')
            ? a[key].toUpperCase() : a[key];
            const varB = (typeof b[key] === 'string')
            ? b[key].toUpperCase() : b[key];

            let comparison = 0;
            if (varA > varB) {
            comparison = 1;
            } else if (varA < varB) {
            comparison = -1;
            }
            return (
            (order === 'desc') ? (comparison * -1) : comparison
            );
        };
    }
    
    // fetchGames();
    fetchGames2();

</script>

<div id="standing-ctn">
    Standing <br>
    <table class="table table-sm">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Team</th>
      <th scope="col">GF</th>
      <th scope="col">GA</th>
      <th scope="col">GD</th>
      <th scope="col">Pts</th>
      <th scope="col">-</th>
    </tr>
  </thead>
  <tbody>
    {#each teamsList as t,k}
        <tr>
        <th scope="row">{k+1}</th>
        <td>{t.name}</td>
        <td>{t.gf}</td>
        <td>{t.ga}</td>
        <td>{t.gf-t.ga}</td>
        <td>{t.pts}</td>
        <td>-</td>
        </tr>
    {/each}
  </tbody>
</table>
</div>



<style>
		#standing-ctn{
			/* background-color: #FFF;
			box-shadow: var(--box-shadow-2);
			border-radius: 8px; */
			/* text-align: center; */
		}
</style>