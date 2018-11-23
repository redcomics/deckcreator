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
				console.log(kaganapan_items);
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