// handshake_ticker.js

var Handshake_Ticker = React.createClass({
	getInitialState: function(){
		return {
			handshakes: null,
			ajax_is_running: false
		};
	},

	getNumberOfHandshakes: function(){
		var _this = this;
		$.ajax({
			url: '/getNumberOfHandshakes',
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			//if data can be converted to number, then it's valid and update
			if( parseInt(data) ){
				if( _this.state.handshakes ) _this.checkDiff(data);
				else _this.init(data);
				//allow heartbeat to run again
				_this.state.ajax_is_running = !1;
			}
		});	
	},

	componentDidMount: function(){
		var _this = this;

        // Disable handshake ticker.

		//get initial number of handshakes
		// this.getNumberOfHandshakes();

		// //then every 10 sec get possibly new number of handshakes
		// setInterval(function(){
		// 	if( !_this.state.ajax_is_running ){
		// 		_this.getNumberOfHandshakes();
		// 		//disallow heartbeat from sending again while this request is running
		// 		_this.state.ajax_is_running = !0;
		// 	}
		// }, 1000);
	},

	//init handshake state
	init: function(data){
		this.setState({handshakes: data.split('')});
	},

	//check if current number of handshakes is less than the newly acquired number of handshakes 
	//if so, update counter, else do nothing
	checkDiff: function(data){
		var _this = this, updatedCount_str = data.split(''), updatedCount_int = parseInt(data),
			currentCount_str = this.state.handshakes.slice(),
			currentCount_int = parseInt(this.state.handshakes.slice().join('')),
			updated_indexes = [], i, k, diff;

		//if both current and updated converted to integer is valid, then proceed
		if( updatedCount_int && currentCount_int ){

			//if updatedCount jumped up another 10s place or more, then adjust currentCount length w/temp data so the two arrays are equal in length
			if( updatedCount_str.length > currentCount_str.length ){
				diff = updatedCount_str.length - currentCount_str.length;
				for (k = 0; k < diff; k++){
					currentCount_str.splice(0, 0, '1');
				}
			}

			//compare updated to current count to see which index has been updated and save the index(s)
			if( updatedCount_int !== currentCount_int ){
				for(i = 0; i < updatedCount_str.length; i++){
					if( currentCount_str[i] !== updatedCount_str[i] ){
						currentCount_str[i] = updatedCount_str[i];
						updated_indexes.push(i);
					}
				}
				//update state and update view w/flip animation
				_this.update(updated_indexes, updatedCount_str);
				_this.state.handshakes = currentCount_str;
			}

		}
	},

	//loop through the updated indexes and flip those tickers
	update: function(updated_indexes, updated_vals){
		var _this = this, i, handshakeCount = _this.state.handshakes.slice();

		if( updated_indexes && updated_indexes.length > 0 ){
			for( i = 0; i < updated_indexes.length; i++ ){
				_this.flip('ticker-'+updated_indexes[i], handshakeCount[updated_indexes[i]], updated_vals[updated_indexes[i]]);
			}
		}
	},

	//flip animation on the passed ticker and flip to specific card val
	flip: function(ticker, prev_val, next_val){
	    var prev_card = $('ul.'+ticker+' li[data-card-val="'+prev_val+'"]'),
	    	next_card = $('ul.'+ticker+' li[data-card-val="'+next_val+'"]');

	    //remove play class from ticker container
		$('.handshake-ticker-container').removeClass('play');

		//remove all before classes
		$('ul.'+ticker+' li').removeClass('before');
		//then reapply before to prev card
		prev_card.addClass('before').removeClass('active');
		//then add active to next card, then add play to container to start animation
		next_card.addClass('active').closest('.handshake-ticker-container').addClass('play');
	},

	render: function(){
		var _this = this;
		return (
			<div className="handshake-ticker-container play clearfix">
			{
				_this.state.handshakes ? _this.state.handshakes.map(function(value, index){
					return <Ticker key={index} val={value} indx={index} />
				}) : null
			}
			</div>
		);
	}
});

var Ticker = React.createClass({
	render: function(){
		var ticker_cards = [], classes = 'flip ticker-'+this.props.indx, num = this.props.val;

		//generate 10 cards for each ticker
		for( var i = 0; i < 10; i++ ){
			if( num === 10 ) num = 0;
			ticker_cards.push( <Card key={i} number={num} /> );
			num++;
		}

		return (
			<ul className={classes}>
				{ticker_cards}
			</ul>
		);
	}
});

var Card = React.createClass({
	prevent: function(e){
		e.preventDefault();
	},

	render: function(){
		return (
			<li data-card-val={this.props.number}>
				<a href="" onClick={this.prevent}>
	                <div className="up">
	                    <div className="shadow"></div>
	                    <div className="inn">{this.props.number}</div>
	                </div>
	                <div className="down">
	                    <div className="shadow"></div>
	                    <div className="inn">{this.props.number}</div>
	                </div>
	            </a>
			</li>
		);
	}
});

React.render( <Handshake_Ticker />, document.getElementById('handshake-ticker-component'));
