<script>
  import { createEventDispatcher, onDestroy } from 'svelte';
  import { fade } from 'svelte/transition';

	const dispatch = createEventDispatcher();
	const close = () => dispatch('close');

	let modal;

	const handle_keydown = e => {
		if (e.key === 'Escape') {
			close();
			return;
		}

		if (e.key === 'Tab') {
			// trap focus
			const nodes = modal.querySelectorAll('*');
			const tabbable = Array.from(nodes).filter(n => n.tabIndex >= 0);

			let index = tabbable.indexOf(document.activeElement);
			if (index === -1 && e.shiftKey) index = 0;

			index += tabbable.length + (e.shiftKey ? -1 : 1);
			index %= tabbable.length;

			tabbable[index].focus();
			e.preventDefault();
		}
	};

	const previously_focused = typeof document !== 'undefined' && document.activeElement;

	if (previously_focused) {
		onDestroy(() => {
			previously_focused.focus();
		});
	}
</script>

<svelte:window on:keydown={handle_keydown}/>

<div class="modal-background" on:click={close} transition:fade={{duration:200}}></div>

<div class="modal-svelte"  transition:fade={{duration:200}} role="dialog" aria-modal="true" bind:this={modal}>
	<!-- <slot name="header"></slot> -->
	<!-- <hr> -->
	<slot></slot>
	<hr>

	<!-- svelte-ignore a11y-autofocus -->
	<button class="float-right" autofocus on:click={close}>close</button>
</div>

<style>
	.modal-background {
    z-index: 101;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
    background: rgba(0, 0, 0, 0.5);
		/* background: rgba(0,0,0,0.3); */
	}

	.modal-svelte {
    z-index: 1000;
		position: fixed;
		left: 50%;
		top: 50%;
		width: calc(60vw - 4em);
		/* max-width: 32em; */
		max-height: calc(100vh - 4em);
		overflow: auto;
		transform: translate(-50%,-50%);
		padding: 1em;
		border-radius: 0.2em;
		background: white;
	}

  

	button {
		display: block;
	}

  @-webkit-keyframes fade-in {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
  @keyframes fade-in {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
  .fade-in {
	  -webkit-animation: fade-in 1.2s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
	          animation: fade-in 1.2s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
  }
</style>
