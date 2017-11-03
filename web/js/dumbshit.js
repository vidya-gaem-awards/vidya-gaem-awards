/*
	* Copyright (c) 2009 George Mandis (georgemandis.com, snaptortoise.com)
*/


var Dumbshit = function() {
	var dumbshit= {
			addEvent:function ( obj, type, fn, ref_obj )
			{
				if (obj.addEventListener)
					obj.addEventListener( type, fn, false );
				else if (obj.attachEvent)
				{
					// IE
					obj["e"+type+fn] = fn;
					obj[type+fn] = function() { obj["e"+type+fn]( window.event,ref_obj ); };
	
					obj.attachEvent( "on"+type, obj[type+fn] );
				}
			},
	        input:"",
	        pattern:"38384040373937396665",
		/*pattern:"38384040373937396665",*/
	        load: function(link) {					
				this.addEvent(document,"keydown", function(e,ref_obj) {											
					if (ref_obj) dumbshit = ref_obj; // IE
					dumbshit.input+= e ? e.keyCode : event.keyCode;
					if (dumbshit.input.length > dumbshit.pattern.length) dumbshit.input = dumbshit.input.substr((dumbshit.input.length - dumbshit.pattern.length));
					if (dumbshit.input == dumbshit.pattern) {
                    dumbshit.code(link);
					dumbshit.input="";
                    }
            	},this);
                      
				},
	        code: function(link) { window.location=link}
	        
	};
	return dumbshit;
};
