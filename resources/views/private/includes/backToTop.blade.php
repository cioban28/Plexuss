<style>
	.backToTop{
		color: #fff;
		text-align: center;
		background-color: #0085b2;
		position: fixed;
		width: 100%;
		border-top-left-radius: 2px;
		border-top-right-radius: 2px;
		left: 50%;
		margin-left: -75px;
		width: 150px;
		display: none;
		bottom: 0;
		z-index: 10;
		cursor: pointer;
	}

	.backToTop a{
		text-decoration: none;
		color: #fff;
	}

	.backToTop span{
		padding: 0.35em 1em;
	}

	#bttRight{
		display: inline-block;
		float: right;
		height: 18px;
		width: 18px;
		transform: rotate(270deg);
		font-size: 1.5em;
		line-height: 0.5em;
		text-align: center;
		position: relative;
		margin-top: 10px;
		margin-right: 10px;
	}

	@media (max-width: 40em){
		.backToTop{
			width: 100%;
			left: auto;
			margin: auto;
			background-color: #f5f5f5;
			color: #797979;
			text-align: left;
			border-top: 2px solid #797979;
			border-bottom: 2px solid #797979;
			border-radius: 0;
			position: fixed;
			padding: 0.5em 0;
		}
	}
}
</style>
	<div class='backToTop'>
		<span class='hide-for-small-only'>
			Back To Top &#x25B2;
		</span>
		<div class='row show-for-small-only'>
			<div class='small-8 column'>
				<span id='bttLeft'>
					Back To Top
				</span>
			</div>
				<span id='bttRight'>
					&#187;
				</span>

				<div class='small-1 column no-padding'>

			</div>
		</div>
	</div>
