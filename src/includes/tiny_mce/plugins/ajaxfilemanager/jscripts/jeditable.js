jQuery.fn.editable = function(target, options, callback) {

    /* prevent elem has no properties error */
    if (this.length == 0) { 
        return(this); 
    };
    
    var settings = {
        target   : target,
        name     : 'value',
        id       : 'id',
        type     : 'text',
        width    : 'auto',
        height   : 'auto',
        event    : 'click',
        onblur   : 'cancel',
        loadtype : 'GET'
    };
        
    if(options) {
        jQuery.extend(settings, options);
    };
    
    var callback = callback || function() { };
      
    jQuery(this).attr('title', settings.tooltip);
    jQuery(this)[settings.event](function(e) {
        /* save this to self because this changes when scope changes */
        var self = this;

        /* prevent throwing an exeption if edit field is clicked again */
        if (self.editing) {
            return;
        }
			
        /* figure out how wide and tall we are */
        var width = 
            ('auto' == settings.width)  ? jQuery(self).width()  : settings.width;
        var height = 
            ('auto' == settings.height) ? jQuery(self).height() : settings.height;

        self.editing    = true;
        self.revert     = jQuery(self).html();
        self.innerHTML  = '';

        /* create the form object */
        var f = document.createElement('form');
        
        /* apply css or style or both */
        if (settings.cssclass) {
            if ('inherit' == settings.cssclass) {
                jQuery(f).attr('class', jQuery(self).attr('class'));
            } else {
                jQuery(f).attr('class', settings.cssclass);
            }
        }
        
        if (settings.style) {
            if ('inherit' == settings.style) {
                jQuery(f).attr('style', jQuery(self).attr('style'));
                /* IE needs the second line or display wont be inherited */
                jQuery(f).css('display', jQuery(self).css('display'));                
            } else {
                jQuery(f).attr('style', settings.style);
            }
        }
        
        /*  main input element */
        var i;
        switch (settings.type) {
            case 'textarea':
                i = document.createElement('textarea');
                if (settings.rows) {
                    i.rows = settings.rows;
                } else {
                    jQuery(i).height(height);
                }
                if (settings.cols) {
                    i.cols = settings.cols;
                } else {
                    jQuery(i).width(width);
                }   
                break;
            case 'select':
                i = document.createElement('select');
                break;
            default:
                i = document.createElement('input');
                i.type  = settings.type;
                jQuery(i).width(width);
                jQuery(i).height(height);
                /* https://bugzilla.mozilla.org/show_bug.cgi?id=236791 */
                i.setAttribute('autocomplete','off');
        }

        /* maintain bc with 1.1.1 and earlier versions */        
        if (settings.getload) {
            settings.loadurl    = settings.getload;
            settings.loadtype = 'GET';
        } else if (settings.postload) {
            settings.loadurl    = settings.postload;
            settings.loadtype = 'POST';
        }

        /* set input content via POST, GET, given data or existing value */
        if (settings.loadurl) {
            var data = {};
            data[settings.id] = self.id;
            jQuery.ajax({
               type : settings.loadtype,
               url  : settings.loadurl,
               data : data,		
			   //the code below added by Logan
			   error : function(xml, msg, e)
			   			{
							alert(msg);	
						},
				//the code above added by Logan
               success: function(str) {
                  setInputContent(str);
									
               }
            });
        } else if (settings.data) {
            setInputContent(settings.data);
        } else { 
            setInputContent(self.revert);
        }

        i.name  = settings.name;
        f.appendChild(i);

        if (settings.submit) {
            var b = document.createElement('input');
            b.type = 'submit';
            b.value = settings.submit;
            f.appendChild(b);
        }

        if (settings.cancel) {
            var b = document.createElement('input');
            b.type = 'button';
            b.value = settings.cancel;
            jQuery(b).click(function() {
                reset();
            });
            f.appendChild(b);
        }

        /* add created form to self */
        self.appendChild(f);

        i.focus();
        
        /* highlight input contents when requested */
        if (settings.select) {
            i.select();
        }
         
        /* discard changes if pressing esc */
        jQuery(i).keydown(function(e) {
            if (e.keyCode == 27) {
                e.preventDefault();
                reset();
            }
        });

        /* discard, submit or nothing with changes when clicking outside */
        /* do nothing is usable when navigating with tab */
        var t;
        if ('cancel' == settings.onblur) {
            jQuery(i).blur(function(e) {
                t = setTimeout(reset, 500)
            });
        } else if ('submit' == settings.onblur) {
            jQuery(i).blur(function(e) {
                jQuery(f).submit();
            });
        } else {
            jQuery(i).blur(function(e) {
              /* TODO: maybe something here */
            });
        }

        jQuery(f).submit(function(e) {

            if (t) { 
                clearTimeout(t);
            }

            /* do no submit */
            e.preventDefault(); 

            /* check if given target is function */
            if (jQuery.isFunction(settings.target)) {
                var str = settings.target.apply(self, [jQuery(i).val(), settings]);
                self.innerHTML = str;
                self.editing = false;
                callback.apply(self, [self.innerHTML, settings]);
            } else {
                /* add edited content and id of edited element to POST */           
                var p = {};
                p[i.name] = jQuery(i).val();
                p[settings.id] = self.id;

                /* show the saving indicator */
                jQuery(self).html(settings.indicator);
                jQuery.post(settings.target, p, function(str) {
					// the code below updated by Logan //
				var errorPrefix = 'error:';
					var indexError = str.indexOf(errorPrefix);
					if( indexError > -1)
					{							
							reset();
							alert(str.substr(errorPrefix.length))
					}else
					{
						var fileNamePrefix = 'name:';
						var filePathPrefix = 'path:';
						var fileNameIndex = str.indexOf(fileNamePrefix);
						var filePathIndex = str.indexOf(filePathPrefix);
						
						if(fileNameIndex > -1 && filePathIndex > -1)
						{
							var fileName = str.substr(fileNameIndex + fileNamePrefix.length);
							var filePath = str.substr(filePathPrefix.length, (fileNameIndex - fileNamePrefix.length));
							//alert(str.substr(fileNameIndex + fileNamePrefix.length));
							//change all references to the new file name or folder name
							var row = $(self).parent();
							var rowNum = $(row).attr('id').substr(3, $(row).attr('id').length - 3);
							//  fileUr, folderUrl
							var checkBox = $('#check' + rowNum);
							$(checkBox).val(fileName);
							var icon = $('#row'+rowNum + ' td a');
							//alert(checkBox.value);
							//var lastIndexOfSlash = $(checkBox).val().lastIndexOf('/');
							
							switch($('#itemType'+rowNum).val())
							{
								case "file":
									$('#fileName'+rowNum).val(fileName); //file name
									$(icon).attr('href', filePath) //icon url
									$('#filePath'+rowNum).val(filePath); //file path
									var fileUrl = $('#fileUrl'+rowNum);
									var fileUrlValue = $(fileUrl).val();
									var lastIndexOfSlash = fileUrlValue.lastIndexOf("/"); 									
									$(fileUrl).val(fileUrlValue.substring(0, fileUrlValue.lastIndexOf("/")) + "/" + fileName);
									break;
								case "folder":
									$('#folderName').val(fileName); //folder name
									var iconUrl = $('#row'+rowNum + ' td a').attr('href');
									$(icon).attr('href', (iconUrl.substring(0, iconUrl.lastIndexOf('?path=') + 6) + filePath));
									//var lastIndexOfSlash = iconUrl.lastIndexOf("/");
									//$('#row'+rowNum + ' td a').attr('href', filePath) //icon url
									$('#folderPath'+rowNum).val(filePath); //folder path							
									break;
							}
							//alert(rowNum);
							//alert($(self).parent().attr('class'));
							self.innerHTML = fileName;	
							$(self).attr('id', filePath);
							self.editing = false;
							
							//alert($('#' + setting.id).parent().attr('title'));
							callback.apply(self, [self.innerHTML, settings]); 							
						}else
						{
							alert('Incorrect results');	
						}

					}
/*                    self.innerHTML = str;
                    self.editing = false;
                    callback.apply(self, [self.innerHTML, settings]);*/				
					// the code above updated by Logan //														 

                });
            }
                        
            return false;
        });

        function reset() {
            self.innerHTML = self.revert;
            self.editing   = false;
        };
        
        function setInputContent(str) {
        	
            if (jQuery.isFunction(str)) {
                var str = str.apply(self, [self.revert, settings]);
            }
            switch (settings.type) { 	 
                case 'select': 	 
                    if (String == str.constructor) { 	 
                        eval ("var json = " + str);
                        for (var key in json) {
                            if ('selected' == key) {
                                continue;
                            } 
                            o = document.createElement('option'); 	 
                            o.value = key;
                            var text = document.createTextNode(json[key]);
                            o.appendChild(text)
                            if (key == json['selected']) {
                                o.selected = true;
                            }
                            i.appendChild(o); 	 
                        }
                    } 	 
                    break; 	 
                default: 	
										
                    i.value = str; 
                    break; 	 
            } 	 
        }

    });

    return(this);
}
