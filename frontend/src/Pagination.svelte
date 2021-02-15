<script>
import { createEventDispatcher } from 'svelte';
const dispatch = createEventDispatcher();
export let data;
var current = data.current_page;
let dada = 1;

console.log(' PAGINATIOOOOOOOOOOOO')
console.log(data);

    function loadPage(page) {
        // console.log(current)
        if(page>0 && page <= data.last_page && current != page){
          current = page
          dispatch('clickPaginate', {
              currentpage: current
          });
        }
        
        
        // allo(page);
    }


</script>


<!-- <button on:click={allo}>
    Click to say hello
</button> -->

<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item">
      <a class="page-link" href="javascript:void(0)" on:click={() => loadPage(current-1)} tabindex="-1">Prev</a>
    </li>
    {#each Array(data.last_page) as _, key}
        <li class="page-item" class:active="{current === key+1}"><a class="page-link" href="javascript:void(0)"  on:click={() => loadPage(key+1)}>{key+1}</a></li>  
    {/each}
    <li class="page-item">
      <a class="page-link" href="javascript:void(0)" on:click={() => loadPage(current+1)}>Next</a>
    </li>
  </ul>
</nav>



<!-- <nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled">
      <a class="page-link" href="#" tabindex="-1">Previous1</a>
    </li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item active">
      <a class="page-link" href="#">2 <span class="sr-only">(current)</span></a>
    </li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item">
      <a class="page-link" href="#">Next</a>
    </li>
  </ul>
</nav> -->

<style>
.page-link{
  cursor: pointer;
  color: #9c27b0;
}



.page-item.active .page-link {
    z-index: 3;
    color: #fff;
     background-color:#9c27b0;
    border-color:#9c27b0;
}

</style>