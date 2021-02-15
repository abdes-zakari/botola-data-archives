<script>
import { AirplayIcon, HomeIcon,ListIcon,ShoppingCartIcon } from 'svelte-feather-icons'
import { isActive, url } from '@sveltech/routify'  

// let currentRoute = "";
 const links = [
    ['/index', 'Dashboard',HomeIcon],
    ['./standing', 'Standing',ListIcon],
    ['./team', 'Test',ShoppingCartIcon]
  ];
  // console.log(isActive)
  // class:active={$isActive("./standing")}

</script>
   <!-- <AirplayIcon size="24" /> -->
    <!-- <AtSignIcon size="1.5x" /> -->


<nav class="col-md-2 d-none d-md-block bg-light sidebar">
      <div class="sidebar-sticky">
        <ul class="nav flex-column">
          {#each links as [path, name,icon]}
            <li class="nav-item">
              <a class="nav-link" href="{$url(path)}" class:active={$isActive(path)}>
              <svelte:component this={icon} size="16" />
                {name} 
              </a>
            </li>
          {/each}
        </ul>

      </div>
    </nav>

 <!-- <nav class="col-md-2 d-none d-md-block bg-light sidebar">
      <div class="sidebar-sticky">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" href="/"  on:click="{() => alert('hola')}">
              <HomeIcon size="16" />
              
              Dashboard 
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/standing">
            <ListIcon size="16" />
              
              Standing
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/club/18">
              <ShoppingCartIcon size="16" />
              
              ClubTest
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/team">
              <ShoppingCartIcon size="16" />
              
              Team
            </a>
          </li>
        </ul>

      </div>
    </nav>  -->