<script>
export let img;
export let width;
export let height;
export let title;



let urlImg = "/images/svg/game-events.svg#"+img;

width =  width!=null ? width : 24;

height = height!=null ? height : 24;

title = title!=null ? title : "";

// console.log(width);
// console.log(height);
// console.log("SVG  ll")
</script>


<svg class="icon" width={width} height={height}>
    <title>{title}</title>
    <use xlink:href={urlImg}></use>
</svg>