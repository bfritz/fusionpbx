
(function($) {
 
$.fn.addOption = function()
{
	var a = arguments;
	if(a.length == 0) return this;
	// select option when added? default is true
	var sO = true;
	// multiple items
	var m = false;
	if(typeof a[0] == "object")
	{
		m = true;
		var items = a[0];
	}
	if(a.length >= 2)
	{
		if(typeof a[1] == "boolean") sO = a[1];
		else if(typeof a[2] == "boolean") sO = a[2];
		if(!m)
		{
			var v = a[0];
			var t = a[1];
		}
		
	}
	this.each(
		function()
		{
			
			if(this.nodeName.toLowerCase() != "select") return;
			
			if(m)
			{
				for(var i in items)
				{
					$(this).addOption(i, items[i], sO);
				}
			}
			else
			{
				
				var option = document.createElement("option");
				option.value = v;
				option.text = t;
				var i;
				var r = false;
				// get options
				var o = this.options;
				// get number of options
				var oL = o.length;
				// loop through existing options
				for(i = 0; i < oL; i++)
				{
					// replace existing option
					if(o[i].value == option.value)
					{
						r = true;
						break;
					}
				}
				if(i < oL && !r) i = oL;
				this.options[i] = option;
				
				if(sO)
				{
					o[i].selected = true;
				}
			}
		}
	)
	return this;
}

$.fn.ajaxAddOption = function(url, params, select, fn, args)
{
	if(typeof url != "string") return this;
	if(typeof params != "object") params = {};
	if(typeof select != "boolean") select = true;
	this.each(
		function()
		{
			var el = this;
			$.getJSON(url,
				params,
				function(r)
				{
					$(el).addOption(r, select);
					if (typeof fn == "function")
					{
						if (typeof args == "object")
						{
							fn.apply(el, args);
						} 
						else
						{
							fn.call(el);
						}
					}
				}
			);
		}
	)
	return this;
}

$.fn.removeOption = function()
{
	var a = arguments;
	if(a.length == 0) return this;
	var ta = typeof a[0];
	if(ta == "string") var v = a[0];
	else if(ta == "object" || ta == "function") var v = a[0]; /* regular expression */
	else if(ta == "number") var i = a[0];
	else return this;
	this.each(
		function()
		{
			if(this.nodeName.toLowerCase() != "select") return;
			if(v)
			{
				// get options
				var o = this.options;
				// get number of options
				var oL = o.length;
				for(var i=oL-1; i>=0; i--)
				{
					if(v.constructor == RegExp)
					{
						if (o[i].value.match(v))
						{
							o[i] = null;
						}
					}
					else if(o[i].value == v)
					{
						o[i] = null;
					}
				}
			}
			else
			{
				this.remove(i);
			}
		}
	)
	return this;
}

$.fn.sortOptions = function(ascending)
{
	var a = typeof ascending == "undefined" ? true : ascending;
	this.each(
		function()
		{
			if(this.nodeName.toLowerCase() != "select") return;
			
			// get options
			var o = this.options;
			// get number of options
			var oL = o.length;
			// create an array for sorting
			var sA = [];
			// loop through options, adding to sort array
			for(var i = 0; i<oL; i++)
			{
				sA[i] =
				{
					v: o[i].value,
					t: o[i].text
				};
			}
			// sort items in array
			sA.sort(
				function(o1, o2)
				{
					// option text is made lowercase for case insensitive sorting
					o1t = o1.t.toLowerCase();
					o2t = o2.t.toLowerCase();
					// if options are the same, no sorting is needed
					if(o1t == o2t) return 0;
					if(a)
					{
						return o1t < o2t ? -1 : 1;
					}
					else
					{
						return o1t > o2t ? -1 : 1;
					}
				}
			);
			// change the options to match the sort array
			for(var i = 0; i<oL; i++)
			{
				o[i].text = sA[i].t;
				o[i].value = sA[i].v;
			}
		}
	)
	return this;
}

$.fn.selectOptions = function(value, clear)
{
	var v = value;
	var vT = typeof value;
	var c = clear || false;
	// has to be a string or regular expression (object in IE, function in Firefox)
	if(vT != "string" && vT != "function" && vT != "object") return this;
	this.each(
		function()
		{
			if(this.nodeName.toLowerCase() != "select") return this;
			
			// get options
			var o = this.options;
			// get number of options
			var oL = o.length;
			
			for(var i = 0; i<oL; i++)
			{
				if(v.constructor == RegExp)
				{
					if (o[i].value.match(v))
					{
						o[i].selected = true;
					}
					else if(c)
					{
						o[i].selected = false;
					}
				}
				else
				{
					if (o[i].value == v)
					{
						o[i].selected = true;
					}
					else if(c)
					{
						o[i].selected = false;
					}
				}
			}
		}
	)
	return this;
}


$.fn.copyOptions = function(to, which)
{
	var w = which || "selected";
	if($(to).size() == 0) return this;
	this.each(
		function()
		{
			if(this.nodeName.toLowerCase() != "select") return this;
			
			// get options
			var o = this.options;
			// get number of options
			var oL = o.length;
			
			for(var i = 0; i<oL; i++)
			{
				if(w == "all" ||
					(w == "selected" && o[i].selected)
					)
				{
					$(to).addOption(o[i].value, o[i].text);
				}
			}
		}
	)
	return this;
}


$.fn.containsOption = function(value, fn)
{
	var found = false;
	var v = value;
	var vT = typeof value;
	var fT = typeof fn;
	// has to be a string or regular expression (object in IE, function in Firefox)
	if(vT != "string" && vT != "function" && vT != "object") return fT == "function" ? this: found;
	this.each(
		function()
		{
			if(this.nodeName.toLowerCase() != "select") return this;
			// option already found
			if(found && fT != "function") return false;
			
			// get options
			var o = this.options;
			// get number of options
			var oL = o.length;
			
			for(var i = 0; i<oL; i++)
			{
				if(v.constructor == RegExp)
				{
					if (o[i].value.match(v))
					{
						found = true;
						if(fT == "function") fn.call(o[i]);
					}
				}
				else
				{
					if (o[i].value == v)
					{
						found = true;
						if(fT == "function") fn.call(o[i]);
					}
				}
			}
		}
	)
	return fT == "function" ? this : found;
}

})(jQuery);