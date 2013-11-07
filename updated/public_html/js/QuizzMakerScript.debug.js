/**
 * http://github.com/valums/file-uploader
 * 
 * Multiple file upload component with progress-bar, drag-and-drop. 
 * � 2010 Andrew Valums ( andrew(at)valums.com ) 
 * 
 * Licensed under GNU GPL 2 or later and GNU LGPL 2 or later, see license.txt.
 */    

//
// Helper functions
//

var qq = qq || {};

/**
 * Adds all missing properties from second obj to first obj
 */ 
qq.extend = function(first, second){
    for (var prop in second){
        first[prop] = second[prop];
    }
};  

/**
 * Searches for a given element in the array, returns -1 if it is not present.
 * @param {Number} [from] The index at which to begin the search
 */
qq.indexOf = function(arr, elt, from){
    if (arr.indexOf) return arr.indexOf(elt, from);
    
    from = from || 0;
    var len = arr.length;    
    
    if (from < 0) from += len;  

    for (; from < len; from++){  
        if (from in arr && arr[from] === elt){  
            return from;
        }
    }  
    return -1;  
}; 
    
qq.getUniqueId = (function(){
    var id = 0;
    return function(){ return id++; };
})();

//
// Events

qq.attach = function(element, type, fn){
    if (element.addEventListener){
        element.addEventListener(type, fn, false);
    } else if (element.attachEvent){
        element.attachEvent('on' + type, fn);
    }
};
qq.detach = function(element, type, fn){
    if (element.removeEventListener){
        element.removeEventListener(type, fn, false);
    } else if (element.attachEvent){
        element.detachEvent('on' + type, fn);
    }
};

qq.preventDefault = function(e){
    if (e.preventDefault){
        e.preventDefault();
    } else{
        e.returnValue = false;
    }
};

//
// Node manipulations

/**
 * Insert node a before node b.
 */
qq.insertBefore = function(a, b){
    b.parentNode.insertBefore(a, b);
};
qq.remove = function(element){
    element.parentNode.removeChild(element);
};

qq.contains = function(parent, descendant){       
    // compareposition returns false in this case
    if (parent == descendant) return true;
    
    if (parent.contains){
        return parent.contains(descendant);
    } else {
        return !!(descendant.compareDocumentPosition(parent) & 8);
    }
};

/**
 * Creates and returns element from html string
 * Uses innerHTML to create an element
 */
qq.toElement = (function(){
    var div = document.createElement('div');
    return function(html){
        div.innerHTML = html;
        var element = div.firstChild;
        div.removeChild(element);
        return element;
    };
})();

//
// Node properties and attributes

/**
 * Sets styles for an element.
 * Fixes opacity in IE6-8.
 */
qq.css = function(element, styles){
    if (styles.opacity != null){
        if (typeof element.style.opacity != 'string' && typeof(element.filters) != 'undefined'){
            styles.filter = 'alpha(opacity=' + Math.round(100 * styles.opacity) + ')';
        }
    }
    qq.extend(element.style, styles);
};
qq.hasClass = function(element, name){
    var re = new RegExp('(^| )' + name + '( |$)');
    return re.test(element.className);
};
qq.addClass = function(element, name){
    if (!qq.hasClass(element, name)){
        element.className += ' ' + name;
    }
};
qq.removeClass = function(element, name){
    var re = new RegExp('(^| )' + name + '( |$)');
    element.className = element.className.replace(re, ' ').replace(/^\s+|\s+$/g, "");
};
qq.setText = function(element, text){
    element.innerText = text;
    element.textContent = text;
};

//
// Selecting elements

qq.children = function(element){
    var children = [],
    child = element.firstChild;

    while (child){
        if (child.nodeType == 1){
            children.push(child);
        }
        child = child.nextSibling;
    }

    return children;
};

qq.getByClass = function(element, className){
    if (element.querySelectorAll){
        return element.querySelectorAll('.' + className);
    }

    var result = [];
    var candidates = element.getElementsByTagName("*");
    var len = candidates.length;

    for (var i = 0; i < len; i++){
        if (qq.hasClass(candidates[i], className)){
            result.push(candidates[i]);
        }
    }
    return result;
};

/**
 * obj2url() takes a json-object as argument and generates
 * a querystring. pretty much like jQuery.param()
 * 
 * how to use:
 *
 *    `qq.obj2url({a:'b',c:'d'},'http://any.url/upload?otherParam=value');`
 *
 * will result in:
 *
 *    `http://any.url/upload?otherParam=value&a=b&c=d`
 *
 * @param  Object JSON-Object
 * @param  String current querystring-part
 * @return String encoded querystring
 */
qq.obj2url = function(obj, temp, prefixDone){
    var uristrings = [],
        prefix = '&',
        add = function(nextObj, i){
            var nextTemp = temp 
                ? (/\[\]$/.test(temp)) // prevent double-encoding
                   ? temp
                   : temp+'['+i+']'
                : i;
            if ((nextTemp != 'undefined') && (i != 'undefined')) {  
                uristrings.push(
                    (typeof nextObj === 'object') 
                        ? qq.obj2url(nextObj, nextTemp, true)
                        : (Object.prototype.toString.call(nextObj) === '[object Function]')
                            ? encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj())
                            : encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj)                                                          
                );
            }
        }; 

    if (!prefixDone && temp) {
      prefix = (/\?/.test(temp)) ? (/\?$/.test(temp)) ? '' : '&' : '?';
      uristrings.push(temp);
      uristrings.push(qq.obj2url(obj));
    } else if ((Object.prototype.toString.call(obj) === '[object Array]') && (typeof obj != 'undefined') ) {
        // we wont use a for-in-loop on an array (performance)
        for (var i = 0, len = obj.length; i < len; ++i){
            add(obj[i], i);
        }
    } else if ((typeof obj != 'undefined') && (obj !== null) && (typeof obj === "object")){
        // for anything else but a scalar, we will use for-in-loop
        for (var i in obj){
            add(obj[i], i);
        }
    } else {
        uristrings.push(encodeURIComponent(temp) + '=' + encodeURIComponent(obj));
    }

    return uristrings.join(prefix)
                     .replace(/^&/, '')
                     .replace(/%20/g, '+'); 
};

//
//
// Uploader Classes
//
//

var qq = qq || {};
    
/**
 * Creates upload button, validates upload, but doesn't create file list or dd. 
 */
qq.FileUploaderBasic = function(o){
    this._options = {
        // set to true to see the server response
        debug: false,
        action: '/server/upload',
        params: {},
        button: null,
        multiple: true,
        maxConnections: 3,
        // validation        
        allowedExtensions: [],               
        sizeLimit: 0,   
        minSizeLimit: 0,                             
        // events
        // return false to cancel submit
        onSubmit: function(id, fileName){},
        onProgress: function(id, fileName, loaded, total){},
        onComplete: function(id, fileName, responseJSON){},
        onCancel: function(id, fileName){},
        // messages                
        messages: {
            typeError: "{file} has invalid extension. Only {extensions} are allowed.",
            sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
            minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
            emptyError: "{file} is empty, please select files again without it.",
            onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
        },
        showMessage: function(message){
            alert(message);
        }               
    };
    qq.extend(this._options, o);
        
    // number of files being uploaded
    this._filesInProgress = 0;
    this._handler = this._createUploadHandler(); 
    
    if (this._options.button){ 
        this._button = this._createUploadButton(this._options.button);
    }
                        
    this._preventLeaveInProgress();         
};
   
qq.FileUploaderBasic.prototype = {
    setParams: function(params){
        this._options.params = params;
    },
    getInProgress: function(){
        return this._filesInProgress;         
    },
    _createUploadButton: function(element){
        var self = this;
        
        return new qq.UploadButton({
            element: element,
            multiple: this._options.multiple && qq.UploadHandlerXhr.isSupported(),
            onChange: function(input){
                self._onInputChange(input);
            }        
        });           
    },    
    _createUploadHandler: function(){
        var self = this,
            handlerClass;        
        
        if(qq.UploadHandlerXhr.isSupported()){           
            handlerClass = 'UploadHandlerXhr';                        
        } else {
            handlerClass = 'UploadHandlerForm';
        }

        var handler = new qq[handlerClass]({
            debug: this._options.debug,
            action: this._options.action,         
            maxConnections: this._options.maxConnections,   
            onProgress: function(id, fileName, loaded, total){                
                self._onProgress(id, fileName, loaded, total);
                self._options.onProgress(id, fileName, loaded, total);                    
            },            
            onComplete: function(id, fileName, result){
                self._onComplete(id, fileName, result);
                self._options.onComplete(id, fileName, result);
            },
            onCancel: function(id, fileName){
                self._onCancel(id, fileName);
                self._options.onCancel(id, fileName);
            }
        });

        return handler;
    },    
    _preventLeaveInProgress: function(){
        var self = this;
        
        qq.attach(window, 'beforeunload', function(e){
            if (!self._filesInProgress){return;}
            
            var e = e || window.event;
            // for ie, ff
            e.returnValue = self._options.messages.onLeave;
            // for webkit
            return self._options.messages.onLeave;             
        });        
    },    
    _onSubmit: function(id, fileName){
        this._filesInProgress++;  
    },
    _onProgress: function(id, fileName, loaded, total){        
    },
    _onComplete: function(id, fileName, result){
        this._filesInProgress--;                 
        if (result.error){
            this._options.showMessage(result.error);
        }             
    },
    _onCancel: function(id, fileName){
        this._filesInProgress--;        
    },
    _onInputChange: function(input){
        if (this._handler instanceof qq.UploadHandlerXhr){                
            this._uploadFileList(input.files);                   
        } else {             
            if (this._validateFile(input)){                
                this._uploadFile(input);                                    
            }                      
        }               
        this._button.reset();   
    },  
    _uploadFileList: function(files){
        for (var i=0; i<files.length; i++){
            if ( !this._validateFile(files[i])){
                return;
            }            
        }
        
        for (var i=0; i<files.length; i++){
            this._uploadFile(files[i]);        
        }        
    },       
    _uploadFile: function(fileContainer){      
        var id = this._handler.add(fileContainer);
        var fileName = this._handler.getName(id);
        
        if (this._options.onSubmit(id, fileName) !== false){
            this._onSubmit(id, fileName);
            this._handler.upload(id, this._options.params);
        }
    },      
    _validateFile: function(file){
        var name, size;
        
        if (file.value){
            // it is a file input            
            // get input value and remove path to normalize
            name = file.value.replace(/.*(\/|\\)/, "");
        } else {
            // fix missing properties in Safari
            name = file.fileName != null ? file.fileName : file.name;
            size = file.fileSize != null ? file.fileSize : file.size;
        }
                    
        if (! this._isAllowedExtension(name)){            
            this._error('typeError', name);
            return false;
            
        } else if (size === 0){            
            this._error('emptyError', name);
            return false;
                                                     
        } else if (size && this._options.sizeLimit && size > this._options.sizeLimit){            
            this._error('sizeError', name);
            return false;
                        
        } else if (size && size < this._options.minSizeLimit){
            this._error('minSizeError', name);
            return false;            
        }
        
        return true;                
    },
    _error: function(code, fileName){
        var message = this._options.messages[code];        
        function r(name, replacement){ message = message.replace(name, replacement); }
        
        r('{file}', this._formatFileName(fileName));        
        r('{extensions}', this._options.allowedExtensions.join(', '));
        r('{sizeLimit}', this._formatSize(this._options.sizeLimit));
        r('{minSizeLimit}', this._formatSize(this._options.minSizeLimit));
        
        this._options.showMessage(message);                
    },
    _formatFileName: function(name){
        if (name.length > 33){
            name = name.slice(0, 19) + '...' + name.slice(-13);    
        }
        return name;
    },
    _isAllowedExtension: function(fileName){
        var ext = (-1 !== fileName.indexOf('.')) ? fileName.replace(/.*[.]/, '').toLowerCase() : '';
        var allowed = this._options.allowedExtensions;
        
        if (!allowed.length){return true;}        
        
        for (var i=0; i<allowed.length; i++){
            if (allowed[i].toLowerCase() == ext){ return true;}    
        }
        
        return false;
    },    
    _formatSize: function(bytes){
        var i = -1;                                    
        do {
            bytes = bytes / 1024;
            i++;  
        } while (bytes > 99);
        
        return Math.max(bytes, 0.1).toFixed(1) + ['kB', 'MB', 'GB', 'TB', 'PB', 'EB'][i];          
    }
};
    
       
/**
 * Class that creates upload widget with drag-and-drop and file list
 * @inherits qq.FileUploaderBasic
 */
qq.FileUploader = function(o){
    // call parent constructor
    qq.FileUploaderBasic.apply(this, arguments);
    
    // additional options    
    qq.extend(this._options, {
        element: null,
        // if set, will be used instead of qq-upload-list in template
        listElement: null,
                
        template: '<div class="qq-uploader">' + 
                '<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
                '<div class="qq-upload-button">Upload a file</div>' +
                '<ul class="qq-upload-list"></ul>' + 
             '</div>',

        // template for one item in file list
        fileTemplate: '<li>' +
                '<span class="qq-upload-file"></span>' +
                '<span class="qq-upload-spinner"></span>' +
                '<span class="qq-upload-size"></span>' +
                '<a class="qq-upload-cancel" href="#">Cancel</a>' +
                '<span class="qq-upload-failed-text">Failed</span>' +
            '</li>',        
        
        classes: {
            // used to get elements from templates
            button: 'qq-upload-button',
            drop: 'qq-upload-drop-area',
            dropActive: 'qq-upload-drop-area-active',
            list: 'qq-upload-list',
                        
            file: 'qq-upload-file',
            spinner: 'qq-upload-spinner',
            size: 'qq-upload-size',
            cancel: 'qq-upload-cancel',

            // added to list item when upload completes
            // used in css to hide progress spinner
            success: 'qq-upload-success',
            fail: 'qq-upload-fail'
        }
    });
    // overwrite options with user supplied    
    qq.extend(this._options, o);       

    this._element = this._options.element;
    this._element.innerHTML = this._options.template;        
    this._listElement = this._options.listElement || this._find(this._element, 'list');
    
    this._classes = this._options.classes;
        
    this._button = this._createUploadButton(this._find(this._element, 'button'));        
    
    this._bindCancelEvent();
    this._setupDragDrop();
};

// inherit from Basic Uploader
qq.extend(qq.FileUploader.prototype, qq.FileUploaderBasic.prototype);

qq.extend(qq.FileUploader.prototype, {
    /**
     * Gets one of the elements listed in this._options.classes
     **/
    _find: function(parent, type){                                
        var element = qq.getByClass(parent, this._options.classes[type])[0];        
        if (!element){
            throw new Error('element not found ' + type);
        }
        
        return element;
    },
    _setupDragDrop: function(){
        var self = this,
            dropArea = this._find(this._element, 'drop');                        

        var dz = new qq.UploadDropZone({
            element: dropArea,
            onEnter: function(e){
                qq.addClass(dropArea, self._classes.dropActive);
                e.stopPropagation();
            },
            onLeave: function(e){
                e.stopPropagation();
            },
            onLeaveNotDescendants: function(e){
                qq.removeClass(dropArea, self._classes.dropActive);  
            },
            onDrop: function(e){
                dropArea.style.display = 'none';
                qq.removeClass(dropArea, self._classes.dropActive);
                self._uploadFileList(e.dataTransfer.files);    
            }
        });
                
        dropArea.style.display = 'none';

        qq.attach(document, 'dragenter', function(e){     
            if (!dz._isValidFileDrag(e)) return; 
            
            dropArea.style.display = 'block';            
        });                 
        qq.attach(document, 'dragleave', function(e){
            if (!dz._isValidFileDrag(e)) return;            
            
            var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);
            // only fire when leaving document out
            if ( ! relatedTarget || relatedTarget.nodeName == "HTML"){               
                dropArea.style.display = 'none';                                            
            }
        });                
    },
    _onSubmit: function(id, fileName){
        qq.FileUploaderBasic.prototype._onSubmit.apply(this, arguments);
        this._addToList(id, fileName);  
    },
    _onProgress: function(id, fileName, loaded, total){
        qq.FileUploaderBasic.prototype._onProgress.apply(this, arguments);

        var item = this._getItemByFileId(id);
        var size = this._find(item, 'size');
        size.style.display = 'inline';
        
        var text; 
        if (loaded != total){
            text = Math.round(loaded / total * 100) + '% from ' + this._formatSize(total);
        } else {                                   
            text = this._formatSize(total);
        }          
        
        qq.setText(size, text);         
    },
    _onComplete: function(id, fileName, result){
        qq.FileUploaderBasic.prototype._onComplete.apply(this, arguments);

        // mark completed
        var item = this._getItemByFileId(id);                
        qq.remove(this._find(item, 'cancel'));
        qq.remove(this._find(item, 'spinner'));
        
        if (result.success){
            qq.addClass(item, this._classes.success);    
        } else {
            qq.addClass(item, this._classes.fail);
        }         
    },
    _addToList: function(id, fileName){
        var item = qq.toElement(this._options.fileTemplate);                
        item.qqFileId = id;

        var fileElement = this._find(item, 'file');        
        qq.setText(fileElement, this._formatFileName(fileName));
        this._find(item, 'size').style.display = 'none';        

        this._listElement.appendChild(item);
    },
    _getItemByFileId: function(id){
        var item = this._listElement.firstChild;        
        
        // there can't be txt nodes in dynamically created list
        // and we can  use nextSibling
        while (item){            
            if (item.qqFileId == id) return item;            
            item = item.nextSibling;
        }          
    },
    /**
     * delegate click event for cancel link 
     **/
    _bindCancelEvent: function(){
        var self = this,
            list = this._listElement;            
        
        qq.attach(list, 'click', function(e){            
            e = e || window.event;
            var target = e.target || e.srcElement;
            
            if (qq.hasClass(target, self._classes.cancel)){                
                qq.preventDefault(e);
               
                var item = target.parentNode;
                self._handler.cancel(item.qqFileId);
                qq.remove(item);
            }
        });
    }    
});
    
qq.UploadDropZone = function(o){
    this._options = {
        element: null,  
        onEnter: function(e){},
        onLeave: function(e){},  
        // is not fired when leaving element by hovering descendants   
        onLeaveNotDescendants: function(e){},   
        onDrop: function(e){}                       
    };
    qq.extend(this._options, o); 
    
    this._element = this._options.element;
    
    this._disableDropOutside();
    this._attachEvents();   
};

qq.UploadDropZone.prototype = {
    _disableDropOutside: function(e){
        // run only once for all instances
        if (!qq.UploadDropZone.dropOutsideDisabled ){

            qq.attach(document, 'dragover', function(e){
                if (e.dataTransfer){
                    e.dataTransfer.dropEffect = 'none';
                    e.preventDefault(); 
                }           
            });
            
            qq.UploadDropZone.dropOutsideDisabled = true; 
        }        
    },
    _attachEvents: function(){
        var self = this;              
                  
        qq.attach(self._element, 'dragover', function(e){
            if (!self._isValidFileDrag(e)) return;
            
            var effect = e.dataTransfer.effectAllowed;
            if (effect == 'move' || effect == 'linkMove'){
                e.dataTransfer.dropEffect = 'move'; // for FF (only move allowed)    
            } else {                    
                e.dataTransfer.dropEffect = 'copy'; // for Chrome
            }
                                                     
            e.stopPropagation();
            e.preventDefault();                                                                    
        });
        
        qq.attach(self._element, 'dragenter', function(e){
            if (!self._isValidFileDrag(e)) return;
                        
            self._options.onEnter(e);
        });
        
        qq.attach(self._element, 'dragleave', function(e){
            if (!self._isValidFileDrag(e)) return;
            
            self._options.onLeave(e);
            
            var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);                      
            // do not fire when moving a mouse over a descendant
            if (qq.contains(this, relatedTarget)) return;
                        
            self._options.onLeaveNotDescendants(e); 
        });
                
        qq.attach(self._element, 'drop', function(e){
            if (!self._isValidFileDrag(e)) return;
            
            e.preventDefault();
            self._options.onDrop(e);
        });          
    },
    _isValidFileDrag: function(e){
        var dt = e.dataTransfer,
            // do not check dt.types.contains in webkit, because it crashes safari 4            
            isWebkit = navigator.userAgent.indexOf("AppleWebKit") > -1;                        

        // dt.effectAllowed is none in Safari 5
        // dt.types.contains check is for firefox            
        return dt && dt.effectAllowed != 'none' && 
            (dt.files || (!isWebkit && dt.types.contains && dt.types.contains('Files')));
        
    }        
}; 

qq.UploadButton = function(o){
    this._options = {
        element: null,  
        // if set to true adds multiple attribute to file input      
        multiple: false,
        // name attribute of file input
        name: 'file',
        onChange: function(input){},
        hoverClass: 'qq-upload-button-hover',
        focusClass: 'qq-upload-button-focus'                       
    };
    
    qq.extend(this._options, o);
        
    this._element = this._options.element;
    
    // make button suitable container for input
    qq.css(this._element, {
        position: 'relative',
        overflow: 'hidden',
        // Make sure browse button is in the right side
        // in Internet Explorer
        direction: 'ltr'
    });   
    
    this._input = this._createInput();
};

qq.UploadButton.prototype = {
    /* returns file input element */    
    getInput: function(){
        return this._input;
    },
    /* cleans/recreates the file input */
    reset: function(){
        if (this._input.parentNode){
            qq.remove(this._input);    
        }                
        
        qq.removeClass(this._element, this._options.focusClass);
        this._input = this._createInput();
    },    
    _createInput: function(){                
        var input = document.createElement("input");
        
        if (this._options.multiple){
            input.setAttribute("multiple", "multiple");
        }
                
        input.setAttribute("type", "file");
        input.setAttribute("name", this._options.name);
        
        qq.css(input, {
            position: 'absolute',
            // in Opera only 'browse' button
            // is clickable and it is located at
            // the right side of the input
            right: 0,
            top: 0,
            fontFamily: 'Arial',
            // 4 persons reported this, the max values that worked for them were 243, 236, 236, 118
            fontSize: '118px',
            margin: 0,
            padding: 0,
            cursor: 'pointer',
            opacity: 0
        });
        
        this._element.appendChild(input);

        var self = this;
        qq.attach(input, 'change', function(){
            self._options.onChange(input);
        });
                
        qq.attach(input, 'mouseover', function(){
            qq.addClass(self._element, self._options.hoverClass);
        });
        qq.attach(input, 'mouseout', function(){
            qq.removeClass(self._element, self._options.hoverClass);
        });
        qq.attach(input, 'focus', function(){
            qq.addClass(self._element, self._options.focusClass);
        });
        qq.attach(input, 'blur', function(){
            qq.removeClass(self._element, self._options.focusClass);
        });

        // IE and Opera, unfortunately have 2 tab stops on file input
        // which is unacceptable in our case, disable keyboard access
        if (window.attachEvent){
            // it is IE or Opera
            input.setAttribute('tabIndex', "-1");
        }

        return input;            
    }        
};

/**
 * Class for uploading files, uploading itself is handled by child classes
 */
qq.UploadHandlerAbstract = function(o){
    this._options = {
        debug: false,
        action: '/upload.php',
        // maximum number of concurrent uploads        
        maxConnections: 999,
        onProgress: function(id, fileName, loaded, total){},
        onComplete: function(id, fileName, response){},
        onCancel: function(id, fileName){}
    };
    qq.extend(this._options, o);    
    
    this._queue = [];
    // params for files in queue
    this._params = [];
};
qq.UploadHandlerAbstract.prototype = {
    log: function(str){
        if (this._options.debug && window.console) console.log('[uploader] ' + str);        
    },
    /**
     * Adds file or file input to the queue
     * @returns id
     **/    
    add: function(file){},
    /**
     * Sends the file identified by id and additional query params to the server
     */
    upload: function(id, params){
        var len = this._queue.push(id);

        var copy = {};        
        qq.extend(copy, params);
        this._params[id] = copy;        
                
        // if too many active uploads, wait...
        if (len <= this._options.maxConnections){               
            this._upload(id, this._params[id]);
        }
    },
    /**
     * Cancels file upload by id
     */
    cancel: function(id){
        this._cancel(id);
        this._dequeue(id);
    },
    /**
     * Cancells all uploads
     */
    cancelAll: function(){
        for (var i=0; i<this._queue.length; i++){
            this._cancel(this._queue[i]);
        }
        this._queue = [];
    },
    /**
     * Returns name of the file identified by id
     */
    getName: function(id){},
    /**
     * Returns size of the file identified by id
     */          
    getSize: function(id){},
    /**
     * Returns id of files being uploaded or
     * waiting for their turn
     */
    getQueue: function(){
        return this._queue;
    },
    /**
     * Actual upload method
     */
    _upload: function(id){},
    /**
     * Actual cancel method
     */
    _cancel: function(id){},     
    /**
     * Removes element from queue, starts upload of next
     */
    _dequeue: function(id){
        var i = qq.indexOf(this._queue, id);
        this._queue.splice(i, 1);
                
        var max = this._options.maxConnections;
        
        if (this._queue.length >= max && i < max){
            var nextId = this._queue[max-1];
            this._upload(nextId, this._params[nextId]);
        }
    }        
};

/**
 * Class for uploading files using form and iframe
 * @inherits qq.UploadHandlerAbstract
 */
qq.UploadHandlerForm = function(o){
    qq.UploadHandlerAbstract.apply(this, arguments);
       
    this._inputs = {};
};
// @inherits qq.UploadHandlerAbstract
qq.extend(qq.UploadHandlerForm.prototype, qq.UploadHandlerAbstract.prototype);

qq.extend(qq.UploadHandlerForm.prototype, {
    add: function(fileInput){
        fileInput.setAttribute('name', 'qqfile');
        var id = 'qq-upload-handler-iframe' + qq.getUniqueId();       
        
        this._inputs[id] = fileInput;
        
        // remove file input from DOM
        if (fileInput.parentNode){
            qq.remove(fileInput);
        }
                
        return id;
    },
    getName: function(id){
        // get input value and remove path to normalize
        return this._inputs[id].value.replace(/.*(\/|\\)/, "");
    },    
    _cancel: function(id){
        this._options.onCancel(id, this.getName(id));
        
        delete this._inputs[id];        

        var iframe = document.getElementById(id);
        if (iframe){
            // to cancel request set src to something else
            // we use src="javascript:false;" because it doesn't
            // trigger ie6 prompt on https
            iframe.setAttribute('src', 'javascript:false;');

            qq.remove(iframe);
        }
    },     
    _upload: function(id, params){                        
        var input = this._inputs[id];
        
        if (!input){
            throw new Error('file with passed id was not added, or already uploaded or cancelled');
        }                

        var fileName = this.getName(id);
                
        var iframe = this._createIframe(id);
        var form = this._createForm(iframe, params);
        form.appendChild(input);

        var self = this;
        this._attachLoadEvent(iframe, function(){                                 
            self.log('iframe loaded');
            
            var response = self._getIframeContentJSON(iframe);

            self._options.onComplete(id, fileName, response);
            self._dequeue(id);
            
            delete self._inputs[id];
            // timeout added to fix busy state in FF3.6
            setTimeout(function(){
                qq.remove(iframe);
            }, 1);
        });

        form.submit();        
        qq.remove(form);        
        
        return id;
    }, 
    _attachLoadEvent: function(iframe, callback){
        qq.attach(iframe, 'load', function(){
            // when we remove iframe from dom
            // the request stops, but in IE load
            // event fires
            if (!iframe.parentNode){
                return;
            }

            // fixing Opera 10.53
            if (iframe.contentDocument &&
                iframe.contentDocument.body &&
                iframe.contentDocument.body.innerHTML == "false"){
                // In Opera event is fired second time
                // when body.innerHTML changed from false
                // to server response approx. after 1 sec
                // when we upload file with iframe
                return;
            }

            callback();
        });
    },
    /**
     * Returns json object received by iframe from server.
     */
    _getIframeContentJSON: function(iframe){
        // iframe.contentWindow.document - for IE<7
        var doc = iframe.contentDocument ? iframe.contentDocument: iframe.contentWindow.document,
            response;
        
        this.log("converting iframe's innerHTML to JSON");
        this.log("innerHTML = " + doc.body.innerHTML);
                        
        try {
            response = eval("(" + doc.body.innerHTML + ")");
        } catch(err){
            response = {};
        }        

        return response;
    },
    /**
     * Creates iframe with unique name
     */
    _createIframe: function(id){
        // We can't use following code as the name attribute
        // won't be properly registered in IE6, and new window
        // on form submit will open
        // var iframe = document.createElement('iframe');
        // iframe.setAttribute('name', id);

        var iframe = qq.toElement('<iframe src="javascript:false;" name="' + id + '" />');
        // src="javascript:false;" removes ie6 prompt on https

        iframe.setAttribute('id', id);

        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        return iframe;
    },
    /**
     * Creates form, that will be submitted to iframe
     */
    _createForm: function(iframe, params){
        // We can't use the following code in IE6
        // var form = document.createElement('form');
        // form.setAttribute('method', 'post');
        // form.setAttribute('enctype', 'multipart/form-data');
        // Because in this case file won't be attached to request
        var form = qq.toElement('<form method="post" enctype="multipart/form-data"></form>');

        var queryString = qq.obj2url(params, this._options.action);

        form.setAttribute('action', queryString);
        form.setAttribute('target', iframe.name);
        form.style.display = 'none';
        document.body.appendChild(form);

        return form;
    }
});

/**
 * Class for uploading files using xhr
 * @inherits qq.UploadHandlerAbstract
 */
qq.UploadHandlerXhr = function(o){
    qq.UploadHandlerAbstract.apply(this, arguments);

    this._files = [];
    this._xhrs = [];
    
    // current loaded size in bytes for each file 
    this._loaded = [];
};

// static method
qq.UploadHandlerXhr.isSupported = function(){
    //Ngoc
    return false;

    var input = document.createElement('input');
    input.type = 'file';        
    
    return (
        'multiple' in input &&
        typeof File != "undefined" &&
        typeof (new XMLHttpRequest()).upload != "undefined" );       
};

// @inherits qq.UploadHandlerAbstract
qq.extend(qq.UploadHandlerXhr.prototype, qq.UploadHandlerAbstract.prototype)

qq.extend(qq.UploadHandlerXhr.prototype, {
    /**
     * Adds file to the queue
     * Returns id to use with upload, cancel
     **/    
    add: function(file){
        if (!(file instanceof File)){
            throw new Error('Passed obj in not a File (in qq.UploadHandlerXhr)');
        }
                
        return this._files.push(file) - 1;        
    },
    getName: function(id){        
        var file = this._files[id];
        // fix missing name in Safari 4
        return file.fileName != null ? file.fileName : file.name;       
    },
    getSize: function(id){
        var file = this._files[id];
        return file.fileSize != null ? file.fileSize : file.size;
    },    
    /**
     * Returns uploaded bytes for file identified by id 
     */    
    getLoaded: function(id){
        return this._loaded[id] || 0; 
    },
    /**
     * Sends the file identified by id and additional query params to the server
     * @param {Object} params name-value string pairs
     */    
    _upload: function(id, params){
        var file = this._files[id],
            name = this.getName(id),
            size = this.getSize(id);
                
        this._loaded[id] = 0;
                                
        var xhr = this._xhrs[id] = new XMLHttpRequest();
        var self = this;
                                        
        xhr.upload.onprogress = function(e){
            if (e.lengthComputable){
                self._loaded[id] = e.loaded;
                self._options.onProgress(id, name, e.loaded, e.total);
            }
        };

        xhr.onreadystatechange = function(){            
            if (xhr.readyState == 4){
                self._onComplete(id, xhr);                    
            }
        };

        // build query string
        params = params || {};
        params['qqfile'] = name;
        var queryString = qq.obj2url(params, this._options.action);

        xhr.open("POST", queryString, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("X-File-Name", encodeURIComponent(name));
        xhr.setRequestHeader("Content-Type", "application/octet-stream");
        xhr.send(file);
    },
    _onComplete: function(id, xhr){
        // the request was aborted/cancelled
        if (!this._files[id]) return;
        
        var name = this.getName(id);
        var size = this.getSize(id);
        
        this._options.onProgress(id, name, size, size);
                
        if (xhr.status == 200){
            this.log("xhr - server response received");
            this.log("responseText = " + xhr.responseText);
                        
            var response;
                    
            try {
                response = eval("(" + xhr.responseText + ")");
            } catch(err){
                response = {};
            }
            
            this._options.onComplete(id, name, response);
                        
        } else {                   
            this._options.onComplete(id, name, {});
        }
                
        this._files[id] = null;
        this._xhrs[id] = null;    
        this._dequeue(id);                    
    },
    _cancel: function(id){
        this._options.onCancel(id, this.getName(id));
        
        this._files[id] = null;
        
        if (this._xhrs[id]){
            this._xhrs[id].abort();
            this._xhrs[id] = null;                                   
        }
    }
});













/**
 @preserve CLEditor WYSIWYG HTML Editor v1.3.0
 http://premiumsoftware.net/cleditor
 requires jQuery v1.4.2 or later

 Copyright 2010, Chris Landowski, Premium Software, LLC
 Dual licensed under the MIT or GPL Version 2 licenses.
*/

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// @output_file_name jquery.cleditor.min.js
// ==/ClosureCompiler==

(function($) {

  //==============
  // jQuery Plugin
  //==============

  $.cleditor = {

    // Define the defaults used for all new cleditor instances
    defaultOptions: {
      width:        500, // width not including margins, borders or padding
      height:       250, // height not including margins, borders or padding
      controls:     // controls to add to the toolbar
                    "bold italic underline strikethrough subscript superscript | font size " +
                    "style | color highlight removeformat | bullets numbering | outdent " +
                    "indent | alignleft center alignright justify | undo redo | " +
                    "rule image link unlink | cut copy paste pastetext | print source",
      colors:       // colors in the color popup
                    "FFF FCC FC9 FF9 FFC 9F9 9FF CFF CCF FCF " +
                    "CCC F66 F96 FF6 FF3 6F9 3FF 6FF 99F F9F " +
                    "BBB F00 F90 FC6 FF0 3F3 6CC 3CF 66C C6C " +
                    "999 C00 F60 FC3 FC0 3C0 0CC 36F 63F C3C " +
                    "666 900 C60 C93 990 090 399 33F 60C 939 " +
                    "333 600 930 963 660 060 366 009 339 636 " +
                    "000 300 630 633 330 030 033 006 309 303",    
      fonts:        // font names in the font popup
                    "Arial,Arial Black,Comic Sans MS,Courier New,Narrow,Garamond," +
                    "Georgia,Impact,Sans Serif,Serif,Tahoma,Trebuchet MS,Verdana",
      sizes:        // sizes in the font size popup
                    "1,2,3,4,5,6,7",
      styles:       // styles in the style popup
                    [["Paragraph", "<p>"], ["Header 1", "<h1>"], ["Header 2", "<h2>"],
                    ["Header 3", "<h3>"],  ["Header 4","<h4>"],  ["Header 5","<h5>"],
                    ["Header 6","<h6>"]],
      useCSS:       false, // use CSS to style HTML when possible (not supported in ie)
      docType:      // Document type contained within the editor
                    '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
      docCSSFile:   // CSS file used to style the document contained within the editor
                    "", 
      bodyStyle:    // style to assign to document body contained within the editor
                    "margin:4px; font:10pt Arial,Verdana; cursor:text"
    },

    // Define all usable toolbar buttons - the init string property is 
    //   expanded during initialization back into the buttons object and 
    //   seperate object properties are created for each button.
    //   e.g. buttons.size.title = "Font Size"
    buttons: {
      // name,title,command,popupName (""=use name)
      init:
      "bold,,|" +
      "italic,,|" +
      "underline,,|" +
      "strikethrough,,|" +
      "subscript,,|" +
      "superscript,,|" +
      "font,,fontname,|" +
      "size,Font Size,fontsize,|" +
      "style,,formatblock,|" +
      "color,Font Color,forecolor,|" +
      "highlight,Text Highlight Color,hilitecolor,color|" +
      "removeformat,Remove Formatting,|" +
      "bullets,,insertunorderedlist|" +
      "numbering,,insertorderedlist|" +
      "outdent,,|" +
      "indent,,|" +
      "alignleft,Align Text Left,justifyleft|" +
      "center,,justifycenter|" +
      "alignright,Align Text Right,justifyright|" +
      "justify,,justifyfull|" +
      "undo,,|" +
      "redo,,|" +
      "rule,Insert Horizontal Rule,inserthorizontalrule|" +
      "image,Insert Image,insertimage,url|" +
      "link,Insert Hyperlink,createlink,url|" +
      "unlink,Remove Hyperlink,|" +
      "cut,,|" +
      "copy,,|" +
      "paste,,|" +
      "pastetext,Paste as Text,inserthtml,|" +
      "print,,|" +
      "source,Show Source"
    },

    // imagesPath - returns the path to the images folder
    imagesPath: function() { return imagesPath(); }

  };

  // cleditor - creates a new editor for each of the matched textareas
  $.fn.cleditor = function(options) {

    // Create a new jQuery object to hold the results
    var $result = $([]);

    // Loop through all matching textareas and create the editors
    this.each(function(idx, elem) {
      if (elem.tagName == "TEXTAREA") {
        var data = $.data(elem, CLEDITOR);
        if (!data) data = new cleditor(elem, options);
        $result = $result.add(data);
      }
    });

    // return the new jQuery object
    return $result;

  };
    
  //==================
  // Private Variables
  //==================

  var

  // Misc constants
  BACKGROUND_COLOR = "backgroundColor",
  BUTTON           = "button",
  BUTTON_NAME      = "buttonName",
  CHANGE           = "change",
  CLEDITOR         = "cleditor",
  CLICK            = "click",
  DISABLED         = "disabled",
  DIV_TAG          = "<div>",
  TRANSPARENT      = "transparent",
  UNSELECTABLE     = "unselectable",

  // Class name constants
  MAIN_CLASS       = "cleditorMain",    // main containing div
  TOOLBAR_CLASS    = "cleditorToolbar", // toolbar div inside main div
  GROUP_CLASS      = "cleditorGroup",   // group divs inside the toolbar div
  BUTTON_CLASS     = "cleditorButton",  // button divs inside group div
  DISABLED_CLASS   = "cleditorDisabled",// disabled button divs
  DIVIDER_CLASS    = "cleditorDivider", // divider divs inside group div
  POPUP_CLASS      = "cleditorPopup",   // popup divs inside body
  LIST_CLASS       = "cleditorList",    // list popup divs inside body
  COLOR_CLASS      = "cleditorColor",   // color popup div inside body
  PROMPT_CLASS     = "cleditorPrompt",  // prompt popup divs inside body
  MSG_CLASS        = "cleditorMsg",     // message popup div inside body

  // Test for ie
  ie = $.browser.msie,
  ie6 = /msie\s6/i.test(navigator.userAgent),

  // Test for iPhone/iTouch/iPad
  iOS = /iphone|ipad|ipod/i.test(navigator.userAgent),

  // Popups are created once as needed and shared by all editor instances
  popups = {},

  // Used to prevent the document click event from being bound more than once
  documentClickAssigned,

  // Local copy of the buttons object
  buttons = $.cleditor.buttons;

  //===============
  // Initialization
  //===============

  // Expand the buttons.init string back into the buttons object
  //   and create seperate object properties for each button.
  //   e.g. buttons.size.title = "Font Size"
  $.each(buttons.init.split("|"), function(idx, button) {
    var items = button.split(","), name = items[0];
    buttons[name] = {
      stripIndex: idx,
      name: name,
      title: items[1] === "" ? name.charAt(0).toUpperCase() + name.substr(1) : items[1],
      command: items[2] === "" ? name : items[2],
      popupName: items[3] === "" ? name : items[3]
    };
  });
  delete buttons.init;

  //============
  // Constructor
  //============

  // cleditor - creates a new editor for the passed in textarea element
  cleditor = function(area, options) {

    var editor = this;

    // Get the defaults and override with options
    editor.options = options = $.extend({}, $.cleditor.defaultOptions, options);

    // Hide the textarea and associate it with this editor
    var $area = editor.$area = $(area)
      .hide()
      .data(CLEDITOR, editor)
      .blur(function() {
        // Update the iframe when the textarea loses focus
        updateFrame(editor, true);
      });

    // Create the main container and append the textarea
    var $main = editor.$main = $(DIV_TAG)
      .addClass(MAIN_CLASS)
      .width(options.width)
      .height(options.height);

    // Create the toolbar
    var $toolbar = editor.$toolbar = $(DIV_TAG)
      .addClass(TOOLBAR_CLASS)
      .appendTo($main);

    // Add the first group to the toolbar
    var $group = $(DIV_TAG)
      .addClass(GROUP_CLASS)
      .appendTo($toolbar);
    
    // Add the buttons to the toolbar
    $.each(options.controls.split(" "), function(idx, buttonName) {
      if (buttonName === "") return true;

      // Divider
      if (buttonName == "|") {

        // Add a new divider to the group
        var $div = $(DIV_TAG)
          .addClass(DIVIDER_CLASS)
          .appendTo($group);

        // Create a new group
        $group = $(DIV_TAG)
          .addClass(GROUP_CLASS)
          .appendTo($toolbar);

      }

      // Button
      else {
        
        // Get the button definition
        var button = buttons[buttonName];

        // Add a new button to the group
        var $buttonDiv = $(DIV_TAG)
          .data(BUTTON_NAME, button.name)
          .addClass(BUTTON_CLASS)
          .attr("title", button.title)
          .bind(CLICK, $.proxy(buttonClick, editor))
          .appendTo($group)
          .hover(hoverEnter, hoverLeave);

        // Prepare the button image
        var map = {};
        if (button.css) map = button.css;
        else if (button.image) map.backgroundImage = imageUrl(button.image);
        if (button.stripIndex) map.backgroundPosition = button.stripIndex * -24;
        $buttonDiv.css(map);

        // Add the unselectable attribute for ie
        if (ie)
          $buttonDiv.attr(UNSELECTABLE, "on");

        // Create the popup
        if (button.popupName)
          createPopup(button.popupName, options, button.popupClass,
            button.popupContent, button.popupHover);
        
      }

    });

    // Add the main div to the DOM and append the textarea
    $main.insertBefore($area)
      .append($area);

    // Bind the document click event handler
    if (!documentClickAssigned) {
      $(document).click(function(e) {
        // Dismiss all non-prompt popups
        var $target = $(e.target);
        if (!$target.add($target.parents()).is("." + PROMPT_CLASS))
          hidePopups();
      });
      documentClickAssigned = true;
    }

    // Bind the window resize event when the width or height is auto or %
    if (/auto|%/.test("" + options.width + options.height))
      $(window).resize(function() {refresh(editor);});

    // Create the iframe and resize the controls
    refresh(editor);

  };

  //===============
  // Public Methods
  //===============

  var fn = cleditor.prototype,

  // Expose the following private functions as methods on the cleditor object.
  // The closure compiler will rename the private functions. However, the
  // exposed method names on the cleditor object will remain fixed.
  methods = [
    ["clear", clear],
    ["disable", disable],
    ["execCommand", execCommand],
    ["focus", focus],
    ["hidePopups", hidePopups],
    ["sourceMode", sourceMode, true],
    ["refresh", refresh],
    ["select", select],
    ["selectedHTML", selectedHTML, true],
    ["selectedText", selectedText, true],
    ["showMessage", showMessage],
    ["updateFrame", updateFrame],
    ["updateTextArea", updateTextArea]
  ];

  $.each(methods, function(idx, method) {
    fn[method[0]] = function() {
      var editor = this, args = [editor];
      // using each here would cast booleans into objects!
      for(var x = 0; x < arguments.length; x++) {args.push(arguments[x]);}
      var result = method[1].apply(editor, args);
      if (method[2]) return result;
      return editor;
    };
  });

  // change - shortcut for .bind("change", handler) or .trigger("change")
  fn.change = function(handler) {
    var $this = $(this);
    return handler ? $this.bind(CHANGE, handler) : $this.trigger(CHANGE);
  };

  //===============
  // Event Handlers
  //===============

  // buttonClick - click event handler for toolbar buttons
  function buttonClick(e) {

    var editor = this,
        buttonDiv = e.target,
        buttonName = $.data(buttonDiv, BUTTON_NAME),
        button = buttons[buttonName],
        popupName = button.popupName,
        popup = popups[popupName];

    // Check if disabled
    if (editor.disabled || $(buttonDiv).attr(DISABLED) == DISABLED)
      return;

    // Fire the buttonClick event
    var data = {
      editor: editor,
      button: buttonDiv,
      buttonName: buttonName,
      popup: popup,
      popupName: popupName,
      command: button.command,
      useCSS: editor.options.useCSS
    };

    if (button.buttonClick && button.buttonClick(e, data) === false)
      return false;

    // Toggle source
    if (buttonName == "source") {

      // Show the iframe
      if (sourceMode(editor)) {
        delete editor.range;
        editor.$area.hide();
        editor.$frame.show();
        buttonDiv.title = button.title;
      }

      // Show the textarea
      else {
        editor.$frame.hide();
        editor.$area.show();
        buttonDiv.title = "Show Rich Text";
      }

      // Enable or disable the toolbar buttons
      // IE requires the timeout
      setTimeout(function() {refreshButtons(editor);}, 100);

    }

    // Check for rich text mode
    else if (!sourceMode(editor)) {

      // Handle popups
      if (popupName) {
        var $popup = $(popup);

        // URL
        if (popupName == "url") {

          // Check for selection before showing the link url popup
          if (buttonName == "link" && selectedText(editor) === "") {
            showMessage(editor, "A selection is required when inserting a link.", buttonDiv);
            return false;
          }

          // Wire up the submit button click event handler
          $popup.children(":button")
            .unbind(CLICK)
            .bind(CLICK, function() {

              // Insert the image or link if a url was entered
              var $text = $popup.find(":text"),
                url = $.trim($text.val());
              if (url !== "")
                execCommand(editor, data.command, url, null, data.button);

              // Reset the text, hide the popup and set focus
              $text.val("http://");
              hidePopups();
              focus(editor);

            });

        }

        // Paste as Text
        else if (popupName == "pastetext") {

          // Wire up the submit button click event handler
          $popup.children(":button")
            .unbind(CLICK)
            .bind(CLICK, function() {

              // Insert the unformatted text replacing new lines with break tags
              var $textarea = $popup.find("textarea"),
                text = $textarea.val().replace(/\n/g, "<br />");
              if (text !== "")
                execCommand(editor, data.command, text, null, data.button);

              // Reset the text, hide the popup and set focus
              $textarea.val("");
              hidePopups();
              focus(editor);

            });

        }

        // Show the popup if not already showing for this button
        if (buttonDiv !== $.data(popup, BUTTON)) {
          showPopup(editor, popup, buttonDiv);
          return false; // stop propagination to document click
        }

        // propaginate to documnt click
        return;

      }

      // Print
      else if (buttonName == "print")
        editor.$frame[0].contentWindow.print();

      // All other buttons
      else if (!execCommand(editor, data.command, data.value, data.useCSS, buttonDiv))
        return false;

    }

    // Focus the editor
    focus(editor);

  }

  // hoverEnter - mouseenter event handler for buttons and popup items
  function hoverEnter(e) {
    var $div = $(e.target).closest("div");
    $div.css(BACKGROUND_COLOR, $div.data(BUTTON_NAME) ? "#FFF" : "#FFC");
  }

  // hoverLeave - mouseleave event handler for buttons and popup items
  function hoverLeave(e) {
    $(e.target).closest("div").css(BACKGROUND_COLOR, "transparent");
  }

  // popupClick - click event handler for popup items
  function popupClick(e) {

    var editor = this,
        popup = e.data.popup,
        target = e.target;

    // Check for message and prompt popups
    if (popup === popups.msg || $(popup).hasClass(PROMPT_CLASS))
      return;

    // Get the button info
    var buttonDiv = $.data(popup, BUTTON),
        buttonName = $.data(buttonDiv, BUTTON_NAME),
        button = buttons[buttonName],
        command = button.command,
        value,
        useCSS = editor.options.useCSS;

    // Get the command value
    if (buttonName == "font")
      // Opera returns the fontfamily wrapped in quotes
      value = target.style.fontFamily.replace(/"/g, "");
    else if (buttonName == "size") {
      if (target.tagName == "DIV")
        target = target.children[0];
      value = target.innerHTML;
    }
    else if (buttonName == "style")
      value = "<" + target.tagName + ">";
    else if (buttonName == "color")
      value = hex(target.style.backgroundColor);
    else if (buttonName == "highlight") {
      value = hex(target.style.backgroundColor);
      if (ie) command = 'backcolor';
      else useCSS = true;
    }

    // Fire the popupClick event
    var data = {
      editor: editor,
      button: buttonDiv,
      buttonName: buttonName,
      popup: popup,
      popupName: button.popupName,
      command: command,
      value: value,
      useCSS: useCSS
    };

    if (button.popupClick && button.popupClick(e, data) === false)
      return;

    // Execute the command
    if (data.command && !execCommand(editor, data.command, data.value, data.useCSS, buttonDiv))
      return false;

    // Hide the popup and focus the editor
    hidePopups();
    focus(editor);

  }

  //==================
  // Private Functions
  //==================

  // checksum - returns a checksum using the Adler-32 method
  function checksum(text)
  {
    var a = 1, b = 0;
    for (var index = 0; index < text.length; ++index) {
      a = (a + text.charCodeAt(index)) % 65521;
      b = (b + a) % 65521;
    }
    return (b << 16) | a;
  }

  // clear - clears the contents of the editor
  function clear(editor) {
    editor.$area.val("");
    updateFrame(editor);
  }

  // createPopup - creates a popup and adds it to the body
  function createPopup(popupName, options, popupTypeClass, popupContent, popupHover) {

    // Check if popup already exists
    if (popups[popupName])
      return popups[popupName];

    // Create the popup
    var $popup = $(DIV_TAG)
      .hide()
      .addClass(POPUP_CLASS)
      .appendTo("body");

    // Add the content

    // Custom popup
    if (popupContent)
      $popup.html(popupContent);

    // Color
    else if (popupName == "color") {
      var colors = options.colors.split(" ");
      if (colors.length < 10)
        $popup.width("auto");
      $.each(colors, function(idx, color) {
        $(DIV_TAG).appendTo($popup)
          .css(BACKGROUND_COLOR, "#" + color);
      });
      popupTypeClass = COLOR_CLASS;
    }

    // Font
    else if (popupName == "font")
      $.each(options.fonts.split(","), function(idx, font) {
        $(DIV_TAG).appendTo($popup)
          .css("fontFamily", font)
          .html(font);
      });

    // Size
    else if (popupName == "size")
      $.each(options.sizes.split(","), function(idx, size) {
        $(DIV_TAG).appendTo($popup)
          .html("<font size=" + size + ">" + size + "</font>");
      });

    // Style
    else if (popupName == "style")
      $.each(options.styles, function(idx, style) {
        $(DIV_TAG).appendTo($popup)
          .html(style[1] + style[0] + style[1].replace("<", "</"));
      });

    // URL
    else if (popupName == "url") {
      $popup.html('Enter URL:<br><input type=text value="http://" size=35><br><input type=button value="Submit">');
      popupTypeClass = PROMPT_CLASS;
    }

    // Paste as Text
    else if (popupName == "pastetext") {
      $popup.html('Paste your content here and click submit.<br /><textarea cols=40 rows=3></textarea><br /><input type=button value=Submit>');
      popupTypeClass = PROMPT_CLASS;
    }

    // Add the popup type class name
    if (!popupTypeClass && !popupContent)
      popupTypeClass = LIST_CLASS;
    $popup.addClass(popupTypeClass);

    // Add the unselectable attribute to all items
    if (ie) {
      $popup.attr(UNSELECTABLE, "on")
        .find("div,font,p,h1,h2,h3,h4,h5,h6")
        .attr(UNSELECTABLE, "on");
    }

    // Add the hover effect to all items
    if ($popup.hasClass(LIST_CLASS) || popupHover === true)
      $popup.children().hover(hoverEnter, hoverLeave);

    // Add the popup to the array and return it
    popups[popupName] = $popup[0];
    return $popup[0];

  }

  // disable - enables or disables the editor
  function disable(editor, disabled) {

    // Update the textarea and save the state
    if (disabled) {
      editor.$area.attr(DISABLED, DISABLED);
      editor.disabled = true;
    }
    else {
      editor.$area.removeAttr(DISABLED);
      delete editor.disabled;
    }

    // Switch the iframe into design mode.
    // ie6 does not support designMode.
    // ie7 & ie8 do not properly support designMode="off".
    try {
      if (ie) editor.doc.body.contentEditable = !disabled;
      else editor.doc.designMode = !disabled ? "on" : "off";
    }
    // Firefox 1.5 throws an exception that can be ignored
    // when toggling designMode from off to on.
    catch (err) {}

    // Enable or disable the toolbar buttons
    refreshButtons(editor);

  }

  // execCommand - executes a designMode command
  function execCommand(editor, command, value, useCSS, button) {

    // Restore the current ie selection
    restoreRange(editor);

    // Set the styling method
    if (!ie) {
      if (useCSS === undefined || useCSS === null)
        useCSS = editor.options.useCSS;
      editor.doc.execCommand("styleWithCSS", 0, useCSS.toString());
    }

    // Execute the command and check for error
    var success = true, description;
    if (ie && command.toLowerCase() == "inserthtml")
      getRange(editor).pasteHTML(value);
    else {
      try { success = editor.doc.execCommand(command, 0, value || null); }
      catch (err) { description = err.description; success = false; }
      if (!success) {
        if ("cutcopypaste".indexOf(command) > -1)
          showMessage(editor, "For security reasons, your browser does not support the " +
            command + " command. Try using the keyboard shortcut or context menu instead.",
            button);
        else
          showMessage(editor,
            (description ? description : "Error executing the " + command + " command."),
            button);
      }
    }

    // Enable the buttons
    refreshButtons(editor);
    return success;

  }

  // focus - sets focus to either the textarea or iframe
  function focus(editor) {
    setTimeout(function() {
      if (sourceMode(editor)) editor.$area.focus();
      else editor.$frame[0].contentWindow.focus();
      refreshButtons(editor);
    }, 0);
  }

  // getRange - gets the current text range object
  function getRange(editor) {
    if (ie) return getSelection(editor).createRange();
    return getSelection(editor).getRangeAt(0);
  }

  // getSelection - gets the current text range object
  function getSelection(editor) {
    if (ie) return editor.doc.selection;
    return editor.$frame[0].contentWindow.getSelection();
  }

  // Returns the hex value for the passed in string.
  //   hex("rgb(255, 0, 0)"); // #FF0000
  //   hex("#FF0000"); // #FF0000
  //   hex("#F00"); // #FF0000
  function hex(s) {
    var m = /rgba?\((\d+), (\d+), (\d+)/.exec(s),
      c = s.split("");
    if (m) {
      s = ( m[1] << 16 | m[2] << 8 | m[3] ).toString(16);
      while (s.length < 6)
        s = "0" + s;
    }
    return "#" + (s.length == 6 ? s : c[1] + c[1] + c[2] + c[2] + c[3] + c[3]);
  }

  // hidePopups - hides all popups
  function hidePopups() {
    $.each(popups, function(idx, popup) {
      $(popup)
        .hide()
        .unbind(CLICK)
        .removeData(BUTTON);
    });
  }

  // imagesPath - returns the path to the images folder
  function imagesPath() {
    var cssFile = "jquery.cleditor.css",
        href = $("link[href$='" + cssFile +"']").attr("href");
    return href.substr(0, href.length - cssFile.length) + "images/";
  }

  // imageUrl - Returns the css url string for a filemane
  function imageUrl(filename) {
    return "url(" + imagesPath() + filename + ")";
  }

  // refresh - creates the iframe and resizes the controls
  function refresh(editor) {

    var $main = editor.$main,
      options = editor.options;

    // Remove the old iframe
    if (editor.$frame) 
      editor.$frame.remove();

    // Create a new iframe
    var $frame = editor.$frame = $('<iframe frameborder="0" src="javascript:true;">')
      .hide()
      .appendTo($main);

    // Load the iframe document content
    var contentWindow = $frame[0].contentWindow,
      doc = editor.doc = contentWindow.document,
      $doc = $(doc);

    doc.open();
    doc.write(
      options.docType +
      '<html>' +
      ((options.docCSSFile === '') ? '' : '<head><link rel="stylesheet" type="text/css" href="' + options.docCSSFile + '" /></head>') +
      '<body style="' + options.bodyStyle + '"></body></html>'
    );
    doc.close();

    // Work around for bug in IE which causes the editor to lose
    // focus when clicking below the end of the document.
    if (ie)
      $doc.click(function() {focus(editor);});

    // Load the content
    updateFrame(editor);

    // Bind the ie specific iframe event handlers
    if (ie) {

      // Save the current user selection. This code is needed since IE will
      // reset the selection just after the beforedeactivate event and just
      // before the beforeactivate event.
      $doc.bind("beforedeactivate beforeactivate selectionchange keypress", function(e) {
        
        // Flag the editor as inactive
        if (e.type == "beforedeactivate")
          editor.inactive = true;
        
        // Get rid of the bogus selection and flag the editor as active
        else if (e.type == "beforeactivate") {
          if (!editor.inactive && editor.range && editor.range.length > 1)
            editor.range.shift();
          delete editor.inactive;
        }

        // Save the selection when the editor is active
        else if (!editor.inactive) {
          if (!editor.range) 
            editor.range = [];
          editor.range.unshift(getRange(editor));

          // We only need the last 2 selections
          while (editor.range.length > 2)
            editor.range.pop();
        }

      });

      // Restore the text range when the iframe gains focus
      $frame.focus(function() {
        restoreRange(editor);
      });

    }

    // Update the textarea when the iframe loses focus
    ($.browser.mozilla ? $doc : $(contentWindow)).blur(function() {
      updateTextArea(editor, true);
    });

    // Enable the toolbar buttons as the user types or clicks
    $doc.click(hidePopups)
      .bind("keyup mouseup", function() {
        refreshButtons(editor);
      });

    // Show the textarea for iPhone/iTouch/iPad or
    // the iframe when design mode is supported.
    if (iOS) editor.$area.show();
    else $frame.show();

    // Wait for the layout to finish - shortcut for $(document).ready()
    $(function() {

      var $toolbar = editor.$toolbar,
          $group = $toolbar.children("div:last"),
          wid = $main.width();

      // Resize the toolbar
      var hgt = $group.offset().top + $group.outerHeight() - $toolbar.offset().top + 1;
      $toolbar.height(hgt);

      // Resize the iframe
      hgt = (/%/.test("" + options.height) ? $main.height() : parseInt(options.height)) - hgt;
      $frame.width(wid).height(hgt);

      // Resize the textarea. IE6 textareas have a 1px top
      // & bottom margin that cannot be removed using css.
      editor.$area.width(wid).height(ie6 ? hgt - 2 : hgt);

      // Switch the iframe into design mode if enabled
      disable(editor, editor.disabled);

      // Enable or disable the toolbar buttons
      refreshButtons(editor);

    });

  }

  // refreshButtons - enables or disables buttons based on availability
  function refreshButtons(editor) {

    // Webkit requires focus before queryCommandEnabled will return anything but false
    if (!iOS && $.browser.webkit && !editor.focused) {
      editor.$frame[0].contentWindow.focus();
      window.focus();
      editor.focused = true;
    }

    // Get the object used for checking queryCommandEnabled
    var queryObj = editor.doc;
    if (ie) queryObj = getRange(editor);

    // Loop through each button
    var inSourceMode = sourceMode(editor);
    $.each(editor.$toolbar.find("." + BUTTON_CLASS), function(idx, elem) {

      var $elem = $(elem),
        button = $.cleditor.buttons[$.data(elem, BUTTON_NAME)],
        command = button.command,
        enabled = true;

      // Determine the state
      if (editor.disabled)
        enabled = false;
      else if (button.getEnabled) {
        var data = {
          editor: editor,
          button: elem,
          buttonName: button.name,
          popup: popups[button.popupName],
          popupName: button.popupName,
          command: button.command,
          useCSS: editor.options.useCSS
        };
        enabled = button.getEnabled(data);
        if (enabled === undefined)
          enabled = true;
      }
      else if (((inSourceMode || iOS) && button.name != "source") ||
      (ie && (command == "undo" || command == "redo")))
        enabled = false;
      else if (command && command != "print") {
        if (ie && command == "hilitecolor")
          command = "backcolor";
        // IE does not support inserthtml, so it's always enabled
        if (!ie || command != "inserthtml") {
          try {enabled = queryObj.queryCommandEnabled(command);}
          catch (err) {enabled = false;}
        }
      }

      // Enable or disable the button
      if (enabled) {
        $elem.removeClass(DISABLED_CLASS);
        $elem.removeAttr(DISABLED);
      }
      else {
        $elem.addClass(DISABLED_CLASS);
        $elem.attr(DISABLED, DISABLED);
      }

    });
  }

  // restoreRange - restores the current ie selection
  function restoreRange(editor) {
    if (ie && editor.range)
      editor.range[0].select();
  }

  // select - selects all the text in either the textarea or iframe
  function select(editor) {
    setTimeout(function() {
      if (sourceMode(editor)) editor.$area.select();
      else execCommand(editor, "selectall");
    }, 0);
  }

  // selectedHTML - returns the current HTML selection or and empty string
  function selectedHTML(editor) {
    restoreRange(editor);
    var range = getRange(editor);
    if (ie)
      return range.htmlText;
    var layer = $("<layer>")[0];
    layer.appendChild(range.cloneContents());
    var html = layer.innerHTML;
    layer = null;
    return html;
  }

  // selectedText - returns the current text selection or and empty string
  function selectedText(editor) {
    restoreRange(editor);
    if (ie) return getRange(editor).text;
    return getSelection(editor).toString();
  }

  // showMessage - alert replacement
  function showMessage(editor, message, button) {
    var popup = createPopup("msg", editor.options, MSG_CLASS);
    popup.innerHTML = message;
    showPopup(editor, popup, button);
  }

  // showPopup - shows a popup
  function showPopup(editor, popup, button) {

    var offset, left, top, $popup = $(popup);

    // Determine the popup location
    if (button) {
      var $button = $(button);
      offset = $button.offset();
      left = --offset.left;
      top = offset.top + $button.height();
    }
    else {
      var $toolbar = editor.$toolbar;
      offset = $toolbar.offset();
      left = Math.floor(($toolbar.width() - $popup.width()) / 2) + offset.left;
      top = offset.top + $toolbar.height() - 2;
    }

    // Position and show the popup
    hidePopups();
    $popup.css({left: left, top: top})
      .show();

    // Assign the popup button and click event handler
    if (button) {
      $.data(popup, BUTTON, button);
      $popup.bind(CLICK, {popup: popup}, $.proxy(popupClick, editor));
    }

    // Focus the first input element if any
    setTimeout(function() {
      $popup.find(":text,textarea").eq(0).focus().select();
    }, 100);

  }

  // sourceMode - returns true if the textarea is showing
  function sourceMode(editor) {
    return editor.$area.is(":visible");
  }

  // updateFrame - updates the iframe with the textarea contents
  function updateFrame(editor, checkForChange) {
    
    var code = editor.$area.val(),
      options = editor.options,
      updateFrameCallback = options.updateFrame,
      $body = $(editor.doc.body);

    // Check for textarea change to avoid unnecessary firing
    // of potentially heavy updateFrame callbacks.
    if (updateFrameCallback) {
      var sum = checksum(code);
      if (checkForChange && editor.areaChecksum == sum)
        return;
      editor.areaChecksum = sum;
    }

    // Convert the textarea source code into iframe html
    var html = updateFrameCallback ? updateFrameCallback(code) : code;

    // Prevent script injection attacks by html encoding script tags
    html = html.replace(/<(?=\/?script)/ig, "&lt;");

    // Update the iframe checksum
    if (options.updateTextArea)
      editor.frameChecksum = checksum(html);

    // Update the iframe and trigger the change event
    if (html != $body.html()) {
      $body.html(html);
      $(editor).triggerHandler(CHANGE);
    }

  }

  // updateTextArea - updates the textarea with the iframe contents
  function updateTextArea(editor, checkForChange) {

    var html = $(editor.doc.body).html(),
      options = editor.options,
      updateTextAreaCallback = options.updateTextArea,
      $area = editor.$area;

    // Check for iframe change to avoid unnecessary firing
    // of potentially heavy updateTextArea callbacks.
    if (updateTextAreaCallback) {
      var sum = checksum(html);
      if (checkForChange && editor.frameChecksum == sum)
        return;
      editor.frameChecksum = sum;
    }

    // Convert the iframe html into textarea source code
    var code = updateTextAreaCallback ? updateTextAreaCallback(html) : html;

    // Update the textarea checksum
    if (options.updateFrame)
      editor.areaChecksum = checksum(code);

    // Update the textarea and trigger the change event
    if (code != $area.val()) {
      $area.val(code);
      $(editor).triggerHandler(CHANGE);
    }

  }

})(jQuery);














//! Script# Core Runtime
//! More information at http://projects.nikhilk.net/ScriptSharp
//!

///////////////////////////////////////////////////////////////////////////////
// Globals

(function () {
  var globals = {
    version: '0.7.4.0',

    isUndefined: function (o) {
      return (o === undefined);
    },

    isNull: function (o) {
      return (o === null);
    },

    isNullOrUndefined: function (o) {
      return (o === null) || (o === undefined);
    },

    isValue: function (o) {
      return (o !== null) && (o !== undefined);
    }
  };

  var started = false;
  var startCallbacks = [];

  function onStartup(cb) {
    startCallbacks ? startCallbacks.push(cb) : setTimeout(cb, 0);
  }
  function startup() {
    if (startCallbacks) {
      var callbacks = startCallbacks;
      startCallbacks = null;
      for (var i = 0, l = callbacks.length; i < l; i++) {
        callbacks[i]();
      }
    }
  }
  if (document.addEventListener) {
    document.readyState == 'complete' ? startup() : document.addEventListener('DOMContentLoaded', startup, false);
  }
  else if (window.attachEvent) {
    window.attachEvent('onload', function () {
      startup();
    });
  }

  var ss = window.ss;
  if (!ss) {
    window.ss = ss = {
      init: onStartup,
      ready: onStartup
    };
  }
  for (var n in globals) {
    ss[n] = globals[n];
  }
})();

///////////////////////////////////////////////////////////////////////////////
// Object Extensions

Object.__typeName = 'Object';
Object.__baseType = null;

Object.clearKeys = function Object$clearKeys(d) {
    for (var n in d) {
        delete d[n];
    }
}

Object.keyExists = function Object$keyExists(d, key) {
    return d[key] !== undefined;
}

if (!Object.keys) {
    Object.keys = function Object$keys(d) {
        var keys = [];
        for (var n in d) {
            keys.push(n);
        }
        return keys;
    }

    Object.getKeyCount = function Object$getKeyCount(d) {
        var count = 0;
        for (var n in d) {
            count++;
        }
        return count;
    }
}
else {
    Object.getKeyCount = function Object$getKeyCount(d) {
        return Object.keys(d).length;
    }
}

///////////////////////////////////////////////////////////////////////////////
// Boolean Extensions

Boolean.__typeName = 'Boolean';

Boolean.parse = function Boolean$parse(s) {
    return (s.toLowerCase() == 'true');
}

///////////////////////////////////////////////////////////////////////////////
// Number Extensions

Number.__typeName = 'Number';

Number.parse = function Number$parse(s) {
    if (!s || !s.length) {
        return 0;
    }
    if ((s.indexOf('.') >= 0) || (s.indexOf('e') >= 0) ||
        s.endsWith('f') || s.endsWith('F')) {
        return parseFloat(s);
    }
    return parseInt(s, 10);
}

Number.prototype.format = function Number$format(format) {
    if (ss.isNullOrUndefined(format) || (format.length == 0) || (format == 'i')) {
        return this.toString();
    }
    return this._netFormat(format, false);
}

Number.prototype.localeFormat = function Number$format(format) {
    if (ss.isNullOrUndefined(format) || (format.length == 0) || (format == 'i')) {
        return this.toLocaleString();
    }
    return this._netFormat(format, true);
}

Number._commaFormat = function Number$_commaFormat(number, groups, decimal, comma) {
    var decimalPart = null;
    var decimalIndex = number.indexOf(decimal);
    if (decimalIndex > 0) {
        decimalPart = number.substr(decimalIndex);
        number = number.substr(0, decimalIndex);
    }

    var negative = number.startsWith('-');
    if (negative) {
        number = number.substr(1);
    }

    var groupIndex = 0;
    var groupSize = groups[groupIndex];
    if (number.length < groupSize) {
        return decimalPart ? number + decimalPart : number;
    }

    var index = number.length;
    var s = '';
    var done = false;
    while (!done) {
        var length = groupSize;
        var startIndex = index - length;
        if (startIndex < 0) {
            groupSize += startIndex;
            length += startIndex;
            startIndex = 0;
            done = true;
        }
        if (!length) {
            break;
        }
        
        var part = number.substr(startIndex, length);
        if (s.length) {
            s = part + comma + s;
        }
        else {
            s = part;
        }
        index -= length;

        if (groupIndex < groups.length - 1) {
            groupIndex++;
            groupSize = groups[groupIndex];
        }
    }

    if (negative) {
        s = '-' + s;
    }    
    return decimalPart ? s + decimalPart : s;
}

Number.prototype._netFormat = function Number$_netFormat(format, useLocale) {
    var nf = useLocale ? ss.CultureInfo.CurrentCulture.numberFormat : ss.CultureInfo.InvariantCulture.numberFormat;

    var s = '';    
    var precision = -1;
    
    if (format.length > 1) {
        precision = parseInt(format.substr(1));
    }

    var fs = format.charAt(0);
    switch (fs) {
        case 'd': case 'D':
            s = parseInt(Math.abs(this)).toString();
            if (precision != -1) {
                s = s.padLeft(precision, '0');
            }
            if (this < 0) {
                s = '-' + s;
            }
            break;
        case 'x': case 'X':
            s = parseInt(Math.abs(this)).toString(16);
            if (fs == 'X') {
                s = s.toUpperCase();
            }
            if (precision != -1) {
                s = s.padLeft(precision, '0');
            }
            break;
        case 'e': case 'E':
            if (precision == -1) {
                s = this.toExponential();
            }
            else {
                s = this.toExponential(precision);
            }
            if (fs == 'E') {
                s = s.toUpperCase();
            }
            break;
        case 'f': case 'F':
        case 'n': case 'N':
            if (precision == -1) {
                precision = nf.numberDecimalDigits;
            }
            s = this.toFixed(precision).toString();
            if (precision && (nf.numberDecimalSeparator != '.')) {
                var index = s.indexOf('.');
                s = s.substr(0, index) + nf.numberDecimalSeparator + s.substr(index + 1);
            }
            if ((fs == 'n') || (fs == 'N')) {
                s = Number._commaFormat(s, nf.numberGroupSizes, nf.numberDecimalSeparator, nf.numberGroupSeparator);
            }
            break;
        case 'c': case 'C':
            if (precision == -1) {
                precision = nf.currencyDecimalDigits;
            }
            s = Math.abs(this).toFixed(precision).toString();
            if (precision && (nf.currencyDecimalSeparator != '.')) {
                var index = s.indexOf('.');
                s = s.substr(0, index) + nf.currencyDecimalSeparator + s.substr(index + 1);
            }
            s = Number._commaFormat(s, nf.currencyGroupSizes, nf.currencyDecimalSeparator, nf.currencyGroupSeparator);
            if (this < 0) {
                s = String.format(nf.currencyNegativePattern, s);
            }
            else {
                s = String.format(nf.currencyPositivePattern, s);
            }
            break;
        case 'p': case 'P':
            if (precision == -1) {
                precision = nf.percentDecimalDigits;
            }
            s = (Math.abs(this) * 100.0).toFixed(precision).toString();
            if (precision && (nf.percentDecimalSeparator != '.')) {
                var index = s.indexOf('.');
                s = s.substr(0, index) + nf.percentDecimalSeparator + s.substr(index + 1);
            }
            s = Number._commaFormat(s, nf.percentGroupSizes, nf.percentDecimalSeparator, nf.percentGroupSeparator);
            if (this < 0) {
                s = String.format(nf.percentNegativePattern, s);
            }
            else {
                s = String.format(nf.percentPositivePattern, s);
            }
            break;
    }

    return s;
}

///////////////////////////////////////////////////////////////////////////////
// String Extensions

String.__typeName = 'String';
String.Empty = '';

String.compare = function String$compare(s1, s2, ignoreCase) {
    if (ignoreCase) {
        if (s1) {
            s1 = s1.toUpperCase();
        }
        if (s2) {
            s2 = s2.toUpperCase();
        }
    }
    s1 = s1 || '';
    s2 = s2 || '';

    if (s1 == s2) {
        return 0;
    }
    if (s1 < s2) {
        return -1;
    }
    return 1;
}

String.prototype.compareTo = function String$compareTo(s, ignoreCase) {
    return String.compare(this, s, ignoreCase);
}

String.concat = function String$concat() {
    if (arguments.length === 2) {
        return arguments[0] + arguments[1];
    }
    return Array.prototype.join.call(arguments, '');
}

String.prototype.endsWith = function String$endsWith(suffix) {
    if (!suffix.length) {
        return true;
    }
    if (suffix.length > this.length) {
        return false;
    }
    return (this.substr(this.length - suffix.length) == suffix);
}

String.equals = function String$equals1(s1, s2, ignoreCase) {
    return String.compare(s1, s2, ignoreCase) == 0;
}

String._format = function String$_format(format, values, useLocale) {
    if (!String._formatRE) {
        String._formatRE = /(\{[^\}^\{]+\})/g;
    }

    return format.replace(String._formatRE,
                          function(str, m) {
                              var index = parseInt(m.substr(1));
                              var value = values[index + 1];
                              if (ss.isNullOrUndefined(value)) {
                                  return '';
                              }
                              if (value.format) {
                                  var formatSpec = null;
                                  var formatIndex = m.indexOf(':');
                                  if (formatIndex > 0) {
                                      formatSpec = m.substring(formatIndex + 1, m.length - 1);
                                  }
                                  return useLocale ? value.localeFormat(formatSpec) : value.format(formatSpec);
                              }
                              else {
                                  return useLocale ? value.toLocaleString() : value.toString();
                              }
                          });
}

String.format = function String$format(format) {
    return String._format(format, arguments, /* useLocale */ false);
}

String.fromChar = function String$fromChar(ch, count) {
    var s = ch;
    for (var i = 1; i < count; i++) {
        s += ch;
    }
    return s;
}

String.prototype.htmlDecode = function String$htmlDecode() {
    var div = document.createElement('div');
    div.innerHTML = this;
    return div.textContent || div.innerText;
}

String.prototype.htmlEncode = function String$htmlEncode() {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(this));
    return div.innerHTML.replace(/\"/g, '&quot;');
}

String.prototype.indexOfAny = function String$indexOfAny(chars, startIndex, count) {
    var length = this.length;
    if (!length) {
        return -1;
    }

    startIndex = startIndex || 0;
    count = count || length;

    var endIndex = startIndex + count - 1;
    if (endIndex >= length) {
        endIndex = length - 1;
    }

    for (var i = startIndex; i <= endIndex; i++) {
        if (chars.indexOf(this.charAt(i)) >= 0) {
            return i;
        }
    }
    return -1;
}

String.prototype.insert = function String$insert(index, value) {
    if (!value) {
        return this;
    }
    if (!index) {
        return value + this;
    }
    var s1 = this.substr(0, index);
    var s2 = this.substr(index);
    return s1 + value + s2;
}

String.isNullOrEmpty = function String$isNullOrEmpty(s) {
    return !s || !s.length;
}

String.prototype.lastIndexOfAny = function String$lastIndexOfAny(chars, startIndex, count) {
    var length = this.length;
    if (!length) {
        return -1;
    }

    startIndex = startIndex || length - 1;
    count = count || length;

    var endIndex = startIndex - count + 1;
    if (endIndex < 0) {
        endIndex = 0;
    }

    for (var i = startIndex; i >= endIndex; i--) {
        if (chars.indexOf(this.charAt(i)) >= 0) {
            return i;
        }
    }
    return -1;
}

String.localeFormat = function String$localeFormat(format) {
    return String._format(format, arguments, /* useLocale */ true);
}

String.prototype.padLeft = function String$padLeft(totalWidth, ch) {
    if (this.length < totalWidth) {
        ch = ch || ' ';
        return String.fromChar(ch, totalWidth - this.length) + this;
    }
    return this;
}

String.prototype.padRight = function String$padRight(totalWidth, ch) {
    if (this.length < totalWidth) {
        ch = ch || ' ';
        return this + String.fromChar(ch, totalWidth - this.length);
    }
    return this;
}

String.prototype.remove = function String$remove(index, count) {
    if (!count || ((index + count) > this.length)) {
        return this.substr(0, index);
    }
    return this.substr(0, index) + this.substr(index + count);
}

String.prototype.replaceAll = function String$replaceAll(oldValue, newValue) {
    newValue = newValue || '';
    return this.split(oldValue).join(newValue);
}

String.prototype.startsWith = function String$startsWith(prefix) {
    if (!prefix.length) {
        return true;
    }
    if (prefix.length > this.length) {
        return false;
    }
    return (this.substr(0, prefix.length) == prefix);
}

if (!String.prototype.trim) {
    String.prototype.trim = function String$trim() {
        return this.trimEnd().trimStart();
    }
}

String.prototype.trimEnd = function String$trimEnd() {
    return this.replace(/\s*$/, '');
}

String.prototype.trimStart = function String$trimStart() {
    return this.replace(/^\s*/, '');
}

///////////////////////////////////////////////////////////////////////////////
// Array Extensions

Array.__typeName = 'Array';
Array.__interfaces = [ ss.IEnumerable ];

Array.prototype.add = function Array$add(item) {
    this[this.length] = item;
}

Array.prototype.addRange = function Array$addRange(items) {
    this.push.apply(this, items);
}

Array.prototype.aggregate = function Array$aggregate(seed, callback, instance) {
    var length = this.length;
    for (var i = 0; i < length; i++) {
        if (i in this) {
            seed = callback.call(instance, seed, this[i], i, this);
        }
    }
    return seed;
}

Array.prototype.clear = function Array$clear() {
    this.length = 0;
}

Array.prototype.clone = function Array$clone() {
    if (this.length === 1) {
        return [this[0]];
    }
    else {
        return Array.apply(null, this);
    }
}

Array.prototype.contains = function Array$contains(item) {
    var index = this.indexOf(item);
    return (index >= 0);
}

Array.prototype.dequeue = function Array$dequeue() {
    return this.shift();
}

Array.prototype.enqueue = function Array$enqueue(item) {
    // We record that this array instance is a queue, so we
    // can implement the right behavior in the peek method.
    this._queue = true;
    this.push(item);
}

Array.prototype.peek = function Array$peek() {
    if (this.length) {
        var index = this._queue ? 0 : this.length - 1;
        return this[index];
    }
    return null;
}

if (!Array.prototype.every) {
    Array.prototype.every = function Array$every(callback, instance) {
        var length = this.length;
        for (var i = 0; i < length; i++) {
            if (i in this && !callback.call(instance, this[i], i, this)) {
                return false;
            }
        }
        return true;
    }
}

Array.prototype.extract = function Array$extract(index, count) {
    if (!count) {
        return this.slice(index);
    }
    return this.slice(index, index + count);
}

if (!Array.prototype.filter) {
    Array.prototype.filter = function Array$filter(callback, instance) {
        var length = this.length;    
        var filtered = [];
        for (var i = 0; i < length; i++) {
            if (i in this) {
                var val = this[i];
                if (callback.call(instance, val, i, this)) {
                    filtered.push(val);
                }
            }
        }
        return filtered;
    }
}

if (!Array.prototype.forEach) {
    Array.prototype.forEach = function Array$forEach(callback, instance) {
        var length = this.length;
        for (var i = 0; i < length; i++) {
            if (i in this) {
                callback.call(instance, this[i], i, this);
            }
        }
    }
}

Array.prototype.getEnumerator = function Array$getEnumerator() {
    return new ss.ArrayEnumerator(this);
}

Array.prototype.groupBy = function Array$groupBy(callback, instance) {
    var length = this.length;
    var groups = [];
    var keys = {};
    for (var i = 0; i < length; i++) {
        if (i in this) {
            var key = callback.call(instance, this[i], i);
            if (String.isNullOrEmpty(key)) {
                continue;
            }
            var items = keys[key];
            if (!items) {
                items = [];
                items.key = key;

                keys[key] = items;
                groups.add(items);
            }
            items.add(this[i]);
        }
    }
    return groups;
}

Array.prototype.index = function Array$index(callback, instance) {
    var length = this.length;
    var items = {};
    for (var i = 0; i < length; i++) {
        if (i in this) {
            var key = callback.call(instance, this[i], i);
            if (String.isNullOrEmpty(key)) {
                continue;
            }
            items[key] = this[i];
        }
    }
    return items;
}

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function Array$indexOf(item, startIndex) {
        startIndex = startIndex || 0;
        var length = this.length;
        if (length) {
            for (var index = startIndex; index < length; index++) {
                if (this[index] === item) {
                    return index;
                }
            }
        }
        return -1;
    }
}

Array.prototype.insert = function Array$insert(index, item) {
    this.splice(index, 0, item);
}

Array.prototype.insertRange = function Array$insertRange(index, items) {
    if (index === 0) {
        this.unshift.apply(this, items);
    }
    else {
        for (var i = 0; i < items.length; i++) {
            this.splice(index + i, 0, items[i]);
        }
    }
}

if (!Array.prototype.map) {
    Array.prototype.map = function Array$map(callback, instance) {
        var length = this.length;
        var mapped = new Array(length);
        for (var i = 0; i < length; i++) {
            if (i in this) {
                mapped[i] = callback.call(instance, this[i], i, this);
            }
        }
        return mapped;
    }
}

Array.parse = function Array$parse(s) {
    return eval('(' + s + ')');
}

Array.prototype.remove = function Array$remove(item) {
    var index = this.indexOf(item);
    if (index >= 0) {
        this.splice(index, 1);
        return true;
    }
    return false;
}

Array.prototype.removeAt = function Array$removeAt(index) {
    this.splice(index, 1);
}

Array.prototype.removeRange = function Array$removeRange(index, count) {
    return this.splice(index, count);
}

if (!Array.prototype.some) {
    Array.prototype.some = function Array$some(callback, instance) {
        var length = this.length;
        for (var i = 0; i < length; i++) {
            if (i in this && callback.call(instance, this[i], i, this)) {
                return true;
            }
        }
        return false;
    }
}

Array.toArray = function Array$toArray(obj) {
    return Array.prototype.slice.call(obj);
}

///////////////////////////////////////////////////////////////////////////////
// RegExp Extensions

RegExp.__typeName = 'RegExp';

RegExp.parse = function RegExp$parse(s) {
    if (s.startsWith('/')) {
        var endSlashIndex = s.lastIndexOf('/');
        if (endSlashIndex > 1) {
            var expression = s.substring(1, endSlashIndex);
            var flags = s.substr(endSlashIndex + 1);
            return new RegExp(expression, flags);
        }
    }

    return null;    
}

///////////////////////////////////////////////////////////////////////////////
// Date Extensions

Date.__typeName = 'Date';

Date.empty = null;

Date.get_now = function Date$get_now() {
    return new Date();
}

Date.get_today = function Date$get_today() {
    var d = new Date();
    return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

Date.isEmpty = function Date$isEmpty(d) {
    return (d === null) || (d.valueOf() === 0);
}

Date.prototype.format = function Date$format(format) {
    if (ss.isNullOrUndefined(format) || (format.length == 0) || (format == 'i')) {
        return this.toString();
    }
    if (format == 'id') {
        return this.toDateString();
    }
    if (format == 'it') {
        return this.toTimeString();
    }

    return this._netFormat(format, false);
}

Date.prototype.localeFormat = function Date$localeFormat(format) {
    if (ss.isNullOrUndefined(format) || (format.length == 0) || (format == 'i')) {
        return this.toLocaleString();
    }
    if (format == 'id') {
        return this.toLocaleDateString();
    }
    if (format == 'it') {
        return this.toLocaleTimeString();
    }

    return this._netFormat(format, true);
}

Date.prototype._netFormat = function Date$_netFormat(format, useLocale) {
    var dt = this;
    var dtf = useLocale ? ss.CultureInfo.CurrentCulture.dateFormat : ss.CultureInfo.InvariantCulture.dateFormat;

    if (format.length == 1) {
        switch (format) {
            case 'f': format = dtf.longDatePattern + ' ' + dtf.shortTimePattern; break;
            case 'F': format = dtf.dateTimePattern; break;

            case 'd': format = dtf.shortDatePattern; break;
            case 'D': format = dtf.longDatePattern; break;

            case 't': format = dtf.shortTimePattern; break;
            case 'T': format = dtf.longTimePattern; break;

            case 'g': format = dtf.shortDatePattern + ' ' + dtf.shortTimePattern; break;
            case 'G': format = dtf.shortDatePattern + ' ' + dtf.longTimePattern; break;

            case 'R': case 'r':
                dtf = ss.CultureInfo.InvariantCulture.dateFormat;
                format = dtf.gmtDateTimePattern;
                break;
            case 'u': format = dtf.universalDateTimePattern; break;
            case 'U':
                format = dtf.dateTimePattern;
                dt = new Date(dt.getUTCFullYear(), dt.getUTCMonth(), dt.getUTCDate(),
                              dt.getUTCHours(), dt.getUTCMinutes(), dt.getUTCSeconds(), dt.getUTCMilliseconds());
                break;

            case 's': format = dtf.sortableDateTimePattern; break;
        }
    }

    if (format.charAt(0) == '%') {
        format = format.substr(1);
    }

    if (!Date._formatRE) {
        Date._formatRE = /'.*?[^\\]'|dddd|ddd|dd|d|MMMM|MMM|MM|M|yyyy|yy|y|hh|h|HH|H|mm|m|ss|s|tt|t|fff|ff|f|zzz|zz|z/g;
    }

    var re = Date._formatRE;
    var sb = new ss.StringBuilder();

    re.lastIndex = 0;
    while (true) {
        var index = re.lastIndex;
        var match = re.exec(format);

        sb.append(format.slice(index, match ? match.index : format.length));
        if (!match) {
            break;
        }

        var fs = match[0];
        var part = fs;
        switch (fs) {
            case 'dddd':
                part = dtf.dayNames[dt.getDay()];
                break;
            case 'ddd':
                part = dtf.shortDayNames[dt.getDay()];
                break;
            case 'dd':
                part = dt.getDate().toString().padLeft(2, '0');
                break;
            case 'd':
                part = dt.getDate();
                break;
            case 'MMMM':
                part = dtf.monthNames[dt.getMonth()];
                break;
            case 'MMM':
                part = dtf.shortMonthNames[dt.getMonth()];
                break;
            case 'MM':
                part = (dt.getMonth() + 1).toString().padLeft(2, '0');
                break;
            case 'M':
                part = (dt.getMonth() + 1);
                break;
            case 'yyyy':
                part = dt.getFullYear();
                break;
            case 'yy':
                part = (dt.getFullYear() % 100).toString().padLeft(2, '0');
                break;
            case 'y':
                part = (dt.getFullYear() % 100);
                break;
            case 'h': case 'hh':
                part = dt.getHours() % 12;
                if (!part) {
                    part = '12';
                }
                else if (fs == 'hh') {
                    part = part.toString().padLeft(2, '0');
                }
                break;
            case 'HH':
                part = dt.getHours().toString().padLeft(2, '0');
                break;
            case 'H':
                part = dt.getHours();
                break;
            case 'mm':
                part = dt.getMinutes().toString().padLeft(2, '0');
                break;
            case 'm':
                part = dt.getMinutes();
                break;
            case 'ss':
                part = dt.getSeconds().toString().padLeft(2, '0');
                break;
            case 's':
                part = dt.getSeconds();
                break;
            case 't': case 'tt':
                part = (dt.getHours() < 12) ? dtf.amDesignator : dtf.pmDesignator;
                if (fs == 't') {
                    part = part.charAt(0);
                }
                break;
            case 'fff':
                part = dt.getMilliseconds().toString().padLeft(3, '0');
                break;
            case 'ff':
                part = dt.getMilliseconds().toString().padLeft(3).substr(0, 2);
                break;
            case 'f':
                part = dt.getMilliseconds().toString().padLeft(3).charAt(0);
                break;
            case 'z':
                part = dt.getTimezoneOffset() / 60;
                part = ((part >= 0) ? '-' : '+') + Math.floor(Math.abs(part));
                break;
            case 'zz': case 'zzz':
                part = dt.getTimezoneOffset() / 60;
                part = ((part >= 0) ? '-' : '+') + Math.floor(Math.abs(part)).toString().padLeft(2, '0');
                if (fs == 'zzz') {
                    part += dtf.timeSeparator + Math.abs(dt.getTimezoneOffset() % 60).toString().padLeft(2, '0');
                }
                break;
            default:
                if (part.charAt(0) == '\'') {
                    part = part.substr(1, part.length - 2).replace(/\\'/g, '\'');
                }
                break;
        }
        sb.append(part);
    }

    return sb.toString();
}

Date.parseDate = function Date$parse(s) {
    // Date.parse returns the number of milliseconds
    // so we use that to create an actual Date instance
    return new Date(Date.parse(s));
}

///////////////////////////////////////////////////////////////////////////////
// Error Extensions

Error.__typeName = 'Error';

Error.prototype.popStackFrame = function Error$popStackFrame() {
    if (ss.isNullOrUndefined(this.stack) ||
        ss.isNullOrUndefined(this.fileName) ||
        ss.isNullOrUndefined(this.lineNumber)) {
        return;
    }

    var stackFrames = this.stack.split('\n');
    var currentFrame = stackFrames[0];
    var pattern = this.fileName + ':' + this.lineNumber;
    while (!ss.isNullOrUndefined(currentFrame) &&
           currentFrame.indexOf(pattern) === -1) {
        stackFrames.shift();
        currentFrame = stackFrames[0];
    }

    var nextFrame = stackFrames[1];
    if (isNullOrUndefined(nextFrame)) {
        return;
    }

    var nextFrameParts = nextFrame.match(/@(.*):(\d+)$/);
    if (ss.isNullOrUndefined(nextFrameParts)) {
        return;
    }

    stackFrames.shift();
    this.stack = stackFrames.join("\n");
    this.fileName = nextFrameParts[1];
    this.lineNumber = parseInt(nextFrameParts[2]);
}

Error.createError = function Error$createError(message, errorInfo, innerException) {
    var e = new Error(message);
    if (errorInfo) {
        for (var v in errorInfo) {
            e[v] = errorInfo[v];
        }
    }
    if (innerException) {
        e.innerException = innerException;
    }

    e.popStackFrame();
    return e;
}

///////////////////////////////////////////////////////////////////////////////
// Debug Extensions

ss.Debug = window.Debug || function() {};
ss.Debug.__typeName = 'Debug';

if (!ss.Debug.writeln) {
    ss.Debug.writeln = function Debug$writeln(text) {
        if (window.console) {
            if (window.console.debug) {
                window.console.debug(text);
                return;
            }
            else if (window.console.log) {
                window.console.log(text);
                return;
            }
        }
        else if (window.opera &&
            window.opera.postError) {
            window.opera.postError(text);
            return;
        }
    }
}

ss.Debug._fail = function Debug$_fail(message) {
    ss.Debug.writeln(message);
    eval('debugger;');
}

ss.Debug.assert = function Debug$assert(condition, message) {
    if (!condition) {
        message = 'Assert failed: ' + message;
        if (confirm(message + '\r\n\r\nBreak into debugger?')) {
            ss.Debug._fail(message);
        }
    }
}

ss.Debug.fail = function Debug$fail(message) {
    ss.Debug._fail(message);
}

///////////////////////////////////////////////////////////////////////////////
// Type System Implementation

window.Type = Function;
Type.__typeName = 'Type';

window.__Namespace = function(name) {
    this.__typeName = name;
}
__Namespace.prototype = {
    __namespace: true,
    getName: function() {
        return this.__typeName;
    }
}

Type.registerNamespace = function Type$registerNamespace(name) {
    if (!window.__namespaces) {
        window.__namespaces = {};
    }
    if (!window.__rootNamespaces) {
        window.__rootNamespaces = [];
    }

    if (window.__namespaces[name]) {
        return;
    }

    var ns = window;
    var nameParts = name.split('.');

    for (var i = 0; i < nameParts.length; i++) {
        var part = nameParts[i];
        var nso = ns[part];
        if (!nso) {
            ns[part] = nso = new __Namespace(nameParts.slice(0, i + 1).join('.'));
            if (i == 0) {
                window.__rootNamespaces.add(nso);
            }
        }
        ns = nso;
    }

    window.__namespaces[name] = ns;
}

Type.prototype.registerClass = function Type$registerClass(name, baseType, interfaceType) {
    this.prototype.constructor = this;
    this.__typeName = name;
    this.__class = true;
    this.__baseType = baseType || Object;
    if (baseType) {
        this.__basePrototypePending = true;
    }

    if (interfaceType) {
        this.__interfaces = [];
        for (var i = 2; i < arguments.length; i++) {
            interfaceType = arguments[i];
            this.__interfaces.add(interfaceType);
        }
    }
}

Type.prototype.registerInterface = function Type$createInterface(name) {
    this.__typeName = name;
    this.__interface = true;
}

Type.prototype.registerEnum = function Type$createEnum(name, flags) {
    for (var field in this.prototype) {
         this[field] = this.prototype[field];
    }

    this.__typeName = name;
    this.__enum = true;
    if (flags) {
        this.__flags = true;
    }
}

Type.prototype.setupBase = function Type$setupBase() {
    if (this.__basePrototypePending) {
        var baseType = this.__baseType;
        if (baseType.__basePrototypePending) {
            baseType.setupBase();
        }

        for (var memberName in baseType.prototype) {
            var memberValue = baseType.prototype[memberName];
            if (!this.prototype[memberName]) {
                this.prototype[memberName] = memberValue;
            }
        }

        delete this.__basePrototypePending;
    }
}

if (!Type.prototype.resolveInheritance) {
    // This function is not used by Script#; Visual Studio relies on it
    // for JavaScript IntelliSense support of derived types.
    Type.prototype.resolveInheritance = Type.prototype.setupBase;
}

Type.prototype.initializeBase = function Type$initializeBase(instance, args) {
    if (this.__basePrototypePending) {
        this.setupBase();
    }

    if (!args) {
        this.__baseType.apply(instance);
    }
    else {
        this.__baseType.apply(instance, args);
    }
}

Type.prototype.callBaseMethod = function Type$callBaseMethod(instance, name, args) {
    var baseMethod = this.__baseType.prototype[name];
    if (!args) {
        return baseMethod.apply(instance);
    }
    else {
        return baseMethod.apply(instance, args);
    }
}

Type.prototype.get_baseType = function Type$get_baseType() {
    return this.__baseType || null;
}

Type.prototype.get_fullName = function Type$get_fullName() {
    return this.__typeName;
}

Type.prototype.get_name = function Type$get_name() {
    var fullName = this.__typeName;
    var nsIndex = fullName.lastIndexOf('.');
    if (nsIndex > 0) {
        return fullName.substr(nsIndex + 1);
    }
    return fullName;
}

Type.prototype.getInterfaces = function Type$getInterfaces() {
    return this.__interfaces;
}

Type.prototype.isInstanceOfType = function Type$isInstanceOfType(instance) {
    if (ss.isNullOrUndefined(instance)) {
        return false;
    }
    if ((this == Object) || (instance instanceof this)) {
        return true;
    }

    var type = Type.getInstanceType(instance);
    return this.isAssignableFrom(type);
}

Type.prototype.isAssignableFrom = function Type$isAssignableFrom(type) {
    if ((this == Object) || (this == type)) {
        return true;
    }
    if (this.__class) {
        var baseType = type.__baseType;
        while (baseType) {
            if (this == baseType) {
                return true;
            }
            baseType = baseType.__baseType;
        }
    }
    else if (this.__interface) {
        var interfaces = type.__interfaces;
        if (interfaces && interfaces.contains(this)) {
            return true;
        }

        var baseType = type.__baseType;
        while (baseType) {
            interfaces = baseType.__interfaces;
            if (interfaces && interfaces.contains(this)) {
                return true;
            }
            baseType = baseType.__baseType;
        }
    }
    return false;
}

Type.isClass = function Type$isClass(type) {
    return (type.__class == true);
}

Type.isEnum = function Type$isEnum(type) {
    return (type.__enum == true);
}

Type.isFlags = function Type$isFlags(type) {
    return ((type.__enum == true) && (type.__flags == true));
}

Type.isInterface = function Type$isInterface(type) {
    return (type.__interface == true);
}

Type.isNamespace = function Type$isNamespace(object) {
    return (object.__namespace == true);
}

Type.canCast = function Type$canCast(instance, type) {
    return type.isInstanceOfType(instance);
}

Type.safeCast = function Type$safeCast(instance, type) {
    if (type.isInstanceOfType(instance)) {
        return instance;
    }
    return null;
}

Type.getInstanceType = function Type$getInstanceType(instance) {
    var ctor = null;

    // NOTE: We have to catch exceptions because the constructor
    //       cannot be looked up on native COM objects
    try {
        ctor = instance.constructor;
    }
    catch (ex) {
    }
    if (!ctor || !ctor.__typeName) {
        ctor = Object;
    }
    return ctor;
}

Type.getType = function Type$getType(typeName) {
    if (!typeName) {
        return null;
    }

    if (!Type.__typeCache) {
        Type.__typeCache = {};
    }

    var type = Type.__typeCache[typeName];
    if (!type) {
        type = eval(typeName);
        Type.__typeCache[typeName] = type;
    }
    return type;
}

Type.parse = function Type$parse(typeName) {
    return Type.getType(typeName);
}

///////////////////////////////////////////////////////////////////////////////
// Delegate

ss.Delegate = function Delegate$() {
}
ss.Delegate.registerClass('Delegate');

ss.Delegate.empty = function() { }

ss.Delegate._contains = function Delegate$_contains(targets, object, method) {
    for (var i = 0; i < targets.length; i += 2) {
        if (targets[i] === object && targets[i + 1] === method) {
            return true;
        }
    }
    return false;
}

ss.Delegate._create = function Delegate$_create(targets) {
    var delegate = function() {
        if (targets.length == 2) {
            return targets[1].apply(targets[0], arguments);
        }
        else {
            var clone = targets.clone();
            for (var i = 0; i < clone.length; i += 2) {
                if (ss.Delegate._contains(targets, clone[i], clone[i + 1])) {
                    clone[i + 1].apply(clone[i], arguments);
                }
            }
            return null;
        }
    };
    delegate._targets = targets;

    return delegate;
}

ss.Delegate.create = function Delegate$create(object, method) {
    if (!object) {
        return method;
    }
    return ss.Delegate._create([object, method]);
}

ss.Delegate.combine = function Delegate$combine(delegate1, delegate2) {
    if (!delegate1) {
        if (!delegate2._targets) {
            return ss.Delegate.create(null, delegate2);
        }
        return delegate2;
    }
    if (!delegate2) {
        if (!delegate1._targets) {
            return ss.Delegate.create(null, delegate1);
        }
        return delegate1;
    }

    var targets1 = delegate1._targets ? delegate1._targets : [null, delegate1];
    var targets2 = delegate2._targets ? delegate2._targets : [null, delegate2];

    return ss.Delegate._create(targets1.concat(targets2));
}

ss.Delegate.remove = function Delegate$remove(delegate1, delegate2) {
    if (!delegate1 || (delegate1 === delegate2)) {
        return null;
    }
    if (!delegate2) {
        return delegate1;
    }

    var targets = delegate1._targets;
    var object = null;
    var method;
    if (delegate2._targets) {
        object = delegate2._targets[0];
        method = delegate2._targets[1];
    }
    else {
        method = delegate2;
    }

    for (var i = 0; i < targets.length; i += 2) {
        if ((targets[i] === object) && (targets[i + 1] === method)) {
            if (targets.length == 2) {
                return null;
            }
            targets.splice(i, 2);
            return ss.Delegate._create(targets);
        }
    }

    return delegate1;
}

ss.Delegate.createExport = function Delegate$createExport(delegate, multiUse, name) {
    // Generate a unique name if one is not specified
    name = name || '__' + (new Date()).valueOf();

    // Exported delegates go on window (so they are callable using a simple identifier).

    // Multi-use delegates are exported directly; for the rest a stub is exported, and the stub
    // first deletes, and then invokes the actual delegate.
    window[name] = multiUse ? delegate : function() {
      try { delete window[name]; } catch(e) { window[name] = undefined; }
      delegate.apply(null, arguments);
    };

    return name;
}

ss.Delegate.deleteExport = function Delegate$deleteExport(name) {
    delete window[name];
}

ss.Delegate.clearExport = function Delegate$clearExport(name) {
    window[name] = ss.Delegate.empty;
}

///////////////////////////////////////////////////////////////////////////////
// CultureInfo

ss.CultureInfo = function CultureInfo$(name, numberFormat, dateFormat) {
    this.name = name;
    this.numberFormat = numberFormat;
    this.dateFormat = dateFormat;
}
ss.CultureInfo.registerClass('CultureInfo');

ss.CultureInfo.InvariantCulture = new ss.CultureInfo('en-US',
    {
        naNSymbol: 'NaN',
        negativeSign: '-',
        positiveSign: '+',
        negativeInfinityText: '-Infinity',
        positiveInfinityText: 'Infinity',
        
        percentSymbol: '%',
        percentGroupSizes: [3],
        percentDecimalDigits: 2,
        percentDecimalSeparator: '.',
        percentGroupSeparator: ',',
        percentPositivePattern: '{0} %',
        percentNegativePattern: '-{0} %',

        currencySymbol:'$',
        currencyGroupSizes: [3],
        currencyDecimalDigits: 2,
        currencyDecimalSeparator: '.',
        currencyGroupSeparator: ',',
        currencyNegativePattern: '(${0})',
        currencyPositivePattern: '${0}',

        numberGroupSizes: [3],
        numberDecimalDigits: 2,
        numberDecimalSeparator: '.',
        numberGroupSeparator: ','
    },
    {
        amDesignator: 'AM',
        pmDesignator: 'PM',

        dateSeparator: '/',
        timeSeparator: ':',

        gmtDateTimePattern: 'ddd, dd MMM yyyy HH:mm:ss \'GMT\'',
        universalDateTimePattern: 'yyyy-MM-dd HH:mm:ssZ',
        sortableDateTimePattern: 'yyyy-MM-ddTHH:mm:ss',
        dateTimePattern: 'dddd, MMMM dd, yyyy h:mm:ss tt',

        longDatePattern: 'dddd, MMMM dd, yyyy',
        shortDatePattern: 'M/d/yyyy',

        longTimePattern: 'h:mm:ss tt',
        shortTimePattern: 'h:mm tt',

        firstDayOfWeek: 0,
        dayNames: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
        shortDayNames: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
        minimizedDayNames: ['Su','Mo','Tu','We','Th','Fr','Sa'],

        monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December',''],
        shortMonthNames: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','']
    });
ss.CultureInfo.CurrentCulture = ss.CultureInfo.InvariantCulture;

///////////////////////////////////////////////////////////////////////////////
// IEnumerator

ss.IEnumerator = function IEnumerator$() { };
ss.IEnumerator.prototype = {
    get_current: null,
    moveNext: null,
    reset: null
}

ss.IEnumerator.getEnumerator = function ss_IEnumerator$getEnumerator(enumerable) {
    if (enumerable) {
        return enumerable.getEnumerator ? enumerable.getEnumerator() : new ss.ArrayEnumerator(enumerable);
    }
    return null;
}

ss.IEnumerator.registerInterface('IEnumerator');

///////////////////////////////////////////////////////////////////////////////
// IEnumerable

ss.IEnumerable = function IEnumerable$() { };
ss.IEnumerable.prototype = {
    getEnumerator: null
}
ss.IEnumerable.registerInterface('IEnumerable');

///////////////////////////////////////////////////////////////////////////////
// ArrayEnumerator

ss.ArrayEnumerator = function ArrayEnumerator$(array) {
    this._array = array;
    this._index = -1;
    this.current = null;
}
ss.ArrayEnumerator.prototype = {
    moveNext: function ArrayEnumerator$moveNext() {
        this._index++;
        this.current = this._array[this._index];
        return (this._index < this._array.length);
    },
    reset: function ArrayEnumerator$reset() {
        this._index = -1;
        this.current = null;
    }
}

ss.ArrayEnumerator.registerClass('ArrayEnumerator', null, ss.IEnumerator);

///////////////////////////////////////////////////////////////////////////////
// IDisposable

ss.IDisposable = function IDisposable$() { };
ss.IDisposable.prototype = {
    dispose: null
}
ss.IDisposable.registerInterface('IDisposable');

///////////////////////////////////////////////////////////////////////////////
// StringBuilder

ss.StringBuilder = function StringBuilder$(s) {
    this._parts = !ss.isNullOrUndefined(s) ? [s] : [];
    this.isEmpty = this._parts.length == 0;
}
ss.StringBuilder.prototype = {
    append: function StringBuilder$append(s) {
        if (!ss.isNullOrUndefined(s)) {
            this._parts.add(s);
            this.isEmpty = false;
        }
        return this;
    },

    appendLine: function StringBuilder$appendLine(s) {
        this.append(s);
        this.append('\r\n');
        this.isEmpty = false;
        return this;
    },

    clear: function StringBuilder$clear() {
        this._parts = [];
        this.isEmpty = true;
    },

    toString: function StringBuilder$toString(s) {
        return this._parts.join(s || '');
    }
};

ss.StringBuilder.registerClass('StringBuilder');

///////////////////////////////////////////////////////////////////////////////
// EventArgs

ss.EventArgs = function EventArgs$() {
}
ss.EventArgs.registerClass('EventArgs');

ss.EventArgs.Empty = new ss.EventArgs();

///////////////////////////////////////////////////////////////////////////////
// XMLHttpRequest

if (!window.XMLHttpRequest) {
    window.XMLHttpRequest = function() {
        var progIDs = [ 'Msxml2.XMLHTTP', 'Microsoft.XMLHTTP' ];

        for (var i = 0; i < progIDs.length; i++) {
            try {
                var xmlHttp = new ActiveXObject(progIDs[i]);
                return xmlHttp;
            }
            catch (ex) {
            }
        }

        return null;
    }
}

///////////////////////////////////////////////////////////////////////////////
// XmlDocumentParser

ss.parseXml = function(markup) {
    try {
        if (DOMParser) {
            var domParser = new DOMParser();
            return domParser.parseFromString(markup, 'text/xml');
        }
        else {
            var progIDs = [ 'Msxml2.DOMDocument.3.0', 'Msxml2.DOMDocument' ];
        
            for (var i = 0; i < progIDs.length; i++) {
                var xmlDOM = new ActiveXObject(progIDs[i]);
                xmlDOM.async = false;
                xmlDOM.loadXML(markup);
                xmlDOM.setProperty('SelectionLanguage', 'XPath');
                
                return xmlDOM;
            }
        }
    }
    catch (ex) {
    }

    return null;
}

///////////////////////////////////////////////////////////////////////////////
// CancelEventArgs

ss.CancelEventArgs = function CancelEventArgs$() {
    ss.CancelEventArgs.initializeBase(this);
    this.cancel = false;
}
ss.CancelEventArgs.registerClass('CancelEventArgs', ss.EventArgs);

///////////////////////////////////////////////////////////////////////////////
// Tuple

ss.Tuple = function (first, second, third) {
  this.first = first;
  this.second = second;
  if (arguments.length == 3) {
    this.third = third;
  }
}
ss.Tuple.registerClass('Tuple');

///////////////////////////////////////////////////////////////////////////////
// Observable

ss.Observable = function(v) {
    this._v = v;
    this._observers = null;
}
ss.Observable.prototype = {

  getValue: function () {
    this._observers = ss.Observable._captureObservers(this._observers);
    return this._v;
  },
  setValue: function (v) {
    if (this._v !== v) {
      this._v = v;

      var observers = this._observers;
      if (observers) {
        this._observers = null;
        ss.Observable._invalidateObservers(observers);
      }
    }
  }
};

ss.Observable._observerStack = [];
ss.Observable._observerRegistration = {
  dispose: function () {
    ss.Observable._observerStack.pop();
  }
}
ss.Observable.registerObserver = function (o) {
  ss.Observable._observerStack.push(o);
  return ss.Observable._observerRegistration;
}
ss.Observable._captureObservers = function (observers) {
  var registeredObservers = ss.Observable._observerStack;
  var observerCount = registeredObservers.length;

  if (observerCount) {
    observers = observers || [];
    for (var i = 0; i < observerCount; i++) {
      var observer = registeredObservers[i];
      if (!observers.contains(observer)) {
        observers.push(observer);
      }
    }
    return observers;
  }
  return null;
}
ss.Observable._invalidateObservers = function (observers) {
  for (var i = 0, len = observers.length; i < len; i++) {
    observers[i].invalidateObserver();
  }
}

ss.Observable.registerClass('Observable');


ss.ObservableCollection = function (items) {
  this._items = items || [];
  this._observers = null;
}
ss.ObservableCollection.prototype = {

  get_item: function (index) {
    this._observers = ss.Observable._captureObservers(this._observers);
    return this._items[index];
  },
  set_item: function (index, item) {
    this._items[index] = item;
    this._updated();
  },
  get_length: function () {
    this._observers = ss.Observable._captureObservers(this._observers);
    return this._items.length;
  },
  add: function (item) {
    this._items.push(item);
    this._updated();
  },
  clear: function () {
    this._items.clear();
    this._updated();
  },
  contains: function (item) {
    return this._items.contains(item);
  },
  getEnumerator: function () {
    this._observers = ss.Observable._captureObservers(this._observers);
    return this._items.getEnumerator();
  },
  indexOf: function (item) {
    return this._items.indexOf(item);
  },
  insert: function (index, item) {
    this._items.insert(index, item);
    this._updated();
  },
  remove: function (item) {
    if (this._items.remove(item)) {
      this._updated();
      return true;
    }
    return false;
  },
  removeAt: function (index) {
    this._items.removeAt(index);
    this._updated();
  },
  toArray: function () {
    return this._items;
  },
  _updated: function() {
    var observers = this._observers;
    if (observers) {
      this._observers = null;
      ss.Observable._invalidateObservers(observers);
    }
  }
}
ss.ObservableCollection.registerClass('ObservableCollection', null, ss.IEnumerable);

///////////////////////////////////////////////////////////////////////////////
// Interfaces

ss.IApplication = function() { };
ss.IApplication.registerInterface('IApplication');

ss.IContainer = function () { };
ss.IContainer.registerInterface('IContainer');

ss.IObjectFactory = function () { };
ss.IObjectFactory.registerInterface('IObjectFactory');

ss.IEventManager = function () { };
ss.IEventManager.registerInterface('IEventManager');

ss.IInitializable = function () { };
ss.IInitializable.registerInterface('IInitializable');












var CubicSpline;
CubicSpline = function () {
    function p(f, d, e, k) {
        var h, j, b, l, i, a, g, c, m, o, n; if (f != null && d != null) { b = e != null && k != null; c = f.length - 1; i = []; o = []; g = []; m = []; n = []; j = []; h = []; l = []; for (a = 0; 0 <= c ? a < c : a > c; 0 <= c ? a += 1 : a -= 1) i[a] = f[a + 1] - f[a]; if (b) { o[0] = 3 * (d[1] - d[0]) / i[0] - 3 * e; o[c] = 3 * k - 3 * (d[c] - d[c - 1]) / i[c - 1] } for (a = 1; 1 <= c ? a < c : a > c; 1 <= c ? a += 1 : a -= 1) o[a] = 3 / i[a] * (d[a + 1] - d[a]) - 3 / i[a - 1] * (d[a] - d[a - 1]); if (b) { g[0] = 2 * i[0]; m[0] = 0.5; n[0] = o[0] / g[0] } else { g[0] = 1; m[0] = 0; n[0] = 0 } for (a = 1; 1 <= c ? a < c : a > c; 1 <= c ? a += 1 : a -= 1) { g[a] = 2 * (f[a + 1] - f[a - 1]) - i[a - 1] * m[a - 1]; m[a] = i[a] / g[a]; n[a] = (o[a] - i[a - 1] * n[a - 1]) / g[a] } if (b) { g[c] = i[c - 1] * (2 - m[c - 1]); n[c] = (o[c] - i[c - 1] * n[c - 1]) / g[c]; j[c] = n[c] } else { g[c] = 1; n[c] = 0; j[c] = 0 } for (a = e = c - 1; e <= 0 ? a <= 0 : a >= 0; e <= 0 ? a += 1 : a -= 1) { j[a] = n[a] - m[a] * j[a + 1]; h[a] = (d[a + 1] - d[a]) / i[a] - i[a] * (j[a + 1] + 2 * j[a]) / 3; l[a] = (j[a + 1] - j[a]) / (3 * i[a]) } this.x = f.slice(0, c + 1); this.a = d.slice(0, c); this.b = h; this.c = j.slice(0, c); this.d = l }
    }
    p.prototype.derivative = function () {
        var f, d, e, k, h; d = new this.constructor; d.x = this.x.slice(0, this.x.length); d.a = this.b.slice(0, this.b.length); h = this.c; e = 0; for (k = h.length; e < k; e++) { f = h[e]; d.b = 2 * f } h = this.d; e = 0; for (k = h.length; e < k; e++) { f = h[e]; d.c = 3 * f } f = 0; for (e = this.d.length; 0 <= e ? f < e : f > e; 0 <= e ? f += 1 : f -= 1) d.d = 0; return d
    };
    p.prototype.interpolate = function (f) {
        var d, e; for (d = e = this.x.length - 1; e <= 0 ? d <= 0 : d >= 0; e <= 0 ? d += 1 : d -= 1) if (this.x[d] <= f) break; f = f - this.x[d]; return this.a[d] + this.b[d] * f + this.c[d] * Math.pow(f, 2) + this.d[d] * Math.pow(f, 3)
    };
    return p
} ();
















//! QuizzMakerScript.debug.js
//

(function($) {

////////////////////////////////////////////////////////////////////////////////
// Advice

window.Advice = function Advice() {
    /// <field name="_advices" type="Array" elementType="String" static="true">
    /// </field>
    /// <field name="_adviceHeaderTemplate" type="String" static="true">
    /// </field>
    /// <field name="_adviceFooterTemplate" type="String" static="true">
    /// </field>
    /// <field name="_adviceTemplate" type="String" static="true">
    /// </field>
}
Advice._buildAdvice = function Advice$_buildAdvice(testpage) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    var adjq = $(testpage).find('.advice > ul');
    adjq.html('');
    $(Advice._adviceHeaderTemplate).appendTo(adjq);
    for (var i = 0; i < Advice._advices.length; i++) {
        $(String.format(Advice._adviceTemplate, i + 1)).appendTo(adjq).find('.question-main').html(Advice._advices[i]);
    }
    $(Advice._adviceFooterTemplate).appendTo(adjq);
}


////////////////////////////////////////////////////////////////////////////////
// Basic

window.Basic = function Basic() {
    /// <field name="_categories" type="Object" static="true">
    /// </field>
    /// <field name="_basicLables" type="Array" elementType="String" static="true">
    /// </field>
    /// <field name="_basics" type="Array" elementType="String" static="true">
    /// </field>
    /// <field name="_basicTemplate" type="String" static="true">
    /// </field>
    /// <field name="_variableTemplate" type="String" static="true">
    /// </field>
    /// <field name="lastVariableIndexId" type="Number" integer="true" static="true">
    /// </field>
}
Basic._buildBasic = function Basic$_buildBasic(testpage, testBasic) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    /// <param name="testBasic" type="TestBasic">
    /// </param>
    var testjq = $(testpage);
    var bsjq = testjq.find('.basic > ul');
    bsjq.html('');
    for (var i = 0; i < Basic._basics.length; i++) {
        $(String.format(Basic._basicTemplate, Basic._basicLables[i])).appendTo(bsjq).find('.question-main').html(Basic._basics[i]);
    }
    var opjq = testjq.find('.test-category');
    opjq.html('<option value="">- Choose -</option>');
    var $dict1 = Basic._categories;
    for (var $key2 in $dict1) {
        var entry = { key: $key2, value: $dict1[$key2] };
        $(String.format('<option value="{0}">{0}</option>', entry.key)).appendTo(opjq);
    }
    Basic.initUploadPicture();
    Basic.lastVariableIndexId = 1;
    if (testBasic != null) {
        if (testBasic.description == null) {
            testBasic.description = 'Find out if you can survive when the zombies attack.';
        }
        var basic = testjq.find('.basic>ul>li');
        $(basic[0]).find('.question-main>p.question-question').html(testBasic.description);
        $(basic[1]).find('.question-main>select').val(testBasic.maturity);
        $(basic[2]).find('.question-main>p>select.test-category').val(testBasic.category).trigger('onchange');
        $(basic[2]).find('.question-main>p>select.test-subcategory').val(testBasic.subCategory);
        if (testBasic.thumnailUrl != null && !!testBasic.thumnailUrl) {
            $(basic[4]).find('.question-main>div.image-container>img').attr('src', testBasic.thumnailUrl).css('display', 'block');
        }
        $(basic[3]).find('.question-main>ul>li.variable').remove();
        var addBtn = $(basic[3]).find('.question-main>ul>li>a.add').get(0);
        if (testBasic.variables == null) {
            testBasic.variables = [];
        }
        if (!testBasic.variables.length) {
            testBasic.variables.add('Pure');
        }
        for (var i = 0; i < testBasic.variables.length; i++) {
            Basic.addVariable(addBtn, testBasic.variables[i]);
        }
        Basic.lastVariableIndexId = testBasic.variables.length + 1;
    }
}
Basic.updateTestRating = function Basic$updateTestRating(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var index = ele.selectedIndex;
    if (index < 0 || index > 3) {
        index = 0;
    }
    $(ele).parent().children('span').each(function(i, child) {
        child.style.display = (i === index) ? 'inline' : 'none';
    });
}
Basic.updateTestCategory = function Basic$updateTestCategory(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var value = '';
    if (ele.selectedIndex >= 0) {
        value = (ele.options[ele.selectedIndex]).value;
    }
    var jq = $('.question-main').has(ele).find('.test-subcategory');
    jq.html('<option value="">- Choose -</option>');
    var subitems = Basic._categories[value];
    if (subitems != null && subitems.length > 0) {
        for (var i = 0; i < subitems.length; i++) {
            $(String.format('<option value="{0}">{0}</option>', subitems[i])).appendTo(jq);
        }
        $(ele).nextAll().css('display', 'inline');
    }
    else {
        $(ele).nextAll().css('display', 'none');
    }
}
Basic.updateTestSubcategory = function Basic$updateTestSubcategory(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
}
Basic.initUploadPicture = function Basic$initUploadPicture() {
    var jq = $('.basic li.question').has('.fileupload');
    var option = { element: jq.find('.question-main .fileupload').get(0), action: '/img/uploadtest', allowedExtensions: [ 'jpg', 'jpeg', 'png', 'gif' ], sizeLimit: 5000000, onComplete: function(id, fileName, responseJSON) {
        if (!ss.isNullOrUndefined(responseJSON['error'])) {
            alert(responseJSON['error']);
        }
        else if (!ss.isNullOrUndefined(responseJSON['success'])) {
            jq.find('img').css('display', 'block').prop('src', responseJSON['path']);
        }
        else {
            alert('Failed to upload file by unknown error');
        }
        jq.find('.qq-upload-list').css('display', 'none');
        jq.find('.imgname').html(jq.find('.qq-upload-list li .qq-upload-file').html() + '<br />');
    }, onSubmit: function(id, fileName, responseJSON) {
        jq.find('.qq-upload-list li').remove();
        jq.find('.qq-upload-list').css('display', 'block');
    } };
    var uploader = new qq.FileUploader(option);
}
Basic._nextVariableName = function Basic$_nextVariableName() {
    /// <returns type="String"></returns>
    var varname = 'Variable_' + Basic.lastVariableIndexId.toString();
    Basic.lastVariableIndexId++;
    return varname;
}
Basic.removeVariable = function Basic$removeVariable(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var jq = $('.variable').has(ele);
    var index = jq.index();
    var varname = jq.find('input').attr('varname');
    Score.basic_RemoveVariable(varname);
    if (jq.parent().children().length > 2) {
        jq.remove();
    }
}
Basic.addVariable = function Basic$addVariable(ele, variable) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="variable" type="String">
    /// </param>
    if ($('.variable-listing').has(ele).children().length < 11) {
        var varname = variable;
        if (varname == null) {
            varname = Basic._nextVariableName();
        }
        $(Basic._variableTemplate).insertBefore(ele.parentNode).find('input:text').val(varname).attr('varname', varname);
        Score.basic_AddVariable(varname);
    }
}
Basic.renameVariable = function Basic$renameVariable(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var jq = $('.variable').has(ele);
    var varname = ele.getAttribute('varname');
    var currentname = ele.value.trim();
    if (varname === currentname) {
        ele.value = varname;
        return;
    }
    if (!currentname) {
        alert("Your variables can't have an empty name! Please try again");
        ele.value = varname;
        return;
    }
    if (TestEditor._textCoreReg.test(currentname)) {
        alert('Your variables should have just letters, numbers, and undercores in their name.');
        ele.value = varname;
        return;
    }
    var exist = false;
    $('.variable-listing').has(ele).find('li.variable input:text').each(function(index, inputEle) {
        if (inputEle !== ele && (inputEle).value === currentname) {
            exist = true;
        }
    });
    if (exist) {
        alert('You already have a variable named ' + currentname + '. Please choose another name!');
        ele.focus();
        ele.value = varname;
        return;
    }
    $('.testpage').has(ele).find('.questions .answers .answer-pure ul li label').each(function(index, pureELe) {
        if (pureELe.innerHTML.replaceAll('&nbsp;', '').trim() === varname) {
            pureELe.innerHTML = currentname;
        }
    });
    ele.setAttribute('varname', currentname);
    Score.basic_ChangeVarname(varname, currentname);
    var qindex = jq.index();
}
Basic.movePosition = function Basic$movePosition() {
}
Basic.getVariables = function Basic$getVariables(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <returns type="Object"></returns>
    var result = {};
    var jqvars = $('.testpage').has(ele).find('.basic .variable-listing li.variable input:text').each(function(index, inputEle) {
        var key = (inputEle).value.trim();
        if (!ss.isNullOrUndefined(result[key])) {
            result[key] = 0;
        }
        else {
            result[key] = 0;
        }
    });
    return result;
}


////////////////////////////////////////////////////////////////////////////////
// Score

window.Score = function Score() {
    /// <field name="_initHtml" type="String" static="true">
    /// </field>
    /// <field name="_resultTypeTemplate" type="String" static="true">
    /// </field>
    /// <field name="_requirementContainerTemplate" type="String" static="true">
    /// </field>
    /// <field name="_requirementDefaultTemplate" type="String" static="true">
    /// </field>
    /// <field name="_requirementTemplate" type="String" static="true">
    /// </field>
}
Score._buildScore = function Score$_buildScore(testpage, score) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    /// <param name="score" type="TestScore">
    /// </param>
    var testjq = $(testpage);
    var scjq = testjq.find('.score');
    scjq.html(Score._initHtml);
    var lsjq = null;
    var addbtn = testjq.find(".score a[onclick='Score.addResultType(this);']").get(0);
    if (score == null || score.scoreItems == null || !score.scoreItems.length) {
        Score.addResultType(addbtn, null);
    }
    else {
        for (var i = score.scoreItems.length - 1; i >= 0; i--) {
            Score.addResultType(addbtn, score.scoreItems[i]);
        }
    }
    if (score != null && score.scaling === 'raw') {
        testjq.find('.score .scalingtype>div>p>a').first().click();
    }
    Score.initiVarRange();
}
Score.useScore = function Score$useScore(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var jq = $(ele).parent().find('a');
    if (jq.first().get(0) === ele) {
        jq.first().addClass('disactivebtn').removeClass('activebtn');
        jq.last().addClass('activebtn').removeClass('disactivebtn');
    }
    else {
        jq.first().addClass('activebtn').removeClass('disactivebtn');
        jq.last().addClass('disactivebtn').removeClass('activebtn');
    }
    $("select[onchange='Score.reqConditionChange(this);']").each(function(index, selEle) {
        Score.reqConditionChange(selEle);
    });
}
Score.addRequirement = function Score$addRequirement(ele, req) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="req" type="Requirement">
    /// </param>
    var uljq = $('.level1>div>ul').has(ele);
    uljq.find('li').first().css('display', 'none');
    uljq.find('li').last().css('display', 'block');
    var rejq = $(Score._requirementTemplate).insertBefore(uljq.find('li').last());
    Score._initiReqVarOption(rejq.get(0), req);
}
Score.removeRequirement = function Score$removeRequirement(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    $(ele).parent().remove();
}
Score.enterEditInput = function Score$enterEditInput(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    $(ele).removeClass('viewmode').addClass('editmode');
    $(ele).parent().parent().find('.caution').parent().css('display', 'block');
}
Score.leaveEditInput = function Score$leaveEditInput(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    $(ele).parent().css('display', 'none');
    $(ele).parent().parent().find('input').removeClass('editmode').addClass('viewmode');
}
Score.enterEditText = function Score$enterEditText(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    $(ele).removeClass('viewmode').addClass('editmode');
    $(ele).parent().parent().find('.caution').parent().css('display', 'block');
}
Score.leaveEditText = function Score$leaveEditText(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    $(ele).parent().css('display', 'none');
    $(ele).parent().parent().find('.editmode').removeClass('editmode').addClass('viewmode');
}
Score.deleteResultType = function Score$deleteResultType(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    if ($('.scorelist > li').length > 1) {
        $('.scorelist > li').has(ele).remove();
    }
    $('.scorelist > li:last-child .requirements-container').html(Score._requirementDefaultTemplate);
}
Score.setDefaultValue = function Score$setDefaultValue(scoreItem) {
    /// <param name="scoreItem" type="ScoreItem">
    /// </param>
    if (String.isNullOrEmpty(scoreItem.description)) {
        scoreItem.description = "You scored $(Pure)% pure. Author: here you should write a description of this test result, telling the taker what his/her score means and why they got it. \r\n                        Note: in the description or title of a test result, you can refer to someone's score on a variable by wrapping the variable name in $(). \r\n                        For example, you could say here, Hey! You scored $(intelligence) on intelligence. Brilliant!";
    }
    if (String.isNullOrEmpty(scoreItem.title)) {
        scoreItem.description = 'Add title here';
    }
    if (String.isNullOrEmpty(scoreItem.subTitle)) {
        scoreItem.description = '$(Pure)% Pure!';
    }
}
Score.addResultType = function Score$addResultType(ele, scoreItem) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="scoreItem" type="ScoreItem">
    /// </param>
    var vars = Basic.getVariables(ele);
    var defaultVars = '';
    var defaultVarDes = '';
    var $dict1 = vars;
    for (var $key2 in $dict1) {
        var v = { key: $key2, value: $dict1[$key2] };
        defaultVars += ((!defaultVars) ? '' : ', ') + String.format('$({0})% {0}!', v.key);
        defaultVarDes += ((!defaultVarDes) ? '' : ', ') + String.format('$({0})% {0}', v.key);
    }
    var resultTypeHtml = Score._resultTypeTemplate.replaceAll('$(Pure)% Pure!', defaultVars).replaceAll('$(Pure)% pure', defaultVarDes);
    var jq;
    if (!$('.score .scorelist > li').length) {
        jq = $(resultTypeHtml).appendTo('.score .scorelist');
        jq.find('.requirements-container').html(Score._requirementDefaultTemplate);
    }
    else {
        jq = $(resultTypeHtml).insertBefore('.score .scorelist > li:first-child');
        jq.find('.requirements-container').html(Score._requirementContainerTemplate);
    }
    jq.fadeTo(0, 0.4, function() {
        jq.fadeTo('slow', 1);
    });
    Score._initUploadImage(jq.get(0));
    if (scoreItem != null) {
        Score.setDefaultValue(scoreItem);
        jq.find('input.reqtitle').val(scoreItem.title);
        jq.find('input.reqsubtitle').val(scoreItem.subTitle);
        jq.find('p.question-question').html(scoreItem.description);
        if (scoreItem.imageUrl != null && !!scoreItem.imageUrl) {
            jq.find('.image-container>img').attr('src', scoreItem.imageUrl).css('display', 'block');
        }
        if (scoreItem.requirements != null && scoreItem.requirements.length > 0) {
            var btnAdd = jq.find(".requirements a[onclick='Score.addRequirement(this)']").get(0);
            if (btnAdd != null) {
                for (var i = 0; i < scoreItem.requirements.length; i++) {
                    Score.addRequirement(btnAdd, scoreItem.requirements[i]);
                }
            }
        }
    }
    else {
    }
}
Score.checkVarRange = function Score$checkVarRange() {
    /// <returns type="Object"></returns>
    var pureMinMax = {};
    $('#testpage .question .answer-pure>ul').each(function(index, ele) {
        var pure = TestEditor.readPure(ele);
        var $dict1 = pure;
        for (var $key2 in $dict1) {
            var item = { key: $key2, value: $dict1[$key2] };
            if (ss.isNullOrUndefined(pureMinMax[item.key])) {
                pureMinMax[item.key] = [ item.value, item.value ];
            }
            if (item.value < pureMinMax[item.key][0]) {
                pureMinMax[item.key][0] = item.value;
            }
            if (item.value > pureMinMax[item.key][1]) {
                pureMinMax[item.key][1] = item.value;
            }
        }
    });
    return pureMinMax;
}
Score.initiVarRange = function Score$initiVarRange() {
    var pureMinMax = Score.checkVarRange();
    var template = '<li class="varitem"><strong>{0}</strong> scores range <strong>{1}</strong> to <strong>{2}</strong>.</li>';
    var jq = $('#testpage .score ul.varlist');
    jq.find('li').remove();
    var $dict1 = pureMinMax;
    for (var $key2 in $dict1) {
        var item = { key: $key2, value: $dict1[$key2] };
        $(String.format(template, item.key, item.value[0], item.value[1])).appendTo(jq);
    }
}
Score._initiReqVarOption = function Score$_initiReqVarOption(ele, req) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="req" type="Requirement">
    /// </param>
    var vars = Basic.getVariables(ele);
    var html = '';
    var $dict1 = vars;
    for (var $key2 in $dict1) {
        var v = { key: $key2, value: $dict1[$key2] };
        html += String.format('<option value={0}>{0}</option>', v.key);
    }
    $(ele).find('select.varoptions').html(html).each(function(index, selEle) {
        if (!index) {
            $(selEle).val((req != null) ? req.variable : null);
        }
        if (index === 1) {
            $(selEle).val((req != null) ? req.objVariable : null);
        }
    });
    $(ele).find('input.reqvalue').val((req != null) ? req.value : null);
    var conjq = $(ele).find("select[onchange='Score.reqConditionChange(this);']");
    conjq.val((req != null) ? req.condition : null);
    Score.reqConditionChange(conjq.get(0));
}
Score._initUploadImage = function Score$_initUploadImage(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var jq = $(ele);
    var option = { element: jq.find('.fileupload').get(0), action: '/img/uploadtest', allowedExtensions: [ 'jpg', 'jpeg', 'png', 'gif' ], sizeLimit: 5000000, onComplete: function(id, fileName, responseJSON) {
        if (!ss.isNullOrUndefined(responseJSON['error'])) {
            alert(responseJSON['error']);
        }
        else if (!ss.isNullOrUndefined(responseJSON['success'])) {
            jq.find('img').css('display', 'block').prop('src', responseJSON['path']);
        }
        else {
            alert('Failed to upload file by unknown error');
        }
        jq.find('.qq-upload-list').css('display', 'none');
        jq.find('.imgname').html(jq.find('.qq-upload-list li .qq-upload-file').html() + '<br />');
    }, onSubmit: function(id, fileName, responseJSON) {
        jq.find('.qq-upload-list li').remove();
        jq.find('.qq-upload-list').css('display', 'block');
    } };
    var uploader = new qq.FileUploader(option);
}
Score.reqConditionChange = function Score$reqConditionChange(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var jq = $(ele).parent();
    var sel = ele.options[ele.selectedIndex];
    switch (sel.value) {
        case 'GreaterThanValue':
        case 'LessThanValue':
            jq.find('.reqvarobj').css('display', 'none');
            jq.find('.reqvalue').css('display', 'inline');
            break;
        case 'GreaterThanVar':
        case 'LessThanVar':
            jq.find('.reqvarobj').css('display', 'inline');
            jq.find('.reqvalue').css('display', 'none');
            break;
        case 'GreatestVar':
        case 'LeastVar':
            jq.find('.reqvarobj').css('display', 'none');
            jq.find('.reqvalue').css('display', 'none');
            break;
    }
    if ((sel.value !== 'GreaterThanValue' && sel.value !== 'LessThanValue') || Score.getScallingType() === 'raw') {
        jq.find('.requnit').css('display', 'none');
    }
    else {
        jq.find('.requnit').css('display', 'inline');
    }
}
Score.getScallingType = function Score$getScallingType() {
    /// <returns type="String"></returns>
    var jq = $('div.scalingtype p a.activebtn');
    if (jq.parent().find('a').first().get(0) === jq.get(0)) {
        return 'Percentage';
    }
    else {
        return 'Raw';
    }
}
Score.onActive = function Score$onActive() {
    Score.initiVarRange();
}
Score.changeVariableName = function Score$changeVariableName(oldname, varname) {
    /// <param name="oldname" type="String">
    /// </param>
    /// <param name="varname" type="String">
    /// </param>
    $('#testpage .score .scorelist input.reqtitle,#testpage .score .scorelist input.reqsubtitle,#testpage .score .scorelist p.question-question').each(function(index, ele) {
        if (ele.tagName.toLowerCase() === 'input') {
            var value = (ele).value;
            (ele).value = value.replaceAll('$(' + oldname + ')', '$(' + varname + ')');
        }
        if (ele.tagName.toLowerCase() === 'p') {
            var value = ele.innerHTML;
            ele.innerHTML = value.replaceAll('$(' + oldname + ')', '$(' + varname + ')');
        }
    });
}
Score.basic_ChangeVarname = function Score$basic_ChangeVarname(oldname, varname) {
    /// <param name="oldname" type="String">
    /// </param>
    /// <param name="varname" type="String">
    /// </param>
    $("#testpage .score .scorelist .requirements .varoptions>option[value='" + oldname + "']").text(varname).attr('value', varname);
    Score.changeVariableName(oldname, varname);
}
Score.basic_AddVariable = function Score$basic_AddVariable(varname) {
    /// <param name="varname" type="String">
    /// </param>
    $('#testpage .score .scorelist .requirements .varoptions').append(String.format('<option value={0}>{0}</option>', varname));
}
Score.basic_RemoveVariable = function Score$basic_RemoveVariable(varname) {
    /// <param name="varname" type="String">
    /// </param>
    var opjq = $("#testpage .score .scorelist .requirements .varoptions>option[value='" + varname + "']");
    if (opjq.parent().val() === varname) {
        opjq.parent().val(null);
    }
    opjq.remove();
    Score.changeVariableName(varname, '????');
}


////////////////////////////////////////////////////////////////////////////////
// TestEditor

window.TestEditor = function TestEditor() {
    /// <field name="_testTitleTemplate" type="String" static="true">
    /// </field>
    /// <field name="_testTitleTemplateTest" type="String" static="true">
    /// </field>
    /// <field name="_menuTemplate" type="String" static="true">
    /// </field>
    /// <field name="_resultTemplateTest" type="String" static="true">
    /// </field>
    /// <field name="_questionTemplate" type="String" static="true">
    /// </field>
    /// <field name="_questionTemplateTest" type="String" static="true">
    /// </field>
    /// <field name="_textTemplate" type="String" static="true">
    /// </field>
    /// <field name="_textTemplateTest" type="String" static="true">
    /// </field>
    /// <field name="_breakTemplate" type="String" static="true">
    /// </field>
    /// <field name="_breakTemplateTest" type="String" static="true">
    /// </field>
    /// <field name="_pictureTemplate" type="String" static="true">
    /// </field>
    /// <field name="_pictureTemplateTest" type="String" static="true">
    /// </field>
    /// <field name="_answerTemplate" type="String" static="true">
    /// </field>
    /// <field name="_answerTemplateTest" type="String" static="true">
    /// </field>
    /// <field name="_pureItemTemplate" type="String" static="true">
    /// </field>
    /// <field name="_pureEditorTemplate" type="String" static="true">
    /// </field>
    /// <field name="_controls" type="String" static="true">
    /// </field>
    /// <field name="_ansDefaulReg" type="RegExp" static="true">
    /// </field>
    /// <field name="_textCoreReg" type="RegExp" static="true">
    /// </field>
}
TestEditor.showTab = function TestEditor$showTab(ele, tabclass) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="tabclass" type="String">
    /// </param>
    $('#testeditortoolbar .nav > li').removeClass('active').has(ele).addClass('active');
    $('#testpage > div a.button-done').each(function(index, aEle) {
        $(aEle).closest('.question-question-editor,.answer-editor').each(function(i, parEle) {
            if (parEle.style.display === 'block') {
                aEle.click();
            }
        });
    });
    $('#testpage > div').not('.testheader').not('.testfooter').hide().filter('.' + tabclass).slideDown();
    if (tabclass === 'score') {
        Score.initiVarRange();
    }
}
TestEditor.navSaveDraft = function TestEditor$navSaveDraft(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    TestEditor.updateTest($('#testpage > div').get(0), false);
}
TestEditor.navComplete = function TestEditor$navComplete(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    TestEditor.updateTest($('#testpage > div').get(0), true);
}
TestEditor.navRemove = function TestEditor$navRemove(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    if (!confirm('Do you want to remove the test?')) {
        return;
    }
    var id = $("#testpage h2>input[name='testid']").val();
    var url = '/quizz/remove/' + id;
    window.location = url;
}
TestEditor.load = function TestEditor$load(testpage, testid) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    /// <param name="testid" type="String">
    /// </param>
    $.getJSON(String.format('/quizztest/load/{0}', testid), function(obj) {
        try {
            var test = obj;
            TestEditor.buildTestEditor(testpage, test);
        }
        catch (ex) {
            TestEditor._error('Could not complete operation. Please try again later');
        }
    }).error(function(request, textStatus, error) {
        TestEditor._error('Could not complete operation. Please try again later');
    }).success(function(data, textStatus, request) {
    });
}
TestEditor._error = function TestEditor$_error(error) {
    /// <param name="error" type="String">
    /// </param>
    alert(error);
}
TestEditor.createDefaultTest = function TestEditor$createDefaultTest(testpage) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    var testdata = new Test();
    testdata.title = 'Type test title here';
    testdata.items = [];
    var item1 = new QuestionItem(ItemTypeDef.html);
    item1.html = "Hi! And welcome to my test. I'll be using advanced logic and magic to determine your true nature. I'll ask you some questions about something, then I'll analyze you. \r\n                        Author:<font color=\"#ff0000\"> <strong>This is your intro page. You should click here to edit the text, and you should write something witty, introducing your test. \r\n                        Also, see the fruity picture above? You should remove it and replace it with a fun picture relating to your test.</strong></font>";
    testdata.items.add(item1);
    var item11 = new QuestionItem(ItemTypeDef.pageBreak);
    item11.buttonNext = 'Go';
    testdata.items.add(item11);
    TestEditor.buildTestEditor(testpage, testdata);
    $(testpage).find('.qmenu').last().find('ul>li>a').first().click();
    $(testpage).find('.qmenu').last().find('ul>li>a').first().click();
}
TestEditor.buildTestEditor = function TestEditor$buildTestEditor(testpage, testdata) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    /// <param name="testdata" type="Test">
    /// </param>
    if (testdata.title == null || !testdata.title) {
        testdata.title = 'Type test title here';
    }
    if (testdata.items == null || !testdata.items.length) {
        testdata.items = [];
        var item1 = new QuestionItem(ItemTypeDef.html);
        item1.html = "Hi! And welcome to my test. I'll be using advanced logic and magic to determine your true nature. I'll ask you some questions about something, then I'll analyze you. \r\n                        Author:<font color=\"#ff0000\"> <strong>This is your intro page. You should click here to edit the text, and you should write something witty, introducing your test. \r\n                        Also, see the fruity picture above? You should remove it and replace it with a fun picture relating to your test.</strong></font>";
        testdata.items.add(item1);
    }
    $(testpage).addClass('testpage');
    $(testpage).html('');
    var jq = $(String.format(TestEditor._testTitleTemplate, testdata.title)).appendTo(testpage);
    jq.find("input[name='testid']").val((testdata != null) ? testdata.testId : '');
    jq.find("input[name='status']").val((testdata != null) ? testdata.status : 'editing');
    var quejq = $(testpage).find('.questions > ul');
    TestEditor.buildTestQuestionEditor(quejq.get(0), testdata);
    Advice._buildAdvice(testpage);
    Basic._buildBasic(testpage, testdata.testBasic);
    Score._buildScore(testpage, testdata.testScore);
    if (testdata.status === 'completed' && false) {
        TestEditor._disableNavBar(false);
        TestEditor.makeEditorReadOnly();
        $("#testeditortoolbar a:contains('Save Draft'),#testeditortoolbar a:contains('Complete'),#testeditortoolbar a:contains('Remove')").css('display', 'none');
    }
    else if (testdata.status === 'completed') {
        $("#testeditortoolbar a:contains('Save Draft')").css('display', 'none');
    }
    if (testdata.items.length === 1) {
        $(testpage).find('.qmenu').first().find('ul>li>a').first().click();
    }
    TestEditor.showPreview(testdata.testId);
}
TestEditor.showPreview = function TestEditor$showPreview(testid) {
    /// <param name="testid" type="String">
    /// </param>
    var previewJq = $("#testeditortoolbar a:contains('Preview')");
    if (testid != null && !!testid && testid !== '0') {
        previewJq.attr('href', '/quizz/preview/' + testid);
        previewJq.attr('onclick', '');
        previewJq.css('display', 'block');
        previewJq.attr('target', 'blank');
    }
    else {
        previewJq.attr('href', 'javascript:void(0);');
        previewJq.attr('onclick', 'return false;');
        previewJq.css('display', 'none');
        previewJq.attr('target', '');
    }
}
TestEditor.buildTestQuestionEditor = function TestEditor$buildTestQuestionEditor(testul, testdata) {
    /// <param name="testul" type="Object" domElement="true">
    /// </param>
    /// <param name="testdata" type="Test">
    /// </param>
    var $enum1 = ss.IEnumerator.getEnumerator(testdata.items);
    while ($enum1.moveNext()) {
        var item = $enum1.current;
        var jq = null;
        if (item.itemType === ItemTypeDef.multiChoice) {
            jq = $(TestEditor._questionTemplate).appendTo(testul);
            jq.find('.question-question').html(item.question);
            var $enum2 = ss.IEnumerator.getEnumerator(item.answers);
            while ($enum2.moveNext()) {
                var ansItem = $enum2.current;
                TestEditor._buildAnswerMultiChoiceEditor(jq.find('ul.answers').get(0), ansItem, testdata.testId, item.questionId, ansItem.answerId);
            }
        }
        if (item.itemType === ItemTypeDef.html) {
            jq = $(TestEditor._textTemplate).appendTo(testul);
            jq.find('.question-question').html(item.html);
        }
        if (item.itemType === ItemTypeDef.pageBreak) {
            jq = $(TestEditor._breakTemplate).appendTo(testul);
            jq.find('.question-main input').val(item.buttonNext);
        }
        if (item.itemType === ItemTypeDef.photo) {
            jq = $(TestEditor._pictureTemplate).appendTo(testul);
            jq.find('.image-container>img').prop('src', item.imagePath);
        }
        if (jq != null) {
            $(TestEditor._menuTemplate).appendTo(jq.find('.qmenu ul'));
        }
    }
}
TestEditor._buildAnswerMultiChoiceEditor = function TestEditor$_buildAnswerMultiChoiceEditor(ansul, ansItem, testid, questionid, answerid) {
    /// <param name="ansul" type="Object" domElement="true">
    /// </param>
    /// <param name="ansItem" type="AnswerItem">
    /// </param>
    /// <param name="testid" type="String">
    /// </param>
    /// <param name="questionid" type="String">
    /// </param>
    /// <param name="answerid" type="String">
    /// </param>
    var jq = $(ansul).find('.add-answer').parent();
    var ansjq = null;
    if (jq.length > 0) {
        ansjq = $(String.format(TestEditor._answerTemplate, ansItem.answer)).insertBefore(jq);
    }
    else {
        ansjq = $(String.format(TestEditor._answerTemplate, ansItem.answer)).appendTo(ansul);
    }
    var pureul = ansjq.find('.answer-html .answer-pure ul').get(0);
    TestEditor._buildHtmlPure(pureul, ansItem.pures);
}
TestEditor.editAnswer = function TestEditor$editAnswer(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var answer = $('ul.answers li').has(ele);
    answer.find('.answer-html').css('display', 'none');
    answer.find('.answer-editor').css('display', 'block');
    var jq = answer.find('textarea');
    var option = { width: 390, height: 120, controls: TestEditor._controls };
    var editor = jq.cleditor(option);
    var html = answer.find('.answer-answer').html().trim();
    jq.val(html).blur();
    var pures = TestEditor.readPure(answer.find('.answer-html .answer-pure ul').get(0));
    TestEditor._buildEditorPure(answer.find('.answer-editor .answer-pure ul').get(0), pures);
}
TestEditor.endEditAnswer = function TestEditor$endEditAnswer(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var answer = $('ul.answers li').has(ele);
    answer.find('.answer-html').css('display', 'inline');
    answer.find('.answer-editor').css('display', 'none');
    var jq = answer.find('textarea');
    answer.find('.answer-answer').html(jq.val());
    var pures = TestEditor.readPure(answer.find('.answer-editor .answer-pure ul').get(0));
    TestEditor._buildHtmlPure(answer.find('.answer-html .answer-pure ul').get(0), pures);
    TestEditor.normalizeDivAndParagraph(answer.find('.answer-answer'));
}
TestEditor.removeAnswer = function TestEditor$removeAnswer(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    $('ul.answers li').has(ele).remove();
}
TestEditor.addAnswer = function TestEditor$addAnswer(ele, pures, defaultText) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="pures" type="Object">
    /// </param>
    /// <param name="defaultText" type="String">
    /// </param>
    if (ele == null) {
        return;
    }
    ele = ele.parentNode;
    var ul = ele.parentNode;
    if (pures == null) {
        pures = {};
    }
    if (ss.isNullOrUndefined(pures['Pure'])) {
        pures['Pure'] = 0;
    }
    var html = String.format(TestEditor._answerTemplate, String.format('Answer {0} - click to change', ul.children.length));
    var pureul = $(html).insertBefore(ele).find('.answer-html .answer-pure ul').get(0);
    TestEditor._buildHtmlPure(pureul, pures);
}
TestEditor.readPure = function TestEditor$readPure(ul) {
    /// <param name="ul" type="Object" domElement="true">
    /// </param>
    /// <returns type="Object"></returns>
    var result = Basic.getVariables(ul);
    if (ul == null) {
        return result;
    }
    $(ul).children('li').each(function(index, el) {
        var key = $(el).children('label').text().trim();
        var value = $(el).find('option:selected').text().trim();
        if (value == null || !value) {
            value = $(el).children('span').text().trim();
        }
        var intvalue = 0;
        try {
            intvalue = parseInt(value);
        }
        catch ($e1) {
        }
        if (!ss.isNullOrUndefined(result[key]) && !!key) {
            result[key] = intvalue;
        }
    });
    return result;
}
TestEditor._buildHtmlPure = function TestEditor$_buildHtmlPure(ul, values) {
    /// <param name="ul" type="Object" domElement="true">
    /// </param>
    /// <param name="values" type="Object">
    /// </param>
    if (ul == null) {
        return;
    }
    var html = '';
    var $dict1 = values;
    for (var $key2 in $dict1) {
        var entry = { key: $key2, value: $dict1[$key2] };
        if (!!entry.value && entry.value !== '0' && !!entry.value) {
            html += String.format(TestEditor._pureItemTemplate, entry.key, TestEditor._formatNumber(entry.value));
        }
    }
    if (!html) {
        html += String.format(TestEditor._pureItemTemplate, '', 'no effect');
    }
    ul.innerHTML = html;
}
TestEditor._buildEditorPure = function TestEditor$_buildEditorPure(ul, values) {
    /// <param name="ul" type="Object" domElement="true">
    /// </param>
    /// <param name="values" type="Object">
    /// </param>
    if (ul == null) {
        return;
    }
    var html = '';
    var $dict1 = values;
    for (var $key2 in $dict1) {
        var entry = { key: $key2, value: $dict1[$key2] };
        html += String.format(TestEditor._pureEditorTemplate, entry.key, TestEditor._buildOption(TestEditor._formatNumber(entry.value)));
    }
    ul.innerHTML = html;
}
TestEditor._formatNumber = function TestEditor$_formatNumber(number) {
    /// <param name="number" type="Number" integer="true">
    /// </param>
    /// <returns type="String"></returns>
    return (number > 0) ? '+' + number.toString() : number.toString();
}
TestEditor._buildOption = function TestEditor$_buildOption(selected) {
    /// <param name="selected" type="String">
    /// </param>
    /// <returns type="String"></returns>
    var values = [ '+5', '+4', '+3', '+2', '+1', '0', '-1', '-2', '-3', '-4', '-5' ];
    var optionTemplate = '<option {1}>{0}</option>';
    var result = '';
    var $enum1 = ss.IEnumerator.getEnumerator(values);
    while ($enum1.moveNext()) {
        var item = $enum1.current;
        result += String.format(optionTemplate, item, (selected === item) ? "selected='selected'" : '');
    }
    return result;
}
TestEditor._showTestMessage = function TestEditor$_showTestMessage(ele, msg, autoHideIn) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="msg" type="String">
    /// </param>
    /// <param name="autoHideIn" type="Number" integer="true">
    /// </param>
    var jqtestpage = null;
    if ($(ele).hasClass('testpage')) {
        jqtestpage = $(ele);
    }
    else {
        jqtestpage = $('.testpage').has(ele);
    }
    var jqheader = jqtestpage.find('.testheader');
    if (!jqheader.prev().length) {
        $("<div style='display:none; background-color: #fcefd6; padding-top: 5px; padding-bottom: 15px; color: #666; '></div>").insertBefore(jqheader).html(msg).slideDown();
    }
    else {
        jqheader.prev().html('').hide().html(msg).slideDown();
    }
    if (autoHideIn > 0) {
        window.setTimeout(function() {
            jqheader.prev().slideUp();
        }, autoHideIn);
    }
}
TestEditor.retakeTest = function TestEditor$retakeTest(id) {
    /// <param name="id" type="Number" integer="true">
    /// </param>
}
TestEditor.rateTest = function TestEditor$rateTest(ele, rate, id) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="rate" type="Number" integer="true">
    /// </param>
    /// <param name="id" type="Number" integer="true">
    /// </param>
    $.getJSON('/quizztest/rate/' + id.toString() + '?rate=' + rate.toString()).success(function(obj) {
        var dic = obj;
        if (dic != null && dic['success'] != null) {
            $(ele).parent().children().removeClass('star-current');
            $(ele).addClass('star-current');
        }
    });
}
TestEditor.loadTest = function TestEditor$loadTest(testpage, testid, page) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    /// <param name="testid" type="String">
    /// </param>
    /// <param name="page" type="Number" integer="true">
    /// </param>
    $.getJSON(String.format('/quizztest/taketest/{0}?page={1}', testid, page), function(obj) {
        try {
            TestEditor.buildTest(testpage, obj, false);
        }
        catch (ex) {
            TestEditor._error('Could not complete operation. Please try again later');
        }
    }).error(function(request, textStatus, error) {
        TestEditor._error('Could not complete operation. Please try again later');
    }).success(function(data, textStatus, request) {
    });
}
TestEditor.buildTest = function TestEditor$buildTest(testpage, data, readonlytest) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    /// <param name="data" type="Object">
    /// </param>
    /// <param name="readonlytest" type="Boolean">
    /// </param>
    var testdata = null;
    var pures = null;
    var takendate = Date.get_today().format('MMM,dd yyy');
    var testresult = null;
    if (!ss.isNullOrUndefined(data['detail'])) {
        testresult = data;
        testdata = testresult.detail;
        pures = testresult.total;
        try {
            if (!ss.isNullOrUndefined(testresult.takenTime)) {
                takendate = testresult.takenTime;
            }
        }
        catch (ex) {
        }
    }
    else {
        testdata = data;
    }
    $(testpage).addClass('testpage');
    if (ss.isNull(testdata.page) || !testdata.page) {
        $(testpage).html('');
        var jq = $(String.format(TestEditor._testTitleTemplateTest, testdata.title)).appendTo(testpage);
        jq.parent().find("h2>input[name='testid']").val(testdata.testId);
    }
    var quejq = $(testpage).find('.questions > ul');
    if (testresult != null) {
    }
    if (testresult != null && testresult.scoreItemResults != null && testresult.scoreItemResults.length > 0) {
        TestEditor.buildTestResult(testpage, testresult);
    }
    if (!readonlytest && testresult == null && testdata.testBasic != null) {
        TestEditor.buildTestQuestion(quejq.get(0), testdata, readonlytest);
        TestEditor.buildTestDescription(testpage, testdata.testBasic);
    }
}
TestEditor.buildTestDescription = function TestEditor$buildTestDescription(testpage, testBasic) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    /// <param name="testBasic" type="TestBasic">
    /// </param>
    var quejq = $(testpage).find('.questions > ul');
    var rsjq = $(TestEditor._resultTemplateTest);
    rsjq.find('.question-left>div').html('DESCTIPTION');
    rsjq.find('.rstitle').html('');
    rsjq.find('.rssubtitle').html('').parent().css('display', 'none');
    rsjq.find('.rsdesc').html(testBasic.description);
    if (testBasic.thumnailUrl != null && !!testBasic.thumnailUrl) {
        rsjq.find('.image-container img').css('display', 'block').attr('src', testBasic.thumnailUrl);
    }
    if (quejq.children('li').length > 0) {
        rsjq = rsjq.insertBefore(quejq.children().first());
    }
    else {
        rsjq = rsjq.appendTo(quejq);
    }
    $('.testpage .questions ul li.testresult a[onclick]').text('TAKE TEST').css('display', 'inline').attr('onclick', 'TestEditor.startTest(this, false); return false;');
}
TestEditor.buildTestResult = function TestEditor$buildTestResult(testpage, testresult) {
    /// <param name="testpage" type="Object" domElement="true">
    /// </param>
    /// <param name="testresult" type="TestResult">
    /// </param>
    var quejq = $(testpage).find('.questions > ul');
    var score = testresult.scoreItemResults[0];
    var rsjq = $(TestEditor._resultTemplateTest);
    rsjq.find('.rstitle').html(score.title);
    rsjq.find('.rssubtitle').html(score.subTitle);
    rsjq.find('.rsdesc').html(score.description);
    if (score.imageUrl != null && !!score.imageUrl) {
        rsjq.find('.image-container img').css('display', 'block').attr('src', score.imageUrl);
    }
    if (quejq.children('li').length > 0) {
        rsjq = rsjq.insertBefore(quejq.children().first());
    }
    else {
        rsjq = rsjq.appendTo(quejq);
    }
    TestEditor.showTestResultDetail(null, false);
    var chartContainerHtml = "<div class='chartContainer' style='margin: 30px 0px 10px 0px;'>" + '</div>';
    var chartHtml = "<div class='chart' style='float: left; margin: 10px 20px 10px 20px; width: 210px; height: 135px;'>" + "<div class='chart-chart'></div>" + "<div class='chart-title'></div>" + '</div>';
    var footer = "<div class='clear'></div>";
    var container = $(chartContainerHtml).appendTo(testpage);
    var drawChart = function() {
        var $enum1 = ss.IEnumerator.getEnumerator(testresult.chartDatas);
        while ($enum1.moveNext()) {
            var chartData = $enum1.current;
            var chartjq = $(chartHtml).appendTo(container);
            var chart = new Quizz.QuizzChart();
            chart.init(chartData.samplePoints, chartData.score, chartData.minX, chartData.maxX, 210, 95);
            chart.draw(chartjq.find('.chart-chart').get(0));
            var title = '';
            if (chartData.scaling === 'Percentage') {
                title = String.format('You scored {0}% on {1}, higher than {2}% of your peers.', Math.round(chartData.percent * 100), chartData.scoreName, chart.getMyPercent());
            }
            else {
                title = String.format('You scored {0} on {1}, higher than {2}% of your peers.', chartData.score, chartData.scoreName, chart.getMyPercent());
            }
            chartjq.find('.chart-title').html(title);
        }
        $(footer).appendTo(container);
    };
    
        google.load('visualization', '1.0', { 'packages': ['corechart'] });    
        if (google.visualization) {
            drawChart();
        }
        else {
            google.setOnLoadCallback(drawChart);
        };
}
TestEditor.showTestResultDetail = function TestEditor$showTestResultDetail(ele, show) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="show" type="Boolean">
    /// </param>
    var jq = $('.testpage .questions>ul>li').not('.testresult');
    if (show) {
        jq.fadeIn();
        $('.testpage .questions ul li.testresult a[onclick]').text('Hide detail').attr('onclick', 'TestEditor.showTestResultDetail(this, false); return false;');
    }
    else {
        if (jq.css('display') !== 'none') {
            jq.fadeOut();
        }
        $('.testpage .questions ul li.testresult a[onclick]').text('Show detail').attr('onclick', 'TestEditor.showTestResultDetail(this, true); return false;');
    }
}
TestEditor.startTest = function TestEditor$startTest() {
    $('.testpage .questions>ul>li.testresult').css('display', 'none');
    TestEditor.nextPage($('.testpage .questions>ul>li.testresult').last().children().first().get(0));
}
TestEditor.buildTestQuestion = function TestEditor$buildTestQuestion(testul, testdata, readonlytest) {
    /// <param name="testul" type="Object" domElement="true">
    /// </param>
    /// <param name="testdata" type="Test">
    /// </param>
    /// <param name="readonlytest" type="Boolean">
    /// </param>
    readonlytest = (readonlytest || testdata.status === 'tested');
    var questionNo = 1;
    var pageBreakNo = 0;
    var $enum1 = ss.IEnumerator.getEnumerator(testdata.items);
    while ($enum1.moveNext()) {
        var item = $enum1.current;
        if (item.itemType === ItemTypeDef.multiChoice) {
            var jq = $(TestEditor._questionTemplateTest).appendTo(testul);
            jq.find('.question-question').html(item.question);
            var ansNo = 1;
            var $enum2 = ss.IEnumerator.getEnumerator(item.answers);
            while ($enum2.moveNext()) {
                var ansItem = $enum2.current;
                TestEditor._buildAnswerMultiChoice(jq.find('ul.answers').get(0), ansItem, testdata.testId, item.questionId, ansItem.answerId);
                ansNo++;
            }
            if (readonlytest) {
                jq.find('ul.answers').find("input[type='radio']").attr('disabled', 'disabled');
            }
            jq.find('.question-left').html(String.format("<div style='text-align:right;font-size:14px!important;color:#999;padding-right:20px;font-weight:bold'>{0}</div>", questionNo));
            questionNo++;
        }
        if (item.itemType === ItemTypeDef.html) {
            var jq = $(TestEditor._textTemplateTest).appendTo(testul);
            jq.find('.question-question').html(item.html);
        }
        if (item.itemType === ItemTypeDef.pageBreak) {
            var jq = $(TestEditor._breakTemplateTest).appendTo(testul);
            jq.find('a.button').text(item.buttonNext);
        }
        if (item.itemType === ItemTypeDef.photo) {
            var jq = $(TestEditor._pictureTemplateTest).appendTo(testul);
            jq.find('.image-container>img').prop('src', item.imagePath);
        }
    }
    if (!readonlytest) {
        var fjq = $(TestEditor._breakTemplateTest).appendTo(testul);
        fjq.find('p. >a').text('Finish').attr('onclick', 'TestEditor.finish(this); return false;');
        fjq.find('p. >a').prop('href', '#');
    }
}
TestEditor._buildAnswerMultiChoice = function TestEditor$_buildAnswerMultiChoice(ansul, ansItem, testid, questionid, answerid) {
    /// <param name="ansul" type="Object" domElement="true">
    /// </param>
    /// <param name="ansItem" type="AnswerItem">
    /// </param>
    /// <param name="testid" type="String">
    /// </param>
    /// <param name="questionid" type="String">
    /// </param>
    /// <param name="answerid" type="String">
    /// </param>
    var jq = $(String.format(TestEditor._answerTemplateTest, ansItem.answer)).appendTo(ansul);
    var ijq = jq.find("input[type='radio']").val(answerid);
    ijq.prop('name', testid + '_' + questionid);
    if (!!ansItem.selected) {
        ijq.attr('checked', 'checked');
        TestEditor._buildHtmlPure(jq.find('.answer-pure ul').get(0), ansItem.pures);
    }
}
TestEditor.finish = function TestEditor$finish(btnEle) {
    /// <param name="btnEle" type="Object" domElement="true">
    /// </param>
    if (!confirm('Are you sure finish the test?')) {
        return;
    }
    var answers = [];
    var testpage = $('.testpage').has(btnEle).get(0);
    $(testpage).find("div[class='questions'] ul li[class='question']").each(function(index, ele) {
        $(ele).find('ul.answers li').each(function(ansIndex, ansEle) {
            var jq = $(ansEle).find('.answer-dot input:checked');
            if (jq.length > 0) {
                answers.add(new UserAnswer(jq.attr('name'), jq.val()));
            }
        });
    });
    var testid = $(testpage).find("h2>input[name='testid']").val();
    $.post(String.format('/quizztest/submit/{0}?page={1}', testid, 0), answers, function(obj) {
        try {
            TestEditor.buildTest(testpage, $.parseJSON(obj), true);
            var msgobj = $(testpage).find('.testheader').prev();
            $("<h2 style='display:none; text-align:center; font-size:14px; '>Thanks for taking the test !</h2>").insertBefore(msgobj.children().first()).show();
        }
        catch (ex) {
            TestEditor._error('Could not complete operation. Please try again later');
        }
    }).error(function(request, textStatus, error) {
        alert('An error occurs, please try again later');
    }).success(function(data, textStatus, request) {
    });
}
TestEditor.nextPage = function TestEditor$nextPage(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    if (ele == null) {
        return;
    }
    var jq = $('.questions>ul>li').has(ele);
    var nextJq = jq.next();
    while (jq.length > 0) {
        jq.css('display', 'none');
        jq = jq.prev();
    }
    while (nextJq.length > 0) {
        nextJq.fadeIn();
        if (nextJq.hasClass('pagebreak')) {
            break;
        }
        nextJq = nextJq.next();
    }
}
TestEditor.loadNext = function TestEditor$loadNext(source) {
    /// <param name="source" type="Object" domElement="true">
    /// </param>
    var quejq = $('.questions > ul').has(source);
    var testid = quejq.prop('testid');
    var current = quejq.prop('currentpage');
    var total = quejq.prop('totalpage');
    if (ss.isNullOrUndefined(current)) {
        current = 0;
    }
    if (ss.isNullOrUndefined(total)) {
        total = 1;
    }
    if (current < total - 1) {
        TestEditor.loadTest(quejq.parent().parent().get(0), testid, current + 1);
    }
}
TestEditor.generateTest = function TestEditor$generateTest() {
    TestEditor.buildTest($('#test').get(0), TestEditor._readTest($('#testpage h2').get(0)), false);
}
TestEditor.upadateTestTitle = function TestEditor$upadateTestTitle(value) {
    /// <param name="value" type="String">
    /// </param>
}
TestEditor.actionClick = function TestEditor$actionClick(actEle) {
    /// <param name="actEle" type="Object" domElement="true">
    /// </param>
    var jq = $('.question').has(actEle);
    if (jq.hasClass('activemenu')) {
        $('.activemenu').removeClass('activemenu');
    }
    else {
        $('.activemenu').removeClass('activemenu');
        jq.addClass('activemenu');
    }
}
TestEditor.addMultiChoice = function TestEditor$addMultiChoice(ele, ul) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="ul" type="Object" domElement="true">
    /// </param>
    var jq = null;
    if (ele != null) {
        jq = $(TestEditor._questionTemplate).insertAfter($('.question').has(ele));
    }
    else if (ul != null) {
        jq = $(TestEditor._questionTemplate).appendTo(ul);
    }
    else {
        return;
    }
    $(TestEditor._menuTemplate).appendTo(jq.find('.qmenu ul'));
    jq.find('.add-answer').click();
    jq.find('.add-answer').click();
    $('.activemenu').removeClass('activemenu');
}
TestEditor.addTextOrHtml = function TestEditor$addTextOrHtml(ele, ul) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="ul" type="Object" domElement="true">
    /// </param>
    var jq = null;
    if (ele != null) {
        jq = $(TestEditor._textTemplate).insertAfter($('.question').has(ele));
    }
    else if (ul != null) {
        jq = $(TestEditor._textTemplate).appendTo(ul);
    }
    else {
        return;
    }
    $(TestEditor._menuTemplate).appendTo(jq.find('.qmenu ul'));
    $('.activemenu').removeClass('activemenu');
}
TestEditor.addPageBreak = function TestEditor$addPageBreak(ele, ul) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="ul" type="Object" domElement="true">
    /// </param>
    var jq = null;
    if (ele != null) {
        jq = $(TestEditor._breakTemplate).insertAfter($('.question').has(ele));
    }
    else if (ul != null) {
        jq = $(TestEditor._breakTemplate).appendTo(ul);
    }
    else {
        return;
    }
    $(TestEditor._menuTemplate).appendTo(jq.find('.qmenu ul'));
    $('.activemenu').removeClass('activemenu');
}
TestEditor.addPicture = function TestEditor$addPicture(ele, ul) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="ul" type="Object" domElement="true">
    /// </param>
    var jq = null;
    if (ele != null) {
        jq = $(TestEditor._pictureTemplate).insertAfter($('.question').has(ele));
    }
    else if (ul != null) {
        jq = $(TestEditor._pictureTemplate).appendTo(ul);
    }
    else {
        return;
    }
    $(TestEditor._menuTemplate).appendTo(jq.find('.qmenu ul'));
    var option = { element: jq.find('.question-main .fileupload').get(0), action: '/img/uploadtest', allowedExtensions: [ 'jpg', 'jpeg', 'png', 'gif' ], sizeLimit: 5000000, onComplete: function(id, fileName, responseJSON) {
        if (!ss.isNullOrUndefined(responseJSON['error'])) {
            alert(responseJSON['error']);
        }
        else if (!ss.isNullOrUndefined(responseJSON['success'])) {
            jq.find('img').css('display', 'block').prop('src', responseJSON['path']);
        }
        else {
            alert('Failed to upload file by unknown error');
        }
        jq.find('.qq-upload-list').css('display', 'none');
        jq.find('.imgname').html(jq.find('.qq-upload-list li .qq-upload-file').html() + '<br />');
    }, onSubmit: function(id, fileName, responseJSON) {
        jq.find('.qq-upload-list li').remove();
        jq.find('.qq-upload-list').css('display', 'block');
    } };
    var uploader = new qq.FileUploader(option);
    $('.activemenu').removeClass('activemenu');
}
TestEditor.deleteItem = function TestEditor$deleteItem(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var jq = $('.question').has(ele);
    if (jq.parent().find('.question').first().get(0) !== jq.get(0)) {
        jq.remove();
    }
    else {
        alert('Please dont delete introduction item');
    }
    $('.activemenu').removeClass('activemenu');
}
TestEditor.cancel = function TestEditor$cancel(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    $('.question-left').has(ele).find("a[class='button']").click();
    $('.activemenu').removeClass('activemenu');
}
TestEditor.imageLoaded = function TestEditor$imageLoaded(img) {
    /// <param name="img" type="Object" domElement="true">
    /// </param>
    img.style.display = 'block';
    var maxWidth = 500;
    var nw = img.naturalWidth;
    var nh = img.naturalHeight;
    if (nw > maxWidth) {
        img.width = maxWidth;
    }
}
TestEditor.editQuestion = function TestEditor$editQuestion(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var main = $('.question-main').has(ele);
    var jqeditorpanel = main.children('.question-question-editor');
    var jqhtml = main.children('.question-question');
    var jqeditor = jqeditorpanel.find('textarea');
    jqhtml.css('display', 'none');
    jqeditorpanel.css('display', 'block');
    var option = { width: 495, height: 140, controls: TestEditor._controls };
    var editor = jqeditor.cleditor(option);
    var html = jqhtml.html().trim();
    jqeditor.val(html).blur();
}
TestEditor.endEditQuestion = function TestEditor$endEditQuestion(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    var main = $('.question-main').has(ele);
    var jqeditorpanel = main.children('.question-question-editor');
    var jqhtml = main.children('.question-question');
    var jqeditor = jqeditorpanel.find('textarea');
    jqhtml.css('display', 'block');
    jqeditorpanel.css('display', 'none');
    jqhtml.html(jqeditor.val());
    TestEditor.normalizeDivAndParagraph(jqhtml);
}
TestEditor.normalizeDivAndParagraph = function TestEditor$normalizeDivAndParagraph(jq) {
    /// <param name="jq" type="jQueryObject">
    /// </param>
    return;
    var replace = jq.children().length > 0;
    jq.children().each(function(index, ele) {
        if (ele.nodeType !== 3) {
            var tagName = ele.tagName.toUpperCase();
            if (tagName !== 'P' && tagName !== 'DIV') {
                replace = false;
            }
        }
    });
    if (replace) {
        var newHtml = '';
        jq.children().each(function(index, ele) {
            var tagName = ele.tagName.toUpperCase();
            if (tagName === 'P' || tagName === 'DIV') {
                newHtml += $(ele).html() + '<br />';
            }
            else {
                newHtml += $(ele).html();
            }
        });
        jq.html(newHtml);
    }
}
TestEditor.booleanMethod = function TestEditor$booleanMethod(result) {
    /// <param name="result" type="Boolean">
    /// </param>
    /// <returns type="Boolean"></returns>
    return result;
}
TestEditor.makeEditorReadOnly = function TestEditor$makeEditorReadOnly() {
    $('#testpage *[onclick]').each(function(index, aEle) {
        var onclick = aEle.getAttribute('onclick');
        if (onclick != null && !onclick.startsWith('return TestEditor.booleanMethod(false);')) {
            aEle.setAttribute('onclick', 'return TestEditor.booleanMethod(false);' + onclick);
        }
    });
    $('#testpage select').attr('disabled', 'disabled');
    $('#testpage .add,#testpage .remove,#testpage .fileupload,#testpage .topdeletebtn,#testpage .answer-remove,#testpage .add-answer').css('display', 'none');
    $('#testpage .questions .question-left').css('visibility', 'hidden');
}
TestEditor._disableOnclick = function TestEditor$_disableOnclick(ele, disable) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="disable" type="Boolean">
    /// </param>
    var jq = $(ele).find('*[onclick]').andSelf();
    if (disable) {
        jq.each(function(index, aEle) {
            var onclick = aEle.getAttribute('onclick');
            if (onclick != null && !onclick.startsWith('return TestEditor.booleanMethod(false);')) {
                aEle.setAttribute('onclick', 'return TestEditor.booleanMethod(false);' + onclick);
            }
        });
    }
    else {
        jq.each(function(index, aEle) {
            var onclick = aEle.getAttribute('onclick');
            if (onclick != null) {
                onclick = onclick.replaceAll('return TestEditor.booleanMethod(false);', '');
            }
            aEle.setAttribute('onclick', onclick);
        });
    }
}
TestEditor._disableNavBar = function TestEditor$_disableNavBar(disable) {
    /// <param name="disable" type="Boolean">
    /// </param>
    var jq = $('#testeditortoolbar');
    if (disable) {
        jq.attr('disabled', 'disabled');
    }
    else {
        jq.removeAttr('disabled');
    }
    TestEditor._disableOnclick(jq.get(0), disable);
}
TestEditor.updateTest = function TestEditor$updateTest(ele, finish) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="finish" type="Boolean">
    /// </param>
    if (finish) {
        if (!confirm('Do you want to complete and publish the test')) {
            return;
        }
    }
    var page = $('.testpage').has(ele);
    if (page.find('.question-question-editor:visible').length + page.find('.answer-editor:visible').length > 0 && finish) {
        alert('Please complete all your editting questions before update');
        return;
    }
    var title = page.find("h2>input[name='test-title']").val();
    if (title.replace(TestEditor._textCoreReg, '').toLowerCase().startsWith('Type test title here'.replace(TestEditor._textCoreReg, '').toLowerCase()) && !TestEditor._isTestMode()) {
        alert('Please edit your test title');
        return;
    }
    var test = TestEditor._readTest(ele);
    if (finish) {
        test.status = 'completed';
    }
    else {
        test.status = 'editing';
    }
    TestEditor._disableNavBar(true);
    $.post('/quizztest/updatetest?finish=' + finish, test, function(obj) {
        TestEditor._disableNavBar(false);
    }).error(function(request, textStatus, error) {
        alert('An error occur, please try again later.\r\nError detail: \r\n' + textStatus);
        TestEditor._disableNavBar(false);
    }).success(function(data, textStatus, request) {
        var result = $.parseJSON(data);
        if (result.status === 'completed' && false) {
            var html = String.format("<h2 style='text-align:center; font-size:14px; '>Your test has been published!</h2>");
            TestEditor._showTestMessage(page.get(0), html, -1);
            page.find("input[name='testid']").val(result.testId);
            page.find("input[name='status']").val(result.status);
            TestEditor._disableNavBar(false);
            TestEditor.makeEditorReadOnly();
            $("#testeditortoolbar a:contains('Save Draft'),#testeditortoolbar a:contains('Complete'),#testeditortoolbar a:contains('Remove')").css('display', 'none');
        }
        if (result.status === 'completed') {
            var html = String.format("<h2 style='text-align:center; font-size:14px; '>Saved</h2>");
            TestEditor._showTestMessage(page.get(0), html, 4000);
            page.find("input[name='testid']").val(result.testId);
            page.find("input[name='status']").val(result.status);
            TestEditor.showPreview(result.testId);
            TestEditor._disableNavBar(false);
            $("#testeditortoolbar a:contains('Save Draft')").css('display', 'none');
        }
        else {
            var html = String.format("<h2 style='text-align:center; font-size:14px; '>Saved to draft</h2>");
            TestEditor._showTestMessage(page.get(0), html, 4000);
            page.find("input[name='testid']").val(result.testId);
            page.find("input[name='status']").val(result.status);
            TestEditor.showPreview(result.testId);
            TestEditor._disableNavBar(false);
        }
    });
}
TestEditor._isTestMode = function TestEditor$_isTestMode() {
    /// <returns type="Boolean"></returns>
    return document.URL.indexOf('127.0.0.1') > 0;
}
TestEditor._parseAndAddQuestionItem = function TestEditor$_parseAndAddQuestionItem(test, question) {
    /// <param name="test" type="Test">
    /// </param>
    /// <param name="question" type="QuestionItem">
    /// </param>
    /// <returns type="Boolean"></returns>
    var istest = TestEditor._isTestMode();
    if (test == null || test.items == null || question == null) {
        return false;
    }
    question.questionId = (test.items.length + 1).toString();
    if (question.itemType === ItemTypeDef.pageBreak) {
        if (test.items.length > 0) {
            var preItem = test.items[test.items.length - 1];
            if (preItem.itemType === ItemTypeDef.pageBreak) {
                return false;
            }
        }
        if (!test.items.length) {
            return false;
        }
    }
    if (question.itemType === ItemTypeDef.html) {
        if (question.html.replace(TestEditor._textCoreReg, '').toLowerCase().startsWith('Edit your text or HTML here'.replace(TestEditor._textCoreReg, '').toLowerCase())) {
            test.parseError.add({ reason: 'invalid html text: ' + question.html });
        }
    }
    if (question.itemType === ItemTypeDef.photo) {
        if (question.imagePath == null || !question.imagePath) {
            return false;
        }
    }
    if (question.itemType === ItemTypeDef.multiChoice && !istest) {
        if (question.question.replace(TestEditor._textCoreReg, '').toLowerCase().startsWith('Click here to edit your new question'.replace(TestEditor._textCoreReg, '').toLowerCase())) {
            test.parseError.add({ reason: 'invalid question text: ' + question.question });
        }
        for (var j = question.answers.length - 1; j >= 0; j--) {
            var ans = question.answers[j];
            if (ans.answer == null || !ans.answer || TestEditor._ansDefaulReg.test(ans.answer)) {
            }
            ans.answerId = (j + 1).toString();
        }
        if (!question.answers.length) {
            test.parseError.add({ reason: 'question must have answer: ' + question.question });
        }
    }
    if (question.itemType === ItemTypeDef.multiChoice && istest) {
        for (var j = question.answers.length - 1; j >= 0; j--) {
            var ans = question.answers[j];
            ans.answerId = (j + 1).toString();
        }
    }
    test.items.add(question);
    return true;
}
TestEditor._readTest = function TestEditor$_readTest(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <returns type="Test"></returns>
    var page = $('.testpage').has(ele);
    var test = new Test();
    test.title = page.find("h2>input[name='test-title']").val();
    test.testId = page.find("h2>input[name='testid']").val();
    test.status = page.find("h2>input[name='status']").val();
    if (test.testId == null || !test.testId) {
        test.testId = '';
    }
    var jqul = page.find('.questions');
    jqul.find('li.question').each(function(index, el) {
        var answers = $(el).find('.question-main ul.answers');
        if (answers.length > 0) {
            TestEditor._parseAndAddQuestionItem(test, TestEditor.readTest_ParseMultichoice(el));
            return;
        }
        var imgcontainer = $(el).find('.question-main .image-container');
        if (imgcontainer.length > 0) {
            TestEditor._parseAndAddQuestionItem(test, TestEditor.readTest_ParsePhoto(el));
            return;
        }
        var pagebreak = $(el).find('.question-main>p:first-child input');
        if (pagebreak.length > 0) {
            TestEditor._parseAndAddQuestionItem(test, TestEditor.readTest_PageBreak(el));
            return;
        }
        var text = $(el).find('.question-main>p:first-child').not('input');
        if (text.length > 0) {
            TestEditor._parseAndAddQuestionItem(test, TestEditor.readTest_ParseHtml(el));
            return;
        }
    });
    test.testBasic = TestEditor._readTestBasic(ele);
    test.testScore = TestEditor._readTestScore(ele);
    return test;
}
TestEditor._readTestBasic = function TestEditor$_readTestBasic(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <returns type="TestBasic"></returns>
    var page = $('.testpage').has(ele);
    var basic = page.find('.basic>ul>li');
    var testBasic = new TestBasic();
    testBasic.description = $(basic[0]).find('.question-main>p.question-question').html();
    testBasic.maturity = $(basic[1]).find('.question-main>select').val();
    testBasic.category = $(basic[2]).find('.question-main>p>select.test-category').val();
    testBasic.subCategory = $(basic[2]).find('.question-main>p>select.test-subcategory').val();
    testBasic.thumnailUrl = $(basic[4]).find('.question-main>div.image-container>img').attr('src');
    $(basic[3]).find('.question-main>ul>li.variable>input').each(function(index, varEle) {
        testBasic.variables.add((varEle).value);
    });
    return testBasic;
}
TestEditor._readTestScore = function TestEditor$_readTestScore(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <returns type="TestScore"></returns>
    var page = $('.testpage').has(ele);
    var testScore = new TestScore();
    testScore.scaling = Score.getScallingType();
    page.find('.score>ul.scorelist>li').each(function(index, scEle) {
        testScore.scoreItems.add(TestEditor._readScoreItem(scEle));
    });
    return testScore;
}
TestEditor._readScoreItem = function TestEditor$_readScoreItem(ele) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <returns type="ScoreItem"></returns>
    var item = new ScoreItem();
    $(ele).find('li').has('select.reqvar').each(function(index, reqEle) {
        var req = new Requirement();
        req.variable = $(reqEle).find('select.reqvar').val();
        req.condition = $(reqEle).find("select[onchange='Score.reqConditionChange(this);']").val();
        req.value = $(reqEle).find('input.reqvalue').val();
        req.objVariable = $(reqEle).find('select.reqvarobj').val();
        item.requirements.add(req);
    });
    item.title = $(ele).find('input.reqtitle').val();
    item.subTitle = $(ele).find('input.reqsubtitle').val();
    item.description = $(ele).find('p.question-question').html();
    item.imageUrl = $(ele).find('.image-container>img').attr('src');
    return item;
}
TestEditor.readTest_ParseMultichoice = function TestEditor$readTest_ParseMultichoice(el) {
    /// <param name="el" type="Object" domElement="true">
    /// </param>
    /// <returns type="QuestionItem"></returns>
    var question = new QuestionItem(ItemTypeDef.multiChoice);
    question.question = $(el).find('.question-main>p:first-child').html();
    question.answers = [];
    $(el).find('.question-main ul.answers>li:not(:last-child)').each(function(ansIndex, ansEle) {
        var ansItem = new AnswerItem();
        ansItem.pures = TestEditor.readPure($(ansEle).find('.answer-html .answer-pure ul').get(0));
        ansItem.answer = $(ansEle).find('.answer-html .answer-answer').html();
        question.answers.add(ansItem);
    });
    return question;
}
TestEditor.readTest_ParsePhoto = function TestEditor$readTest_ParsePhoto(el) {
    /// <param name="el" type="Object" domElement="true">
    /// </param>
    /// <returns type="QuestionItem"></returns>
    var question = new QuestionItem(ItemTypeDef.photo);
    question.imagePath = $(el).find('.question-main .image-container img').attr('src');
    if (question.imagePath == null || !question.imagePath) {
        return null;
    }
    return question;
}
TestEditor.readTest_PageBreak = function TestEditor$readTest_PageBreak(el) {
    /// <param name="el" type="Object" domElement="true">
    /// </param>
    /// <returns type="QuestionItem"></returns>
    var question = new QuestionItem(ItemTypeDef.pageBreak);
    question.buttonNext = $(el).find('.question-main>p:first-child input').val();
    return question;
}
TestEditor.readTest_ParseHtml = function TestEditor$readTest_ParseHtml(el) {
    /// <param name="el" type="Object" domElement="true">
    /// </param>
    /// <returns type="QuestionItem"></returns>
    var question = new QuestionItem(ItemTypeDef.html);
    question.html = $(el).find('.question-main>p:first-child').html();
    return question;
}
TestEditor.remove = function TestEditor$remove(ele, testId) {
    /// <param name="ele" type="Object" domElement="true">
    /// </param>
    /// <param name="testId" type="Number" integer="true">
    /// </param>
    var jq = $(ele).closest('tr');
    jq.find('p,div').fadeOut('fast', function() {
        jq.remove();
    });
    $.post('/quizz/createtest/remove.php?id=' + testId);
}
TestEditor.nothing = function TestEditor$nothing() {
    alert('Feature has not implemented yet');
    $.get('/taketest/list.php', function(obj) {
        $('.quizzlist').html(obj);
    });
    $.get('', function(obj) {
        var text = obj;
        if (text.length > 2) {
        }
        text = text.substr(1, text.length - 2);
        var data = eval(text);
    });
    $.getJSON('', function(obj) {
    });
    $('').blur(function(evt) {
        $.get('', function(obj) {
            var text = (obj).trim();
            if (text.length > 2) {
                text = text.substr(1, text.length - 2);
                var data = eval(text);
            }
        });
    });
}
TestEditor.test1 = function TestEditor$test1() {
    $('#email').blur(function(evt) {
        $(evt.target).parent().find('div:last-child').css('display', 'block').html().replaceAll('?', '');
    });
    var container = null;
    var url = $(container).find('img').attr('src');
    $('#imgviewer > img').attr('src', url);
    var img = null;
}
TestEditor.test2 = function TestEditor$test2() {
    var dir = 'next';
    var cururl = $('#imgviewer > img').attr('src');
    var nexturl = '';
    var backurl = '';
    var jq = $('#content_profile_images .profile_img img');
    var i = 0;
    if (cururl == null || !cururl) {
        nexturl = backurl = jq.attr('src');
    }
    else {
        for (i = 0; i < jq.length; i++) {
            if (jq[i].getAttribute('src') === cururl) {
                if (i > 0) {
                    backurl = jq[i - 1].getAttribute('src');
                }
                if (i < jq.length - 1) {
                    nexturl = jq[i + 1].getAttribute('src');
                }
                break;
            }
        }
        if (!nexturl && jq.length > 0) {
            nexturl = jq[0].getAttribute('src');
        }
        if (!backurl && jq.length > 0) {
            backurl = jq[jq.length - 1].getAttribute('src');
        }
    }
    if (i <= 0) {
        $('#imgviewer .imgback').css('display', 'none');
    }
    else {
        $('#imgviewer .imgback').css('display', 'block');
    }
    if (i >= jq.length - 1) {
        $('#imgviewer .imgnext').css('display', 'none');
    }
    else {
        $('#imgviewer .imgnext').css('display', 'block');
    }
    if (dir === 'next') {
        $('#imgviewer > img').attr('src', nexturl);
    }
    else {
        $('#imgviewer > img').attr('src', backurl);
    }
}
TestEditor.test3 = function TestEditor$test3() {
    var userid = '';
    var cururl = $('#imgviewer > img').attr('src');
    var jq = $('#content_profile_images .profile_img img');
    var imgid = '';
    var i = 0;
    for (i = 0; i < jq.length; i++) {
        if (jq[i].getAttribute('src') === cururl) {
            imgid = jq[i].getAttribute('id');
            break;
        }
    }
    var a = {};
    var $dict1 = a;
    for (var $key2 in $dict1) {
        var b = { key: $key2, value: $dict1[$key2] };
    }
    $('').slideUp();
    userid.substr(0, 500);
}


////////////////////////////////////////////////////////////////////////////////
// TestList

window.TestList = function TestList() {
}
TestList.search = function TestList$search(page) {
    /// <param name="page" type="Number" integer="true">
    /// </param>
    var search = $("input[name='testsearch']").val();
    search = eval('escape(search)');
    var option = { search: search, order: $("input[name='testorder']:checked").val(), type: (document.URL.indexOf('/quizz/taken') >= 0) ? 'taken' : ((document.URL.indexOf('/quizz/created') >= 0) ? 'created' : 'index'), page: page };
    $('.quizzlist').load('/quizz/list', option, function(obj) {
    }).error(function(e) {
        alert('An error occur, please try again later.');
    });
}
TestList._truncateText = function TestList$_truncateText(text, maxCharacter) {
    /// <param name="text" type="String">
    /// </param>
    /// <param name="maxCharacter" type="Number" integer="true">
    /// </param>
    /// <returns type="String"></returns>
    if (maxCharacter == null || maxCharacter > 310) {
        maxCharacter = 310;
    }
    if (text.length < maxCharacter) {
        return text;
    }
    text = text.substr(0, maxCharacter);
    var lastIndex = text.lastIndexOf(' ');
    if (lastIndex > maxCharacter - 10) {
        return text.substr(0, lastIndex) + '...';
    }
    return text.substr(0, maxCharacter - 3) + '...';
}
TestList.truncateDescription = function TestList$truncateDescription() {
    $('.testdescription').each(function(index, ele) {
        var jq = $(ele);
        jq.text(TestList._truncateText(jq.text(), 300));
        jq.css('display', 'block');
    });
}


////////////////////////////////////////////////////////////////////////////////
// ItemTypeDef

window.ItemTypeDef = function ItemTypeDef() {
    /// <field name="multiChoice" type="String" static="true">
    /// </field>
    /// <field name="html" type="String" static="true">
    /// </field>
    /// <field name="photo" type="String" static="true">
    /// </field>
    /// <field name="pageBreak" type="String" static="true">
    /// </field>
    /// <field name="shortAnswer" type="String" static="true">
    /// </field>
    /// <field name="multiSelect" type="String" static="true">
    /// </field>
}


////////////////////////////////////////////////////////////////////////////////
// AnswerItem

window.AnswerItem = function AnswerItem() {
    /// <field name="answerId" type="String">
    /// </field>
    /// <field name="answer" type="String">
    /// </field>
    /// <field name="pures" type="Object">
    /// </field>
    /// <field name="selected" type="Boolean">
    /// </field>
    this.pures = {};
}
AnswerItem.prototype = {
    answerId: '',
    answer: '',
    selected: false
}


////////////////////////////////////////////////////////////////////////////////
// QuestionItem

window.QuestionItem = function QuestionItem(type) {
    /// <param name="type" type="String">
    /// </param>
    /// <field name="questionId" type="String">
    /// </field>
    /// <field name="itemType" type="String">
    /// </field>
    /// <field name="question" type="String">
    /// </field>
    /// <field name="answers" type="Array">
    /// </field>
    /// <field name="html" type="String">
    /// </field>
    /// <field name="buttonNext" type="String">
    /// </field>
    /// <field name="imagePath" type="String">
    /// </field>
    this.itemType = ItemTypeDef.multiChoice;
    this.itemType = type;
}
QuestionItem.prototype = {
    questionId: '',
    question: null,
    answers: null,
    html: null,
    buttonNext: null,
    imagePath: null
}


////////////////////////////////////////////////////////////////////////////////
// Test

window.Test = function Test() {
    /// <field name="title" type="String">
    /// </field>
    /// <field name="items" type="Array">
    /// </field>
    /// <field name="testId" type="String">
    /// </field>
    /// <field name="totalPage" type="Number" integer="true">
    /// </field>
    /// <field name="page" type="Number" integer="true">
    /// </field>
    /// <field name="parseError" type="Array">
    /// </field>
    /// <field name="status" type="String">
    /// </field>
    /// <field name="testBasic" type="TestBasic">
    /// </field>
    /// <field name="testScore" type="TestScore">
    /// </field>
    this.items = [];
    this.parseError = [];
}
Test.prototype = {
    title: null,
    testId: null,
    totalPage: 0,
    page: 0,
    status: 'editing',
    testBasic: null,
    testScore: null
}


////////////////////////////////////////////////////////////////////////////////
// UserAnswer

window.UserAnswer = function UserAnswer(name, value) {
    /// <param name="name" type="String">
    /// </param>
    /// <param name="value" type="String">
    /// </param>
    /// <field name="name" type="String">
    /// </field>
    /// <field name="value" type="Object">
    /// </field>
    this.name = name;
    this.value = value;
}
UserAnswer.prototype = {
    name: null,
    value: null
}


////////////////////////////////////////////////////////////////////////////////
// ResultStats

window.ResultStats = function ResultStats() {
    /// <field name="scoreName" type="String">
    /// </field>
    /// <field name="percent" type="Number" integer="true">
    /// </field>
    /// <field name="score" type="Number" integer="true">
    /// </field>
    /// <field name="minX" type="Number" integer="true">
    /// </field>
    /// <field name="maxX" type="Number" integer="true">
    /// </field>
    /// <field name="samplePoints" type="Array" elementType="Point">
    /// </field>
    /// <field name="scaling" type="String">
    /// </field>
}
ResultStats.prototype = {
    scoreName: null,
    percent: 0,
    score: 0,
    minX: 0,
    maxX: 0,
    samplePoints: null,
    scaling: 'Percentage'
}


////////////////////////////////////////////////////////////////////////////////
// TestResult

window.TestResult = function TestResult() {
    /// <field name="detail" type="Test">
    /// </field>
    /// <field name="total" type="Object">
    /// </field>
    /// <field name="takenTime" type="String">
    /// </field>
    /// <field name="chartDatas" type="Array" elementType="ResultStats">
    /// </field>
    /// <field name="scoreItemResults" type="Array" elementType="ScoreItemResult">
    /// </field>
    this.total = {};
    this.scoreItemResults = new Array(0);
}
TestResult.prototype = {
    detail: null,
    takenTime: null,
    chartDatas: null
}


////////////////////////////////////////////////////////////////////////////////
// ScoreItemResult

window.ScoreItemResult = function ScoreItemResult() {
    /// <field name="title" type="String">
    /// </field>
    /// <field name="subTitle" type="String">
    /// </field>
    /// <field name="description" type="String">
    /// </field>
    /// <field name="imageUrl" type="String">
    /// </field>
}
ScoreItemResult.prototype = {
    title: null,
    subTitle: null,
    description: null,
    imageUrl: null
}


////////////////////////////////////////////////////////////////////////////////
// TestBasic

window.TestBasic = function TestBasic() {
    /// <field name="description" type="String">
    /// </field>
    /// <field name="maturity" type="String">
    /// </field>
    /// <field name="category" type="String">
    /// </field>
    /// <field name="subCategory" type="String">
    /// </field>
    /// <field name="variables" type="Array">
    /// </field>
    /// <field name="thumnailUrl" type="String">
    /// </field>
    this.variables = [];
}
TestBasic.prototype = {
    description: '',
    maturity: 'Teen',
    category: '',
    subCategory: '',
    thumnailUrl: ''
}


////////////////////////////////////////////////////////////////////////////////
// TestScore

window.TestScore = function TestScore() {
    /// <field name="scaling" type="String">
    /// </field>
    /// <field name="scoreItems" type="Array">
    /// </field>
    this.scoreItems = [];
}
TestScore.prototype = {
    scaling: 'Raw'
}


////////////////////////////////////////////////////////////////////////////////
// ScoreItem

window.ScoreItem = function ScoreItem() {
    /// <field name="requirements" type="Array">
    /// </field>
    /// <field name="title" type="String">
    /// </field>
    /// <field name="subTitle" type="String">
    /// </field>
    /// <field name="description" type="String">
    /// </field>
    /// <field name="imageUrl" type="String">
    /// </field>
    this.requirements = [];
}
ScoreItem.prototype = {
    title: null,
    subTitle: null,
    description: null,
    imageUrl: null
}


////////////////////////////////////////////////////////////////////////////////
// Requirement

window.Requirement = function Requirement() {
    /// <field name="greaterThanValue" type="String" static="true">
    /// </field>
    /// <field name="lessThanValue" type="String" static="true">
    /// </field>
    /// <field name="greaterThanVar" type="String" static="true">
    /// </field>
    /// <field name="lessThanVar" type="String" static="true">
    /// </field>
    /// <field name="greatestVar" type="String" static="true">
    /// </field>
    /// <field name="leastVar" type="String" static="true">
    /// </field>
    /// <field name="variable" type="String">
    /// </field>
    /// <field name="condition" type="String">
    /// </field>
    /// <field name="value" type="String">
    /// </field>
    /// <field name="objVariable" type="String">
    /// </field>
}
Requirement.prototype = {
    variable: null,
    condition: null,
    value: null,
    objVariable: null
}


Type.registerNamespace('Quizz');

////////////////////////////////////////////////////////////////////////////////
// Quizz.QuizzChart

Quizz.QuizzChart = function Quizz_QuizzChart() {
    /// <field name="_hMin" type="Number" integer="true" static="true">
    /// </field>
    /// <field name="_hMax" type="Number" integer="true" static="true">
    /// </field>
    /// <field name="_vMin" type="Number" static="true">
    /// </field>
    /// <field name="_vMax" type="Number" static="true">
    /// </field>
    /// <field name="_step" type="Number" integer="true" static="true">
    /// </field>
    /// <field name="_samplePoints" type="Array">
    /// </field>
    /// <field name="_curPosition" type="Number">
    /// </field>
    /// <field name="_minX" type="Number" integer="true">
    /// </field>
    /// <field name="_maxX" type="Number" integer="true">
    /// </field>
    /// <field name="_minY" type="Number">
    /// </field>
    /// <field name="_maxY" type="Number">
    /// </field>
    /// <field name="_width" type="Number" integer="true">
    /// </field>
    /// <field name="_height" type="Number" integer="true">
    /// </field>
    /// <field name="_datatable" type="Array">
    /// </field>
    this._samplePoints = [];
    this._curPosition = Quizz.QuizzChart._hMin;
    this._minX = Quizz.QuizzChart._hMin;
    this._maxX = 10;
    this._datatable = [];
}
Quizz.QuizzChart.prototype = {
    _minY: 0,
    _maxY: 2000000000,
    _width: 210,
    _height: 95,
    
    init: function Quizz_QuizzChart$init(samplePoints, curPosition, minX, maxX, width, height) {
        /// <param name="samplePoints" type="Array" elementType="Point">
        /// </param>
        /// <param name="curPosition" type="Number">
        /// </param>
        /// <param name="minX" type="Number" integer="true">
        /// </param>
        /// <param name="maxX" type="Number" integer="true">
        /// </param>
        /// <param name="width" type="Number" integer="true">
        /// </param>
        /// <param name="height" type="Number" integer="true">
        /// </param>
        this._width = (width) ? width : 210;
        this._height = (height) ? height : 95;
        this._minX = minX;
        this._maxX = maxX;
        if (this._minX >= this._maxX) {
            this._maxX = this._minX + 1;
        }
        if (samplePoints.length > 0) {
            for (var index = this._minX; index <= this._maxX; index++) {
                var point = this._findPoint(samplePoints, index);
                if (point == null) {
                    point = new Quizz.Point(index, 0);
                }
                var x = point.x;
                var y = Math.max(0, point.y);
                if (index === this._minX) {
                    this._minY = this._maxY = y;
                }
                else {
                    if (this._minY > y) {
                        this._minY = y;
                    }
                    if (this._maxY < y) {
                        this._maxY = y;
                    }
                }
                if (index - 1 < curPosition && curPosition <= index) {
                    this._curPosition = index;
                }
                this._samplePoints.add(new Quizz.Point(x, y));
            }
        }
        this._spline();
    },
    
    _findPoint: function Quizz_QuizzChart$_findPoint(samplePoints, x) {
        /// <param name="samplePoints" type="Array" elementType="Point">
        /// </param>
        /// <param name="x" type="Number" integer="true">
        /// </param>
        /// <returns type="Quizz.Point"></returns>
        var $enum1 = ss.IEnumerator.getEnumerator(samplePoints);
        while ($enum1.moveNext()) {
            var point = $enum1.current;
            if (point.x === x) {
                return point;
            }
        }
        return null;
    },
    
    statisticChart_old: function Quizz_QuizzChart$statisticChart_old(samplePoints, curPosition, minX, maxX, width, height) {
        /// <param name="samplePoints" type="Array" elementType="Point">
        /// </param>
        /// <param name="curPosition" type="Number">
        /// </param>
        /// <param name="minX" type="Number" integer="true">
        /// </param>
        /// <param name="maxX" type="Number" integer="true">
        /// </param>
        /// <param name="width" type="Number" integer="true">
        /// </param>
        /// <param name="height" type="Number" integer="true">
        /// </param>
        this._width = (width) ? width : 210;
        this._height = (width) ? width : 95;
        this._minX = minX;
        this._maxX = maxX;
        if (this._minX === this._maxX) {
            this._maxX = this._minX + 1;
        }
        if (!samplePoints.length) {
            samplePoints[samplePoints.length] = new Quizz.Point(0, 0);
        }
        if (samplePoints.length === 1) {
            samplePoints[samplePoints.length] = new Quizz.Point(samplePoints[0].x + 1, 0);
        }
        for (var i = 0; i < samplePoints.length; i++) {
            var point = samplePoints[i];
            var x = Math.min(10, Math.max(Quizz.QuizzChart._hMin, point.x));
            var y = Math.min(0, point.y);
            if (!i) {
                this._minX = this._maxX = parseInt(x);
                this._minY = this._maxY = y;
            }
            else {
                if (this._minY > y) {
                    this._minY = y;
                }
                if (this._maxY < y) {
                    this._maxY = y;
                }
            }
            if (!i || this._samplePoints[i - 1].x < x) {
                this._samplePoints.add(new Quizz.Point(x, y));
            }
        }
        if (minX && minX < this._minX) {
            this._minX = Math.max(Quizz.QuizzChart._hMin, minX);
        }
        if (maxX && maxX > this._maxX) {
            this._maxX = Math.min(10, maxX);
        }
        this._curPosition = (curPosition) ? Math.min(this._maxX, Math.max(this._minX, curPosition)) : this._minX;
        this._spline();
    },
    
    _spline: function Quizz_QuizzChart$_spline() {
        var lx = [];
        var ly = [];
        var $enum1 = ss.IEnumerator.getEnumerator(this._samplePoints);
        while ($enum1.moveNext()) {
            var point = $enum1.current;
            lx[lx.length] = point.x;
            ly[ly.length] = point.y;
        }
        var splineFunc = null;
        splineFunc = new CubicSpline(lx, ly);
        this._datatable = [];
        var height = Math.max(50, this._height - 10);
        if (this._samplePoints.length > 0) {
            for (var index = 0; index < this._width + 3; index += 3) {
                if (index > this._width) {
                    index = this._width;
                }
                var x = this._minX + (this._maxX - this._minX) * index / this._width;
                x = Math.min(x, this._maxX);
                var y = 0;
                y = splineFunc.interpolate(x);
                if (x === this._maxX) {
                    y = this._samplePoints[this._samplePoints.length - 1].y;
                }
                y = Math.max(0, y);
                var value = Math.max(1, y / ((!this._maxY) ? 1 : this._maxY) * height);
                var my = null;
                if (index - 3 < (this._curPosition - this._minX) / (this._maxX - this._minX) * this._width && (this._curPosition - this._minX) / (this._maxX - this._minX) * this._width <= index) {
                    my = value;
                }
                this._datatable.add([ index / 3, value, ((this._curPosition >= x) ? value : null), my ]);
            }
        }
    },
    
    draw: function Quizz_QuizzChart$draw(element) {
        /// <param name="element" type="Object" domElement="true">
        /// </param>
        var data = new google.visualization.DataTable();
            data.addColumn('number', 'X');
            data.addColumn('number', 'statistic');
            data.addColumn('number', 'You');
            data.addColumn('number', 'Current');
            var options = { 'titlePosition': 'none', 
                'width': this._width + 10, 
                'height': this._height,
                'lineWidth': 1,
                'chartArea': {left:0, top: 0, width: this._width + 1, height: this._height},
                'hAxis': { 'gridlines': { count: 2 }, textPosition: 'none', maxValue:  this._width + 10},
                'vAxis': { 'gridlines': { count: 2 }, textPosition: 'none' },
                'legend': { 'position': 'none' },
                'colors': ['#4eac25', '#4eac25'],
                'tooltip': { 'trigger': 'none' },
                'series': [{}, {}, { 'pointSize': 7}]
            };
            if(this._datatable.length >0){
                data.addRows(this._datatable);
            }
            var chart = new google.visualization.AreaChart(element);
            chart.draw(data, options);;
    },
    
    getMyPercent_raw: function Quizz_QuizzChart$getMyPercent_raw() {
        /// <returns type="Number"></returns>
        var result = 0;
        var area = 0;
        var myarea = 0;
        var lower = 0;
        for (var i = 0; i < this._samplePoints.length; i++) {
            var point = this._samplePoints[i];
            area += point.y;
            if (point.x <= this._curPosition) {
                myarea += point.y;
            }
            if (point.x < this._curPosition) {
                lower += point.y;
            }
        }
        result = (area > 0) ? (lower / area) : 0;
        return Math.round(result * 100);
    },
    
    getMyPercent: function Quizz_QuizzChart$getMyPercent() {
        /// <returns type="Number"></returns>
        var result = 0;
        var area = 0;
        var myarea = 0;
        for (var i = 0; i < this._samplePoints.length; i++) {
            var point = this._samplePoints[i];
            if (i > 0) {
                var prePoint = this._samplePoints[i - 1];
                var newArea = (prePoint.y + point.y) * (point.x - prePoint.x) / 2;
                area += newArea;
                if (point.x <= this._curPosition) {
                    myarea += newArea;
                }
            }
        }
        result = (area > 0) ? (myarea / area) : 0;
        return Math.round(result * 100);
    }
}


////////////////////////////////////////////////////////////////////////////////
// Quizz.Point

Quizz.Point = function Quizz_Point(x, y) {
    /// <param name="x" type="Number">
    /// </param>
    /// <param name="y" type="Number">
    /// </param>
    /// <field name="x" type="Number">
    /// </field>
    /// <field name="y" type="Number">
    /// </field>
    this.x = x;
    this.y = y;
}
Quizz.Point.prototype = {
    x: 0,
    y: 0
}


Advice.registerClass('Advice');
Basic.registerClass('Basic');
Score.registerClass('Score');
TestEditor.registerClass('TestEditor');
TestList.registerClass('TestList');
ItemTypeDef.registerClass('ItemTypeDef');
AnswerItem.registerClass('AnswerItem');
QuestionItem.registerClass('QuestionItem');
Test.registerClass('Test');
UserAnswer.registerClass('UserAnswer');
ResultStats.registerClass('ResultStats');
TestResult.registerClass('TestResult');
ScoreItemResult.registerClass('ScoreItemResult');
TestBasic.registerClass('TestBasic');
TestScore.registerClass('TestScore');
ScoreItem.registerClass('ScoreItem');
Requirement.registerClass('Requirement');
Quizz.QuizzChart.registerClass('Quizz.QuizzChart');
Quizz.Point.registerClass('Quizz.Point');
Advice._advices = [ '<style type="text/css">\r\n            .testpage .advice #mobility .container .guts p {\r\n\t            margin-bottom: 0.625em;\r\n            }\r\n\r\n            .testpage .advice .advice-heading { margin-top: 6px;  margin-bottom: 12px;}\r\n\r\n            .testpage .advice .advice-heading {\r\n\t            font-size:1.5em;\r\n            }\r\n            .testpage .advice .example-question {\r\n\t            border: 4px solid #beeec5;\r\n\t            background-color:#38a20a;\r\n\t            padding:10px;\r\n\t            margin: 10px 0;\r\n\t            font-size: 0.8em;\r\n\t            color: #f9f9f9;\r\n            }\r\n            .testpage .advice .example-question h4 {\r\n\t            margin: 0 0 8px 0;\r\n\t            font-size: 17px;\r\n\t            color: #f9f9f9;\r\n            }\r\n            .testpage .advice .example-question p,\r\n            .testpage .advice .example-question ul li {\r\n\t            font-size: 13px;\r\n            }\r\n            .testpage .advice .example-question ul {\r\n\t            margin: 8px 18px;\r\n            }\r\n            .testpage .advice ol, ul {\r\n\t            list-style: none;\r\n            }\r\n            .testpage .advice .bad {\r\n                color: #f9f9f9;\r\n\t            background-color:rgb(172, 0, 12);\r\n\t            border: 4px solid #eebebe;\r\n            }\r\n            .testpage .advice i\r\n            {\r\n\t            font-size: 13px;\r\n            }\r\n        </style>\r\n\r\n        <p class="advice-heading">\r\n            Keep your answers short</p>\r\n        <p>\r\n            Test takers are like drunk children. They need simple pleasure or they&#39;ll pass \r\n            out. &quot;True/false,&quot; &quot;Yes/No/Maybe,&quot;-these are understandable by everyone. \r\n            Consider the following scenario question:</p>\r\n        <div class="example-question bad">\r\n            <h4>\r\n                Bad Question: Reader might fall asleep</h4>\r\n            <p>\r\n                You&#39;ve just cooked a really tasty meal for your significant other, and it took \r\n                you 4 hours to prepare all the courses. He or she shows up at home, throws down \r\n                the briefcase, and heads out to join friends. Without eating, and with no \r\n                apology. Do you...</p>\r\n            <ul>\r\n                <li>(a) Follow them out the door, with a pot of food, and dump it on them. Hey, they \r\n                    can either eat it or wear it.</li>\r\n                <li>(b) Try to be understanding; friendship is important and we didn plan the dinner \r\n                    ahead of time</li>\r\n            </ul>\r\n        </div>\r\n        <p>\r\n            Your test will never be popular like this. Be snappy!</p>\r\n        <div class="example-question">\r\n            <h4>\r\n                Good question</h4>\r\n            <p>\r\n                Scenario: you surprise your lover with a 7-course home-cooked meal, but (s)he \r\n                wants to skip out to see friends. Is that cool?</p>\r\n            <ul>\r\n                <li>(a) Sure</li>\r\n                <li>(b) Not really</li>\r\n                <li>(c) No</li>\r\n            </ul>\r\n        </div>\r\n        <p>\r\n            Quick tip: answer options should never wrap lines.</p>', '<p class="advice-heading">\r\n            Offer complete answer options</p>\r\n        <p>\r\n            Test takers are also like robots. Their heads explode when they run out of \r\n            options. For example:</p>\r\n        <div class="example-question bad">\r\n            <h4>\r\n                Bad question: Not enough answer options</h4>\r\n            <p>\r\n                You catch your roommate and your boyfriend together. What do you do?</p>\r\n            <ul>\r\n                <li>(a) I fight him to the death.</li>\r\n                <li>(b) I fight her to the death.</li>\r\n            </ul>\r\n        </div>\r\n        <p>\r\n            Sayonara, test taker. Robot-man needs more options. What if he would just roll \r\n            away? What if he would confront them with words and data? Be general, but \r\n            all-encompassing:</p>\r\n        <div class="example-question">\r\n            <p>\r\n                ...what do you do?</p>\r\n            <ul>\r\n                <li>(a) Confront</li>\r\n                <li>(b) Move on</li>\r\n                <li>(c) Other / I don&#39;t know</li>\r\n            </ul>\r\n        </div>', '<p class="advice-heading">\r\n            Measure something meaningful</p>\r\n        <p>\r\n            Congratulations on your funny test. To make it go all viral, give your readers \r\n            something accurate to compare. A purity percentage, a colorful personality type, \r\n            a political party, X% of your friends HATE you: these are powerful, telling \r\n            titles and people will want to share. On the other end, &quot;Great job! You got 35 \r\n            lollipop points!&quot; at the end of a test is not so helpful. Give people a \r\n            meaningful result.\r\n        </p>', '<p class="advice-heading">\r\n            Make sure everyone doesn&#39;t get the same result</p>\r\n        <p>\r\n            The launch of your test is just the beginning. You should watch your stats \r\n            (accessible from the &quot;Edit your tests&quot; page), and you should adjust scoring or \r\n            results rules to make sure people are getting a variety of results. If you write \r\n            a Which-Superhero-Are-You-Test that has 8 results, make sure people are scoring \r\n            all 8 results. If you write a Purity test, make sure some people score high and \r\n            some low.</p>', '<p class="advice-heading">\r\n            Include nice pictures and check your spelling and grammar</p>\r\n        <p>\r\n            The more professional your test, the more it will spread around. Grammar and \r\n            spelling nazis are teh sux0rz, but they are still part of your audience. \r\n            Accommodate those with higher standards, without pissing off those without.</p>', '        <p class="advice-heading">\r\n            When your test is polished, make it famous!</p>\r\n        <p>\r\n            Sometimes a test just needs some activation energy. For example, if you write a \r\n            quiz about topic X, seek out forums on X. Email your friends. Find people who \r\n            blog all about X and send your quiz to them. Odds are, they&#39;ll love posting \r\n            about it and thank you. Giving your test a push in the right community can be \r\n            the difference between 100 takers and 100,000 takers. So Google is your friend. \r\n            Research the communities and experts and let them all know.</p>' ];
Advice._adviceHeaderTemplate = "                \r\n                <li class=\"question pagetext\">\r\n                    <div class=\"question-left\">\r\n                        <div style=''>SOME ADVICE</div>\r\n                    </div>\r\n                    <div class=\"question-main\">\r\n                        <p class=\"question-question\">\r\n                            <i>At Meetsi/Quizzy, we have noticed some trends among the most popular, highest rated tests. \r\n                            This advice is yours to ignore, but we recommend reading the checklist.</i>\r\n                        </p>                        \r\n                    </div>\r\n                    <div class=\"question-right\" style=\"display:none;\">\r\n                    </div>\r\n                </li>";
Advice._adviceFooterTemplate = "                \r\n                <li class=\"question pagetext\">\r\n                    <div class=\"question-left\">\r\n                        <div style=''>LAST...</div>\r\n                    </div>\r\n                    <div class=\"question-main\">\r\n                        <p class=\"question-question\">\r\n                            <i>We will add more advice here soon. Thanks for reading... - Meetsi Staff</i>\r\n                        </p>                        \r\n                    </div>\r\n                    <div class=\"question-right\" style=\"display:none;\">\r\n                    </div>\r\n                </li>";
Advice._adviceTemplate = "\r\n                <li class=\"question pagetext\">\r\n                    <div class=\"question-left\">\r\n                        <div class='leftnumber'>{0}</div>\r\n                    </div>\r\n                    <div class=\"question-main\"> \r\n                        <p class=\"question-question\">\r\n                        </p>                          \r\n                    </div>\r\n                    <div class=\"question-right\" style=\"display:none;\">\r\n                    </div>\r\n                </li>";
Basic._categories = { Advice: [ 'Career', 'Hobbies', 'Other', 'Self-Improvement' ], 'Animals and Pets': [ 'Household Pets', 'Maulings', 'Trivia', 'Wild Animals' ], 'Arts and Leisure': [], 'Books and Scholarship': [ 'Fiction', 'History', 'Nerd Studies', 'Non-Fiction' ], 'Life Experience': [ 'Knowledge / Trivia', 'Languages', 'Places and Travel', 'Purity', 'Sex', 'Substances', 'Touchy Subjects' ], Lifestyle: [ 'Hobbies', 'Sexuality' ], 'Me, Myself, and I': [ 'Compatibility: Female Author Testing Both', 'Compatibility: Female Author Testing Females', 'Compatibility: Female Author Testing Males', 'Compatibility: Male Author Testing Both', 'Compatibility: Male Author Testing Females', 'Compatibility: Male Author Testing Males', 'How Similar Are We?', 'How Well Do You Know Me?', 'Other' ], Philosophy: [ 'Deep Shit', 'Semi-Deep Shit' ], 'Politics and News': [ 'Current Events', 'Knowledge / Trivia', 'Social Theory', 'Touchy Subjects', 'Voting' ], Psychology: [ 'Personality' ], 'Religion and Spirituality': [ 'Astrology', 'Atheism', 'Figuring Things Out', 'Mythology', 'Religion', 'Tom Cruise' ], Sports: [ 'Knowledge / Trivia', 'Obsession' ], 'TV, Music, and Movies': [ 'Celebrities', 'Characters', 'Knowledge / Trivia', 'Lyrics', 'Obsession' ], Trivia: [ 'Art', 'Food', 'History', 'Humanities', 'Other', 'People', 'Places', 'Pop Culture', 'Science and Math', 'Technology' ], Uncategorizable: [], Wacky: [ 'Experimental', 'Funny', 'Nonsensical', 'Other', 'WTF' ] };
Basic._basicLables = [ 'DESCRIPTION', 'MATURITY RATING', 'CATEGORY', 'VARIABLES', 'THUMBNAIL' ];
Basic._basics = [ '\r\n          <p>Write a sentence that summarizes your test, for our catalogue.</p>\r\n           <p class="question-question" onclick="TestEditor.editQuestion(this); return false;">\r\n                Find out if you can survive when the zombies attack.</p>\r\n            <div class="question-question-editor">\r\n                <textarea></textarea>\r\n                <br />\r\n                <a class="button-done" href="#" onclick="TestEditor.endEditQuestion(this); return false;">Done</a>\r\n            </div>', '<select name="test-rating" class="test-rating" onchange="Basic.updateTestRating(this)">\r\n            <option value="Family">Family</option>\r\n            <option value="Teen" selected="selected">Teen</option>\r\n            <option value="Adult">Adult</option>\r\n           </select>\r\n           <span class="rating-summary-area">Clean and pure for grandmas and babies.</span>\r\n           <span class="rating-summary-area" style="display:inline;">May contain dirty language, near nudity.</span>\r\n           <span class="rating-summary-area">Appropriate for adults only. No hard-core. Think "R" rated.</span>', '<p>If possible, choose a category and subcategory that fit your test.</p>\r\n        <p>\r\n            <select class="test-category" onchange="Basic.updateTestCategory(this)" name="test-category">\r\n            </select> \r\n            <span style="display:none;">under</span>\r\n            <select class="test-subcategory" style="display:none;">\r\n            </select>\r\n        </p>\r\n        ', '<p>Simple tests that measure one thing about you (e.g. "purity") require one variable. \r\n            Tests that explore multiple parts of your personality, like, say "knowledge" and "potential" require multiple.</p>\r\n        <ul class="variable-listing">\r\n            <li class="variable">\r\n                <label class="variable-label">Variable Name</label>      \r\n                <input onchange="Basic.renameVariable(this);" value="Pure" varname="Pure" type="text">\r\n                <a class="remove" onclick="Basic.removeVariable(this); return false;">remove</a>\r\n            </li>\r\n            <li>\r\n                <a class="add" onclick="Basic.addVariable(this); return false;">add a variable</a>\r\n            </li>   \r\n          </ul>', "<p>And (optional) upload a picture that will go next to your test name.</p>\r\n            <div class=\"fileupload\"></div>\r\n            <div class='imgname' style='display:none'><br /></div>\r\n            <div class=\"image-container\">\r\n                <img src=\"\" onload=\"TestEditor.imageLoaded(this);\" style='display:none; margin: 0px auto 0px auto; ' />\r\n            </div>\r\n            <div class='clear'></div>" ];
Basic._basicTemplate = "\r\n                <li class=\"question pagetext\">\r\n                    <div class=\"question-left\">\r\n                        <div style=''>{0}</div>\r\n                    </div>\r\n                    <div class=\"question-main\">                         \r\n                    </div>\r\n                    <div class=\"question-right\" style=\"display:none;\">\r\n                    </div>\r\n                </li>";
Basic._variableTemplate = '\r\n            <li class="variable">\r\n                <label class="variable-label">Variable Name</label>      \r\n                <input onchange="Basic.renameVariable(this);" value="Pure" type="text">\r\n                <a class="remove" onclick="Basic.removeVariable(this); return false;">remove</a>\r\n            </li>';
Basic.lastVariableIndexId = 1;
Score._initHtml = '\r\n    <div class="level1" style="clear: both;">\r\n        <h3>\r\n            Variables</h3>\r\n        <div>\r\n            <p>\r\n                Use these variable ranges when creating your different result types.</p>\r\n            <ul class="varlist">\r\n                <li class="varitem"><strong>Pure</strong> scores range <strong>0</strong> to <strong>\r\n                    1</strong>.</li>\r\n            </ul>\r\n        </div>\r\n        <div class="clear">\r\n        </div>\r\n    </div>\r\n    <div class="level1 scalingtype" style="clear: both;">\r\n        <h3>\r\n            Scaling Type</h3>\r\n        <div>\r\n            <p>\r\n                Switch between raw scores (the ranges listed above) and percents. .</p>\r\n            <p>\r\n                <a class="activebtn" onclick="Score.useScore(this) ;return false;" href="javascript:void();">Use Raw Scores</a> \r\n                <a class="disactivebtn" style="margin-left:20px;" onclick="Score.useScore(this) ;return false;" href="javascript:void();">Using Percentages</a>\r\n            </p>\r\n        </div>\r\n        <div class="clear">\r\n        </div>\r\n    </div>\r\n    <div class="level1" style="clear: both;">\r\n        <h3>\r\n            Result Types</h3>\r\n        <div>\r\n            <p>\r\n                <a class="add" href="javascript:void();" onclick="Score.addResultType(this);" >Add a new result type</a>\r\n            </p>\r\n        </div>\r\n        <div class="clear">\r\n        </div>\r\n    </div>\r\n    <ul style="list-style-type: none; list-style-position: outside; list-style-image: none;" class="scorelist">        \r\n    </ul>';
Score._resultTypeTemplate = "<li style=\"\">\r\n            <div class=\"level1\" style=\"clear: both; position: relative; height: 1px; padding: 0px 0px 0px px;\">\r\n                <a href=\"javascript:void();\" onclick=\"Score.deleteResultType(this);\" style=\"position: absolute; right: 10px; top: -10px;\" class=\"topdeletebtn\">delete\r\n                </a>\r\n                <div class=\"clear\">\r\n                </div>\r\n            </div>\r\n            <div class=\"level1\" style=\"clear: both;\">\r\n                <h4>\r\n                    Give This Result\r\n                </h4>\r\n                <div class=\"requirements-container\">\r\n                </div>\r\n                <div class=\"clear\">\r\n                </div>\r\n            </div>\r\n            <div class=\"level1\" style=\"clear: both;\">\r\n                <h4>\r\n                    Title\r\n                </h4>\r\n                <div>\r\n                    <div>\r\n                        <input class=\"reqtitle viewmode\" style=\"\" value=\"Add title here\" onclick=\"Score.enterEditInput(this)\" />\r\n                    </div>\r\n                    <div style=\"display:none\">\r\n                        <div class=\"caution\">\r\n                            <strong>This is their headline!</strong><i> Pick something powerful and suggestive.</i></div>\r\n                        <a href=\"javascript:void();\" onclick=\"Score.leaveEditInput(this)\" class=\"button-done\">Done</a>\r\n                    </div>\r\n                </div>\r\n                <div class=\"clear\">\r\n                </div>\r\n            </div>\r\n            <div class=\"level1\" style=\"clear: both;\">\r\n                <h4>\r\n                    Subtitle\r\n                </h4>\r\n                <div>\r\n                    <div>\r\n                        <input class=\"reqsubtitle viewmode\"  value=\"$(Pure)% Pure!\" style=\"\" onclick=\"Score.enterEditInput(this)\"/>\r\n                    </div>\r\n                    <div style=\"display:none\">\r\n                        <div class=\"caution\">\r\n                            <strong>Tip!</strong> You can refer to the taker's score on a given variable by\r\n                            wrapping it in $( ). For example: <i>$(Pure) percent on Pure. Well done!</i></div>\r\n                        <a href=\"javascript:void();\" onclick=\"Score.leaveEditInput(this)\" class=\"button-done\">Done</a>\r\n                    </div>\r\n                </div>\r\n                <div class=\"clear\">\r\n                </div>\r\n            </div>\r\n            <div class=\"level1\" style=\"clear: both;\">\r\n                <h4>\r\n                    Description\r\n                </h4>\r\n                <div class=\"question-main\">\r\n                   <p class=\"question-question\" onclick=\"TestEditor.editQuestion(this); return false;\">\r\n                        You scored $(Pure)% pure. Author: here you should write a description of this test result, telling the taker what his/her score means and why they got it. \r\n                        Note: in the description or title of a test result, you can refer to someone's score on a variable by wrapping the variable name in $(). \r\n                        For example, you could say here, Hey! You scored $(intelligence) on intelligence. Brilliant!</p>\r\n                    <div class=\"question-question-editor\">\r\n                        <textarea></textarea>\r\n                        <br />\r\n                        <a class=\"button-done testtest\" href=\"#\" onclick=\"TestEditor.endEditQuestion(this); return false;\">Done</a>\r\n                    </div>\r\n                </div>\r\n                <div class=\"clear\">\r\n                </div>\r\n            </div>\r\n            <div class=\"level1\" style=\"\">\r\n                <h4>\r\n                    Image\r\n                </h4>\r\n                <div>\r\n                    <div>\r\n                        <div class=\"fileupload\"></div>\r\n                        <div class='imgname' style='display:none'><br /></div>\r\n                        <div class=\"image-container\">\r\n                            <img src=\"\" style='display:none; margin: 0px auto 0px auto; ' />\r\n                        </div>\r\n                    </div>\r\n                    <div>\r\n                    </div>\r\n                </div>\r\n                <div class=\"clear\">\r\n                </div>\r\n            </div>\r\n            <div style=\"clear: both;\">\r\n            </div>\r\n        </li>";
Score._requirementContainerTemplate = '\r\n                    <ul class="requirements">\r\n                        <li>You have to add a <a onclick="Score.addRequirement(this)" href="javascript:void();" style="color:#207cf2">requirement</a> or this result will be ignored.</li>\r\n                        <li style="display:none"><a onclick="Score.addRequirement(this)" href="javascript:void();" class="add">add another requirement</a></li>\r\n                    </ul>';
Score._requirementDefaultTemplate = '<p><i>... to everyone who takes your test.</i></p>';
Score._requirementTemplate = '\r\n        <li>If\r\n            <select class="varoptions reqvar">\r\n            </select>\r\n            is\r\n            <select onchange="Score.reqConditionChange(this);">\r\n                <option value="GreaterThanValue">greater than a value</option>\r\n                <option value="GreaterThanVar">greater than another variable</option>\r\n                <option value="GreatestVar">the greatest variable</option>\r\n                <option selected="" value="LessThanValue">less than a value</option>\r\n                <option value="LessThanVar">less than a another variable</option>\r\n                <option value="LeastVar">the least variable</option>\r\n            </select>\r\n            <input type="text" class="reqvalue" style="width:40px;" />\r\n            <span class="requnit">%</span>\r\n            <select class="varoptions reqvarobj" style="display:none;">\r\n            </select>\r\n            <a onclick="Score.removeRequirement(this);" href="javascript:void();" class="remove">remove</a>\r\n        </li>';
TestEditor._testTitleTemplate = "\r\n        <h2 class=\"testheader\">\r\n            <label for=\"test-title\">\r\n                You are editing:</label>\r\n            <input class=\"test-title\" name=\"test-title\" onchange=\"return 0\"; type=\"text\" value=\"{0}\"\r\n                onfocus=\"this.style.backgroundColor = '#FFFFE0';\" />\r\n            <input type=\"hidden\" name=\"testid\" value=\"\"/>\r\n            <input type=\"hidden\" name=\"status\" value=\"\"/>\r\n        </h2>\r\n        <div class=\"questions\" style=\"clear: both; display: none; \">\r\n            <ul>    \r\n            </ul>\r\n        </div>\r\n        <div class=\"basic\" style=\"display: block\">\r\n            <ul>\r\n            </ul>\r\n        </div>\r\n        <div class=\"score\" style=\"display: none\">\r\n        </div>\r\n        <div class=\"look\" style=\"display: none\">\r\n            <!-- will load static html from server -->\r\n        </div>\r\n        <div class=\"advice\" style=\"display: none\">\r\n            <ul>\r\n            </ul>\r\n        </div>\r\n        <div class=\"launch\" style=\"display: none\">\r\n            <!-- will load static html from server -->\r\n        </div>\r\n        <div class=\"testfooter\">            \r\n            <a class=\"button\" style=\"display: inline;\" href=\"#\" onclick=\"TestEditor.updateTest(this, false); return false;\">\r\n                Save</a>\r\n            <a class=\"button\" style=\"display: inline;\" href=\"#\" onclick=\"TestEditor.updateTest(this, true); return false;\">\r\n                Complete</a>\r\n        </div>";
TestEditor._testTitleTemplateTest = '\r\n        <h2 class="testheader">\r\n            <label for="test-title"></label>\r\n            <input class="test-title" name="test-title"\r\n                type="text" value="{0}" readonly="readonly" />\r\n            <input type="hidden" name="testid" value=""/>\r\n            <input type="hidden" name="status" value=""/>\r\n            <input type="hidden" name="totalpage" value=""/>\r\n            <input type="hidden" name="page" value=""/>\r\n        </h2>\r\n        <div class="questions" style="clear: both;">\r\n            <ul>    \r\n            </ul>\r\n        </div>\r\n        <div id="testfooter">\r\n            <!--<a class="button" style="display: inline;" href="#" onclick="TestEditor.finish(this); return false;">\r\n                Finish</a>-->\r\n        </div>';
TestEditor._menuTemplate = '                                \r\n                                <li><a href="#" onclick="TestEditor.addMultiChoice(this); return false;">add multiple choice question</a></li>\r\n                                <li><a href="#" onclick="TestEditor.addTextOrHtml(this); return false;">add Text or HTML</a></li>\r\n                                <li><a href="#" onclick="TestEditor.addPageBreak(this); return false;">add page break</a></li>\r\n                                <li><a href="#" onclick="TestEditor.addPicture(this); return false;">add picture or photo</a></li>\r\n                                <li><a href="#" onclick="TestEditor.deleteItem(this); return false;">delete this item</a></li>\r\n                                <li><a href="#" onclick="TestEditor.cancel(this); return false;">cancel</a></li>';
TestEditor._resultTemplateTest = "                \r\n                <li class=\"question testresult\">\r\n                    <div class=\"question-left\">\r\n                        <div style=''>YOUR TEST RESULT:</div>\r\n                    </div>\r\n                    <div class=\"question-main\" style='width:610px;'>\r\n                        <div style='padding:10px 0px 5px 0px;'>\r\n                            <div class=\"rstitle\" style='text-align:center; font-size:24px; color: #f83265;'></div>\r\n                            <div class=\"rssubtitle\" style='text-align:center; font-size:14px; margin-top: 5px;'></div>\r\n                        </div>\r\n                        <div class=\"rsdesc\" style='background-color:#fafafa; margin:5px 0px 10px 10px; padding:5px 10px 5px 10px; min-height:50px;'>\r\n                        </div>\r\n                        <div class='image-container'>\r\n                            <img style='margin: 0px auto; display: none; width=300px;' width='300' src='' onload=\"TestEditor.imageLoaded(this);\" />\r\n                        </div>\r\n                    </div>\r\n                    <div style='clear:both; padding-left:20px; margin-top:10px; color:#207cf2'>\r\n                        <a href='javascript:void();' onclick='TestEditor.showTestResultDetail(this); return false;' style='color:#207cf2!important; display:none;'></a>\r\n                    </div>\r\n                </li>";
TestEditor._questionTemplate = '                \r\n                <li class="question">\r\n                    <div class="question-left">\r\n                        <a href="#" onclick="TestEditor.actionClick(this); return false;" class="button">Actions</a>\r\n                        <div class="qmenu">\r\n                            <ul>\r\n                            </ul>\r\n                        </div>\r\n                    </div>\r\n                    <div class="question-main">\r\n                        <p class="question-question" onclick="TestEditor.editQuestion(this); return false;">\r\n                            Click here to edit your new question. Ok?</p>\r\n                        <div class="question-question-editor">\r\n                            <textarea></textarea>\r\n                            <br />\r\n                            <a class="button-done" href="#" onclick="TestEditor.endEditQuestion(this); return false;">Done</a>\r\n                        </div>\r\n                        <ul class="answers">\r\n                            <li style="clear: both;"><a href="#" onclick="TestEditor.addAnswer(this);  return false;" class="add-answer">add answer</a></li>\r\n                        </ul>\r\n                    </div>\r\n                    <div class="question-right">\r\n                        <a class="button" href="javascript:void();">Move</a>\r\n                    </div>\r\n                </li>';
TestEditor._questionTemplateTest = "                \r\n                <li class=\"question\" style=\"display: none;\">\r\n                    <div class=\"question-left\">2\r\n                    </div>\r\n                    <div class=\"question-main\">\r\n                        <p class=\"question-question\">\r\n                        </p>\r\n                        <ul class=\"answers\" style='padding-bottom:2px;'>\r\n                        </ul>\r\n                    </div>\r\n                    <div class=\"question-right\">\r\n                    </div>\r\n                </li>";
TestEditor._textTemplate = '                \r\n                <li class="question pagetext">\r\n                    <div class="question-left">\r\n                        <a href="#" onclick="TestEditor.actionClick(this); return false;" class="button">Actions</a>\r\n                        <div class="qmenu">\r\n                            <ul>\r\n                            </ul>\r\n                        </div>\r\n                    </div>\r\n                    <div class="question-main">\r\n                        <p class="question-question" onclick="TestEditor.editQuestion(this);  return false;">\r\n                            Edit your text or HTML here.</p>\r\n                        <div class="question-question-editor">\r\n                            <textarea></textarea>\r\n                            <br />\r\n                            <a class="button-done" href="#" onclick="TestEditor.endEditQuestion(this); return false;">Done</a>\r\n                        </div>\r\n                    </div>\r\n                    <div class="question-right">\r\n                        <a class="button" href="javascript:void();">Move</a>\r\n                    </div>\r\n                </li>';
TestEditor._textTemplateTest = '                \r\n                <li class="question pagetext" style="display: none;">\r\n                    <div class="question-left">\r\n                    </div>\r\n                    <div class="question-main">\r\n                        <p class="question-question" >\r\n                        </p>\r\n                    </div>\r\n                    <div class="question-right">\r\n                    </div>\r\n                </li>';
TestEditor._breakTemplate = '                \r\n                <li class="question pagetext">\r\n                    <div class="question-left">\r\n                        <a href="#" onclick="TestEditor.actionClick(this); return false;" class="button">Actions</a>\r\n                        <div class="qmenu">\r\n                            <ul>\r\n                            </ul>\r\n                        </div>\r\n                    </div>\r\n                    <div class="question-main">\r\n                        <p>\r\n                            <strong>Page Break </strong>(Submit Button Text:\r\n                            <input type="text" value="Next" />\r\n                        ) </p>\r\n                    </div>\r\n                    <div class="question-right">\r\n                        <a class="button" href="javascript:void();">Move</a>\r\n                    </div>\r\n                </li>';
TestEditor._breakTemplateTest = '                \r\n                <li class="question pagetext pagebreak" id="" style="display: none;">\r\n                    <div class="question-left">\r\n                    </div>\r\n                    <div class="question-main">\r\n                        <p class="question-question">\r\n                            <a class="button" style="display: inline;" href="javascript:void(0);" onclick="TestEditor.nextPage(this); return false;" >Next</a>\r\n                        </p>\r\n                    </div>\r\n                    <div class="question-right">\r\n                    </div>\r\n                </li>';
TestEditor._pictureTemplate = "                \r\n                <li class=\"question pagetext\" style=\"overflow:auto;\">\r\n                    <div class=\"question-left\">\r\n                        <a href=\"#\" onclick=\"TestEditor.actionClick(this); return false;\" class=\"button\">Actions</a>\r\n                        <div class=\"qmenu\">\r\n                            <ul>\r\n                            </ul>\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"question-main\">\r\n                        <div class=\"fileupload\"></div>\r\n                        <div class='imgname' style='display:none'><br /></div>\r\n                        <div class=\"image-container\">\r\n                            <img src=\"\" onload=\"TestEditor.imageLoaded(this);\" style='display:none; margin: 0px auto 0px auto;' />\r\n                        </div>\r\n                        <div class='clear'></div>\r\n                    </div>\r\n                    <div class=\"question-right\">\r\n                        <a class=\"button\" href=\"javascript:void();\">Move</a>\r\n                    </div>\r\n                </li>";
TestEditor._pictureTemplateTest = "                \r\n                <li class=\"question pagetext\" style=\"overflow:auto; display: none;\">\r\n                    <div class=\"question-left\">\r\n                    </div>\r\n                    <div class=\"question-main\">\r\n                        <div class=\"image-container\">\r\n                            <img src=\"\" onload=\"TestEditor.imageLoaded(this);\" style='margin: 0px auto 0px auto;' />\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"question-right\">\r\n                    </div>\r\n                </li>";
TestEditor._answerTemplate = '           <li style="clear: both;"><span class="answer-dot"><input type="radio" disabled="disabled" /></span>\r\n                <div class="answer-html">\r\n                    <div class="answer-content">\r\n                        <span class="answer-answer" onclick="TestEditor.editAnswer(this); return false;">{0}\r\n                            </span><a class="answer-remove" href="#" onclick="TestEditor.removeAnswer(this);  return false;">remove</a></div>\r\n                    <div class="answer-pure">\r\n                        <ul>\r\n                        </ul>\r\n                    </div>\r\n                </div>\r\n                <div class="answer-editor">\r\n                    <div class="answer-content">\r\n                        <textarea style="width: 380px; height: 50px;"></textarea>\r\n                        <br />\r\n                        <a class="button-done" href="#" onclick="TestEditor.endEditAnswer(this);  return false;">Done</a>\r\n                    </div>\r\n                    <div class="answer-pure">\r\n                        <ul>\r\n                        </ul>\r\n                    </div>\r\n                    <div class="clear">\r\n                    </div>\r\n                </div>\r\n            </li>';
TestEditor._answerTemplateTest = '           <li style="clear: both;"><span class="answer-dot"><input type="radio" /></span>\r\n                <div class="answer-html">\r\n                    <div class="answer-content">\r\n                        <span class="answer-answer">{0}\r\n                            </span></div>\r\n                    <div class="answer-pure">\r\n                        <ul>\r\n                        </ul>\r\n                    </div>\r\n                    <div class="clear">\r\n                    </div>\r\n                </div>\r\n            </li>';
TestEditor._pureItemTemplate = '                                  <li><span>{1}</span>\r\n                                        <label>\r\n                                            {0}&nbsp;\r\n                                        </label>\r\n                                    </li>';
TestEditor._pureEditorTemplate = '                                            <li><span>\r\n                                                <select>\r\n                                                    {1}\r\n                                                </select></span>\r\n                                                <label>\r\n                                                    {0}&nbsp;\r\n                                                </label>\r\n                                            </li>';
TestEditor._controls = 'bold italic underline | size | color highlight removeformat | source';
TestEditor._ansDefaulReg = new RegExp('answer\\s*\\d*\\s*-\\s*click\\s*to\\s*change', 'i');
TestEditor._textCoreReg = new RegExp('[^\\w\\d]', 'igm');
ItemTypeDef.multiChoice = 'MultiChoice';
ItemTypeDef.html = 'Html';
ItemTypeDef.photo = 'Photo';
ItemTypeDef.pageBreak = 'PageBreak';
ItemTypeDef.shortAnswer = 'ShortAnswer';
ItemTypeDef.multiSelect = 'MultiSelect';
Requirement.greaterThanValue = 'GreaterThanValue';
Requirement.lessThanValue = 'LessThanValue';
Requirement.greaterThanVar = 'GreaterThanVar';
Requirement.lessThanVar = 'LessThanVar';
Requirement.greatestVar = 'GreatestVar';
Requirement.leastVar = 'LeastVar';
Quizz.QuizzChart._hMin = -10;
})(jQuery);

//! This script was generated using Script# v0.7.4.0
