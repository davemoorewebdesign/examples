// Light: define & declare constructor
function Light(trafficlightBox, colour) {
	this.trafficlightBox = trafficlightBox;
	this.colour = colour;
	this.blinkInterval = null;
	this.isBlinking = false;
}

// Light render() puts html on page
Light.prototype.render = function() {
	// append Light html to trafficlightBox inner html
	$('.trafficlight[data-id="'+this.trafficlightBox.id+'"').append('<div class="trafficlight__light trafficlight__light--'+this.colour+'"></div>\n');
}

// Light update() sets if red is lit, green is lit or red is blinking
Light.prototype.update = function(state) {
	// Locate Light html
	var el =  $('.trafficlight[data-id="'+this.trafficlightBox.id+'"] .trafficlight__light--'+this.colour);
	
	// Check new state for outcome
	if (state == 1) {
		// when state is 1, turn on light
		if (this.isBlinking) {
			// Stop blinking
			clearInterval(this.blinkInterval);
		}
		el.addClass('active');
	} else if (state == 2) {
		// when state is 2, blink light
		if (!this.isBlinking) {
			this.blinkInterval = setInterval(function(){
				el.toggleClass("active");
			}, 500);
			this.isBlinking = true;
		}
	} else {
		// When state is neither 1 or 2, turn off light 
		el.removeClass('active');
	}
}


// TrafficlightBox: define & declare constructor
function TrafficlightBox(id, name, url, frequency) {
	this.id = id;
	this.name = name;
	this.url = url;
	this.frequency = frequency;
	this.timer = 0;
	this.errorCount = 0;
	this.errorTimes = [];
}

// TrafficlightBox render() put html on page and set it up ready to check url
TrafficlightBox.prototype.render = function() {
	// Add TrafficLightBox html
	$('.trafficlights-container').append(' \
	<div class="col col--half"> \
	    <div class="trafficlight-outer"> \
	        <div class="trafficlight" data-id="'+this.id+'" data-url="'+this.url+'" data-time="'+this.frequency+'"></div> \
	    </div> \
	    <div class="trafficlight-details"> \
	        <div class="trafficlight-details-name">'+this.name+'</div> \
			<div class="trafficlight-details-url"><a href="'+this.url+'" target="_blank">'+this.url+'</a></div> \
	        <div class="trafficlight-details-time">Frequency: '+this.frequency+' seconds</div> \
		</div> \
	</div>');
	
	// Render Red Light
	this.redLight = new Light(this, 'red');
	this.redLight.render();
	
	// Render Green Light
	this.greenLight = new Light(this, 'green');
	this.greenLight.render();
	
	// Check http code when html is rendered
	this.checkHttpcode();
	
	// Start regular http code checks
	var self = this;
	window.setInterval(function(){
		self.checkHttpcode();
	}, self.frequency*1000);
}

// TrafficLightbox getHttpcode() gets http code from url and passes to update
TrafficlightBox.prototype.checkHttpcode = function() {
	// Sent Ajax requests to status url with URL
	var self = this;
	var data = {url : this.url};
	var url = this.url;
	var baseUrl = window.location.href.split('?')[0];
	var statusUrl = baseUrl+'?page=status';
	$.ajax({
		type: "POST",
		data: data,
		dataType: "text",
		url: statusUrl,
		success: function(code) {
			self.update(code);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) { 
			self.update(0);
		}
	});
}

// TrafficLightbox update() receives url check outcome and updates lights
TrafficlightBox.prototype.update = function(code) {
	const timeLimit = 120;
	var date = new Date();
	const currentTime = date.getTime() / 1000;
	if (code != 200) {
		// Add new time to errorTimes array
		this.errorTimes.push(currentTime);
		// Remove all but last 3 error times from errorTimes 
		this.errorTimes = this.errorTimes.slice(-1 * 3);
	}
	
	// Check if blink is needed
	var withinCount = 0;
	for (var i = 0; i < this.errorTimes.length; i++) {
		var timeDifference = currentTime - this.errorTimes[i];
		if (timeDifference <= 120) {
			withinCount++;
		}
	}
	switch(withinCount) {
		case 0:
		case 1:
			this.redLight.update(0);
			this.greenLight.update(1);
			break;
		case 3:
			this.redLight.update(2);
			this.greenLight.update(0);
			break;
		default:
			this.redLight.update(1);
			this.greenLight.update(0);
			break;
	}
}

var App = {
	init : function() {
		if (typeof trafficlightsData !== 'undefined') {
			for (var i = 0; i < trafficlightsData.length; i++) {
				var boxData = trafficlightsData[i];
				if (boxData.id && boxData.url) {
					if (!boxData.frequency) {
						boxData.frequency = 10;
					}
					var trafficlightBox = new TrafficlightBox(boxData.id, boxData.name, boxData.url, boxData.frequency);
					trafficlightBox.render();
				}
			}
		}
	}
};


$(document).ready(function() {
	App.init();
});