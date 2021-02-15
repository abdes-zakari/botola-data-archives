<script>
import Navbar from '../Navbar.svelte'
import Sidebar from '../Sidebar.svelte'


</script>

<svelte:head>
 <style>
 
 body {
    background:#f2f6ff;
  }
 </style>
</svelte:head>

<Navbar></Navbar>
<Sidebar></Sidebar>

<slot></slot> <!-- Router Content detected automatically by routify-->