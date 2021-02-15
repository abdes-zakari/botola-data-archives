<script>
	export let show;
</script>

<div style="display: {show ? 'none' : 'inline'}">
	<div class="wrapper-loader" id="loader">
		<div class="loader"></div>
	</div>
</div>


<style>
    .wrapper-loader{
		  text-align:center;
		  padding-top:5%;
		  padding-bottom: 10%;
		}
		.loader{
			display: inline-block;
			width: 1em;
			height: 1em;
			color: inherit;
			vertical-align: middle;
			pointer-events: none;
		  font-size: 30px;
			border: .2em solid #990099;
			border-bottom-color: transparent;
			border-radius: 50%;
			animation: 1s loader-animation linear infinite;
			position: relative;
		}

		@keyframes loader-animation {
			0% {
				transform: rotate(0deg);
			}
			100% {
				transform: rotate(360deg);
			}
    }
    </style>