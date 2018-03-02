<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Ninuno Deck Creator</title>
	<style>
		*{
			margin: 0px;
			padding: 0px;
		}
		#wrapper{
			height: 100vh;
			width: 100%;
			background: #efefef;
		}
		#wrapper_row{
			height: 95vh;
			overflow: hidden;
			vertical-align: top;
			display: -webkit-flex;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: flex;
		}
		#legend_row{
			background: #111;
			box-sizing: border-box;
			color: #fff;
			font-family: Arial, Arial Black, sans;
			height: 5vh;
			padding: 5px 10px;
		}
		#legend_row #deck_card_count.insufficient{
			color: #f00;
		}
		#legend_row #deck_card_count.sufficient{
			color: #05E85C;
		}
		#preview_block{
			background: #333333;
			float: left;
			height: 100vh;
			padding: 0.1in;
			vertical-align: top;
			width: 325px;
		}
		#left_content{
			background: #777777;
			box-sizing: border-box;
			display: inline-block;
			float: left;
			height: 100%;
			overflow-y: auto;
			padding: 55px 0.125in 0.1in;
			vertical-align: top;
			width: 450px;
		}
		#right_content{
			background: #efefef;
			box-sizing: border-box;
			display: inline-block;
			float: left;
			height: 100%;
			overflow-y: auto;
			padding: 0.1in;
			vertical-align: top;
			width: 570px;
			flex-grow: 1;
		}
		#card_search{
			border: 0.1in solid #777;
			box-sizing: border-box;
			display: block;
			font-family: Arial, Arial Black, sans;
			margin-bottom: 0.1in;
			margin-top: -55px;
			outline: none;
			padding: 10px;
			position: fixed;
			width: 410px;
			z-index: 10000;
		}
		#preview_image{
			background: #fff;
			border-radius: 4mm;
			width: 100%;
		}
		.card_image{
			cursor: pointer;
			margin: 0px 0.1in 0.05in 0px;
		}
		#left_content .card_image{
			width: 2in;
		}
		#left_content .card_image.maxed_out{
			opacity: 0.35;
		}
		#right_content .card_image{
			width: 1.75in;
			margin: 0px 0.1in 0px 0px;
		}
		.hidden{
			display: none;
		}
		@media print {
			#wrapper{
				background: #fff;
			}
			#preview_block, #left_content, #legend_row{
				display: none;
			}
			#wrapper_row, #right_content{
				width: 100%;
				padding: 0px;
				background: none;
				overflow: initial;
				height: auto;
			}
			#right_content .card_image{
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
				width: 2.5in;
				height: 3.5in;
				padding: 3px !important;
			}
		}
		@page{
			margin: 3mm 2mm 3mm 4.5mm;
			width: 210mm;
			height: 297mm;
		}
	</style>
</head>
<body id="body">
	<div id="wrapper">
		<div id="wrapper_row">
			<div id="preview_block">
				<img src="../assets/img/NinunoCardBack.png" alt="Ninuno Card Game" id="preview_image">
			</div>
			<div id="left_content">
				<form>
					<input type="text" id="card_search" placeholder="Search card name">
				</form>
			</div>
			<div id="right_content"></div>
		</div>
		<div id="legend_row">
			<h3>
				<span>Cards in Deck:</span>
				<span id="deck_card_count" class="insufficient">0</span>
				<span>/ 40</span>
			</h3>
		</div>
	</div>
<script type="text/javascript">
	var card_limit = 3;
	var card_search_items = [];
	var card_items = [];
	var kaganapan_items = [];
	var document_body = document.getElementById("body");
	var left_content_div = document.getElementById("left_content");
	var right_content_div = document.getElementById("right_content");
	var card_search_field = document.getElementById("card_search");

	function loadJSON(callback) {

		var xobj = new XMLHttpRequest();
		xobj.overrideMimeType("application/json");
		xobj.open('GET', '../assets/json/base_set_internationalized.json', true); // Replace 'my_data' with the path to your file
		xobj.onreadystatechange = function () {
			if (xobj.readyState == 4 && xobj.status == "200") {
			// Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
				callback(xobj.responseText);
			}
		};
		xobj.send(null);
	}

	function init() {
		loadJSON(function(response) {
			// Parse JSON string into object
			var cards_JSON = JSON.parse(response);

			for(var card_index in cards_JSON)
			{
				var card_id = (parseInt(card_index) + 1);
				var card_image = create_card_image(card_id, "left_content");

				card_image.addEventListener('click', clone_card);
				card_image.addEventListener('mouseover', preview_card);
				var current_card = cards_JSON[card_index];
				var card_obj = {
					id : card_id,
					name : current_card.Name,
					img : "../assets/img/cards/"+ card_id +".jpg"
				}

				if(current_card.K == true)
				{
					kaganapan_items.push( parseInt(card_index) );
				}

				card_items.push(card_obj);
				card_search_items.push(current_card.Name.toLowerCase());

				if(card_id == cards_JSON.length)
				{
					card_search_field.addEventListener("keyup", search_card);
				}
			}
		});
	}

	function search_card(event) {
		var search_value = this.value;
		console.log(search_value.length, search_value);

		if(search_value.length >= 3)
		{
			var card_ids = get_card_match_id(search_value);

			if(card_ids.length != 0)
			{
				var card_elements = left_content_div.getElementsByClassName("card_image");

				for(var i_id in card_elements)
				{
					var card_classes = card_elements[i_id].className;

					if(typeof card_classes != "undefined")
					{
						if( card_ids.indexOf(i_id + 1) == -1 && card_classes.indexOf(" hidden") == -1)
						{
							card_elements[i_id].className = card_classes +" hidden";
						}
					}
				}
			}
		}
		else
		{
			var card_elements = left_content_div.getElementsByClassName("card_image");

			for(var i_id in card_elements)
			{
				var card_classes = card_elements[i_id].className;

				if(typeof card_classes != "undefined")
				{
					if( card_classes.indexOf(" hidden") > -1)
					{
						card_elements[i_id].className = card_classes.replace(" hidden","");
					}
				}
			}
		}
	}

	function get_card_match_id(search_value)
	{
		var match_array = [];
		for(var search_id in card_search_items)
		{
			var match_string =  new RegExp(search_value.toLowerCase(), 'g');
			var has_match = card_search_items[search_id].match(match_string);

			if( has_match != null )
			{
				match_array.push( search_id + 1 );
			}

			if((search_id + 1) == card_search_items.length)
			{
				return match_array;
			}
		}

		return match_array;
	}

	function clone_card(event)
	{
		var active_section = this.parentNode;
		var section_id = active_section.id;
		var raw_class_name = this.className;
		var strip_card_image_class = raw_class_name.replace("card_image ","");
		var search_class = strip_card_image_class.replace(" maxed_out","");
		var card_id = strip_card_image_class.replace("card_","");

		if(section_id == "left_content")
		{
			var searched_elements = right_content_div.getElementsByClassName(search_class);

			if(searched_elements.length < card_limit)
			{
				var cloned_card = create_card_image(card_id, "right_content");
				cloned_card.addEventListener('click', clone_card);
			}

			if(searched_elements.length >= card_limit && this.className.indexOf("maxed_out") == -1)
			{
				this.className = this.className +" maxed_out";
			}
		}
		else
		{
			active_section.removeChild(this);
			var searched_elements = left_content_div.getElementsByClassName(search_class);

			if(searched_elements.length < card_limit)
			{
				searched_elements[0].className = searched_elements[0].className.replace(" maxed_out","");
			}
		}

		var deck_card_count = document.getElementById("deck_card_count");
		var deck_cards = right_content_div.getElementsByClassName("card_image");
		deck_card_count.innerText = deck_cards.length;

		if(deck_cards.length < 40)
		{
			deck_card_count.className = "insufficient";
		}
		else
		{
			deck_card_count.className = "sufficient";
		}
	}

	function preview_card(event){
		event.stopPropagation();
		var preview_image = document.getElementById("preview_image");
		preview_image.src = this.src;
	}

	function create_card_image(card_id, target_id){
		var card_image = document.createElement("IMG");
		card_image.src = "../assets/img/cards/"+ card_id +".jpg";
		card_image.className = "card_image card_"+ card_id;

		if(typeof target_id == "undefined")
		{
			document_body.appendChild(card_image);
		}
		else
		{
			var target_element = document.getElementById(target_id);
			target_element.appendChild(card_image);
		}

		return card_image;
	}

	init();
</script>

</body>
</html>