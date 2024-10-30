/*
 *
 *  Ajax Autocomplete for Prototype, version 1.0.4
 *  (c) 2010 Tomas Kirda
 *
 *  Ajax Autocomplete for Prototype is freely distributable under the terms of an MIT-style license.
 *  For details, see the web site: http://www.devbridge.com/projects/autocomplete/
 *
 *  (c) 2010 Transinsight:
 *  Changes done to enable callback requests.
 */

var Autocomplete = function(el, indexIfMore, options){
  this.el = el;
  this.id = this.el.identify();
  this.indexIfMore=indexIfMore;
  this.el.setAttribute('autocomplete','off');
  this.suggestions = [];
  this.termUris = [];
  this.data = [];
  this.badQueries = [];
  this.selectedIndex = -1;
  this.currentValue = this.el.value.toLowerCase();
  this.intervalId = 0;
  this.cachedResponse = [];
  this.instanceId = null;
  this.onChangeInterval = null;
  this.ignoreValueChange = false;
  this.serviceUrl = options.serviceUrl;
  this.ontologyBranch= options.ontologyBranch;
  this.suggestor=null;

  //on Firefox, we have to wait a little, other way it eats characters on fast typing
  this.getDelay=function() {	
//  	if (navigator.userAgent.indexOf("Firefox")!=-1) {
//  		//alert("Firefox");
//  		return 300;
//  	}
  	return 0;
  };
  
  this.options = {
    autoSubmit:false,
    minChars:1,
    maxHeight:300,
    deferRequestBy:this.getDelay(),
    width:0,
    container:null,
    relativeParent:document.body    
  };  
  if(options){ Object.extend(this.options, options); }
  this.relativeParent=this.options.relativeParent;
  if(Autocomplete.isDomLoaded){
    this.initialize();
  }else{
    Event.observe(document, 'dom:loaded', this.initialize.bind(this), false);
  }
};

Autocomplete.activeInstance=null;
Autocomplete.instances = [];
Autocomplete.isDomLoaded = false;

Autocomplete.getInstance = function(id){
  var instances = Autocomplete.instances;
  var i = instances.length;
  while(i--){ if(instances[i].id === id){ return instances[i]; }}
};

Autocomplete.highlight = function(value, re){
  return value.replace(re, function(match){ return '<strong>' + match + '<\/strong>'; });
};

Autocomplete.prototype = {

  killerFn: null,

  initialize: function() {
    var me = this;
    this.killerFn = function(e) {
      if (!$prototype(Event.element(e)).up('.autocomplete')) {
        me.killSuggestions();
        me.disableKillerFn();
      }
    } .bindAsEventListener(this);

    if (!this.options.width) { this.options.width = this.el.width; }

    var div = new Element('div', { style: 'position:absolute; z-index:10000; overflow: visible;' });
    div.update('<div class="autocomplete" id="Autocomplete_' + this.id + this.indexIfMore + '" style="display:none; overflow:visible;"></div>');
    
    this.options.container = $prototype(this.options.container);
           
    this.options.relativeParent.appendChild(div);

    this.mainContainerId = div.identify();
    this.container = $prototype('Autocomplete_' + this.id+this.indexIfMore);
    this.fixPosition();

    Event.observe(this.el, window.opera ? 'keypress':'keydown', this.onKeyPress.bind(this));
    Event.observe(this.el, 'keyup', this.onKeyUp.bind(this));
    Event.observe(this.el, 'blur', this.enableKillerFn.bind(this));
    Event.observe(this.el, 'focus', this.fixPosition.bind(this));
    this.container.setStyle({ maxHeight: this.options.maxHeight + 'px' });
    this.instanceId = Autocomplete.instances.push(this) - 1;
  },

  fixPosition: function(element) {
    var offset = jQuery(this.el).position();//Element.cumulativeOffset(this.el);
    //var wrapper = this.relativeParent;
    //var wrapperOffset=Element.cumulativeOffset(wrapper);
    
    var w=window,d=document,e=d.documentElement,g=document.body,x=w.innerWidth&&e.clientWidth&&g.clientWidth,y=w.innerHeight&&e.clientHeight&&g.clientHeight;
    var wi=$prototype(this.mainContainerId).getWidth();  // start it more left if it is longer than the available place on the right in the browser.
    if (offset.left+wi>x) {
    	offset.left=x-wi-1;
    }      
    var padding=this.el.getStyle('padding-bottom');
    var bplus=0;
    if (padding.length>2 && padding.substr(padding.length - 2) == "px") {
    	padding=padding.substr(0,padding.length-2);
    	if (padding<12)
    		bplus=12-padding;
    }
    
    $prototype(this.mainContainerId).setStyle({ top: (offset.top + this.el.getHeight() + bplus) + 'px', left: (offset.left) + 'px' });
    
    if (this.suggestor==null && element) {
    	var jsonConf = new com.transinsight.JsonConf(this.serviceUrl,null);
        this.suggestor = new com.transinsight.yggdrasil.jsonapi.JsonQuerySuggestComponent(jsonConf);
        try {
      	  this.suggestor.setOntologyBranchLabel(this.ontologyBranch, function() {});
        }
      	catch (e) {
      		console.log('error setting the ontology label');
      		return;
      	}
    }
  },

  enableKillerFn: function() {
    Event.observe(document.body, 'click', this.killerFn);
  },

  disableKillerFn: function() {
    Event.stopObserving(document.body, 'click', this.killerFn);
  },

  killSuggestions: function() {
    this.stopKillSuggestions();
    this.intervalId = window.setInterval(function() { this.hide(); this.stopKillSuggestions(); } .bind(this), 0);
  },

  stopKillSuggestions: function() {
    window.clearInterval(this.intervalId);
  },

  onKeyPress: function(e) {
    if (!this.enabled) { return; }
    // return will exit the function
    // and event will not fire
    switch (e.keyCode) {
      case Event.KEY_ESC:
        this.el.value = this.currentValue;
        this.hide();
        break;
      case Event.KEY_TAB:
      case Event.KEY_RETURN:
        if (this.selectedIndex === -1) {
          this.hide();
          return;
        }
        this.select(this.selectedIndex);
        if (e.keyCode === Event.KEY_TAB) { return; }
        break;
      case Event.KEY_UP:
        this.moveUp();
        break;
      case Event.KEY_DOWN:
        this.moveDown();
        break;
      default:
        return;
    }
    Event.stop(e);
  },

  onKeyUp: function(e) {
    switch (e.keyCode) {
      case Event.KEY_UP:
      case Event.KEY_DOWN:
        return;
    }
    clearInterval(this.onChangeInterval);
    if (this.currentValue !== this.el.value.toLowerCase()) {
      if (this.options.deferRequestBy > 0) {
        // Defer lookup in case when value changes very quickly:
        this.onChangeInterval = setInterval((function() {
          this.onValueChange();
        }).bind(this), this.options.deferRequestBy);
      } else {
        this.onValueChange();
      }
    }
  },

  onValueChange: function() {
    clearInterval(this.onChangeInterval);
    this.currentValue = this.el.value.toLowerCase();
    this.selectedIndex = -1;
    if (this.ignoreValueChange) {
      this.ignoreValueChange = false;
      return; 
    }
    
    if (this.currentValue === '' || this.currentValue.length < this.options.minChars) {
      this.hide();
    } else {
      this.getSuggestions();      
    }
  },
  
//  onfocus: function() {	  
//  },

  getSuggestions: function() {
    var cr = this.cachedResponse[this.currentValue];
    
    // alert(this.currentValue+' - '+(cr==true));
    
    if (cr && Object.isArray(cr.suggestions)) {
      this.suggestions = cr.suggestions;
      this.data = cr.data;
      this.termUris = cr.termUris;
      this.suggest();
    } else if (!this.isBadQuery(this.currentValue)) {
    	// alert('new request - '+this.currentValue)
        this.makeRequest(this.currentValue);
    }
  },
  
  requestCallback: function(obj) {
	  var self= Autocomplete.activeInstance;
	  var suggestions = [];
      var newtexts= [];
      var uris= [];
      for(var i=0; i<obj.result.length; i++) {
    	  uris[i]=obj.result[i].term.uri;
    	  
    	  suggestions[i]=obj.result[i].finding;
    	  if (suggestions[i]==null)
    		  suggestions[i]='';
    	  newtexts[i]=suggestions[i];
    	  
    	  //if (obj.result[i].nonAmbiguousParent) {
//    		  suggestions[i]=suggestions[i]+' {'+obj.result[i].nonAmbiguousParent.label+'}';
    	  //}
    	  
    	  var syncount=obj.result[i].synonyms.length;    	  
    	  if (syncount>3)
    		  syncount=3;
    	  for (var j=0; j<syncount; j++) {
    		  add="";
    		  if (j==0) 
    			  add=' <span>&nbsp;&nbsp;&nbsp;';
    		  else add=", ";
    		  add=add+obj.result[i].synonyms[j];
    		  if (j==syncount-1) {
    			  if (obj.result[i].synonyms.length>syncount)
    				  add=add+", ...";
    			  add=add+"";
    			  add=add+"</span>";
    		  }
    		  suggestions[i]=suggestions[i]+add;
    	  }
    	  
    	  if (newtexts[i].indexOf(" ")>-1)
    		  newtexts[i]='"'+newtexts[i]+'"';    	      	  
      }
      
      var response = {
    		  query : self.currentValue,
    		  suggestions : suggestions,
    		  data : newtexts,
    		  termUris: uris
      };
      
      self.cachedResponse[response.query] = response;
      if (response.suggestions.length === 0) { self.badQueries.push(response.query); }
      if (response.query === self.currentValue) {
    	self.suggestions = response.suggestions;
    	self.data = response.data;
    	self.termUris = response.termUris;
    	self.suggest();
      }
  },

  makeRequest: function(queryString) {	       
      try {
    	  if (this.suggestor!=null) {
    		  Autocomplete.activeInstance=this;
    		  var obj = this.suggestor.suggest(queryString, queryString.length, this.requestCallback);
    	  }
    	  else {
    		  console.log('suggestor is not initialized.');
    		  return;
    	  }
      }
      catch (e) {  	  
    	  console.log('There was an error getting the suggestions, session expired ? Refreshing the page should help.');    	  
    	  return;
      }
  }, 
  
  isBadQuery: function(q) {
    var i = this.badQueries.length;
    while (i--) {
      if (q.indexOf(this.badQueries[i]) === 0) { return true; }
    }
    return false;
  },

  hide: function() {
    this.enabled = false;
    this.selectedIndex = -1;
    this.container.hide();
  },

  suggest: function() {
    if (this.suggestions.length === 0) {
      this.hide();
      return;
    }
    var content = [];
    var re = new RegExp('\\b' + this.currentValue.match(/\w+/g).join('|\\b'), 'gi');
    this.suggestions.each(function(value, i) {
      content.push((this.selectedIndex === i ? '<div class="selected"' : '<div'), ' onclick="Autocomplete.instances[', this.instanceId, '].select(', i, ');" onmouseover="Autocomplete.instances[', this.instanceId, '].activate(', i, ');">', Autocomplete.highlight(value, re), '</div>');
    } .bind(this));
    
    this.enabled = true;
    this.container.update(content.join('')).show();
    this.fixPosition();
  },

  processResponse: function(xhr) {	  
	  // alert('response - '+xhr.responseText);
	  
    var response;
    try {
      response = xhr.responseText.evalJSON();
      if (!Object.isArray(response.data)) { response.data = []; }
    } catch (err) {
    	return;
    }
    
    // alert('responsecount - '+response.suggestions.length);
    
    this.cachedResponse[response.query] = response;
    if (response.suggestions.length === 0) { this.badQueries.push(response.query); }
    if (response.query === this.currentValue) {
      this.suggestions = response.suggestions;
      this.data = response.data;
      this.termUris = response.termUris;
      this.suggest();
    }
  },

  activate: function(index) {
    var divs = this.container.childNodes;
    var activeItem=null;
    // Clear previous selection:
    if (this.selectedIndex !== -1 && divs.length > this.selectedIndex) {
      divs[this.selectedIndex].className = '';
    }
    this.selectedIndex = index;
    if (this.selectedIndex !== -1 && divs.length > this.selectedIndex) {
      activeItem = divs[this.selectedIndex];
      activeItem.className = 'selected';
    }
    return activeItem;
  },

  deactivate: function(div, index) {
    div.className = '';
    if (this.selectedIndex === index) { this.selectedIndex = -1; }
  },

  select: function(i) {
    var selectedValue = this.data[i];
    if (selectedValue) {
      this.el.value = selectedValue;
      if (this.options.autoSubmit && this.el.form) {
        this.el.form.submit();
      }
      this.ignoreValueChange = true;
      this.hide();
      this.onSelect(i);
    }
  },

  moveUp: function() {
    if (this.selectedIndex === -1) { return; }
    if (this.selectedIndex === 0) {
      this.container.childNodes[0].className = '';
      this.selectedIndex = -1;
      this.el.value = this.currentValue;
      return;
    }
    this.adjustScroll(this.selectedIndex - 1);
  },

  moveDown: function() {
    if (this.selectedIndex === (this.suggestions.length - 1)) { return; }
    this.adjustScroll(this.selectedIndex + 1);
  },

  adjustScroll: function(i) {
    var container = this.container;
    var activeItem = this.activate(i);
    var offsetTop = activeItem.offsetTop;
    var upperBound = container.scrollTop;
    var lowerBound = upperBound + this.options.maxHeight - 25;
    if (offsetTop < upperBound) {
      container.scrollTop = offsetTop;
    } else if (offsetTop > lowerBound) {
      container.scrollTop = offsetTop - this.options.maxHeight + 25;
    }
    this.el.value = this.data[i];
  },

  onSelect: function(i) {
    (this.options.onSelect || Prototype.emptyFunction)(this.suggestions[i], this.data[i], this.termUris[i]);
  }

};

Event.observe(document, 'dom:loaded', function(){ Autocomplete.isDomLoaded = true; }, false);
