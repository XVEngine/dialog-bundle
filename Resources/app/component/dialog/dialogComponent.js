(function(namespace, app, globals) {


    namespace.dialogComponent = app.newClass({
        extend: function () {
            return app.components.abstractComponent;
        }
    });
    
   
    /**
     * 
     * @returns {$}
     */
    namespace.dialogComponent.prototype.getTemplate = function() {
        var tmplString = app.utils.getString(function() {
            //@formatter:off
            /**<string>
                    <xv-dialog class="event-insert event-resize event-esc" tabindex="-1">
                        <div class="closable">
                            <div class="closable">
                                <div class="window">
                                    <a href="#close">
                                        <i class="icon icon-close"></i>
                                    </a>
                                    <header></header>
                                    <section></section>
                                    <footer></footer>
                                </div>
                            </div>
                        </div>
                    </xv-dialog>
             </string>*/
            //@formatter:on
        });
        return $(tmplString);
    };


    /**
     * 
     * @returns {object}
     */
    namespace.dialogComponent.prototype.getDefaultParams = function() {
        return {
            width: "60vw",
            height: "auto",
            headerComponent : null,
            contentComponent : null,
            footerComponent : null,
            manualClose : false
        };
    };

  
    /**
     * 
     * @returns {undefined}
     */
    namespace.dialogComponent.prototype.init = function() {
        this.$dialogWindow = this.$element.find(">div > div > div");
        this.$close = this.$dialogWindow.find("> a");
        
        this.setWidth(this.params.width);
        this.setHeight(this.params.height);
        
        this.$header =  this.$dialogWindow.find("> header");
        this.$content =  this.$dialogWindow.find("> section");
        this.$footer =  this.$dialogWindow.find("> footer");
        
        this.setManualClose(this.params.manualClose);
        this.params.headerComponent && this.setHeaderComponent(this.params.headerComponent);
        this.params.contentComponent && this.setContentComponent(this.params.contentComponent);
        this.params.footerComponent && this.setFooterComponent(this.params.footerComponent);
        this.createScrollRule();
        this.bindEvents();
        var self = this;
        delete Hammer.defaults.cssProps.userSelect;
        //if($('html').is('.touch')) {
        //    this.$element.hammer().bind('panup', function () {
        //        if (self.$element.scrollTop() + self.$element.height() >= self.$dialogWindow.height()) {
        //            self.close();
        //
        //        }
        //    });
        //    this.$element.data('hammer').get('pan').set({ threshold: 30 });
        //}

       this.onScroll();


        this.show(); //this should be manual triggered!
    };

    namespace.dialogComponent.prototype.onScroll = function() {
        var started = false;
        var self = this;
        var ended = false;
        touchStart = null;
        this.$element.on("touchstart", function(e){
            touchStart = e.originalEvent.targetTouches[0];
            started = (self.$element.scrollTop() + self.$element.height() >= self.$dialogWindow.height()) ;
            self.$dialogWindow.removeClass("animate");
        });

        self.$element.on("touchmove touchend", function(e) {
            if (!started) {
                return;
            }
            if (e.type == "touchend") {
                started = false;
                self.$dialogWindow.addClass("animate");
                if(ended) {
                    self.close();
                } else {
                    self.$dialogWindow.css("transform", "translate3d(0, 0, 0)");
                    self.$dialogWindow.css("opacity", 1);
                }
            } else {
                ended = false;
                var touch = e.originalEvent.targetTouches[0];
                var y = touchStart.clientY - touch.clientY;
                if (y < 0) {
                    y = 0;
                }
                var ratio = y/($(window).height() * 0.4);
                self.$dialogWindow.css("transform", "perspective(1000px) scale("+(1-ratio*0.08)+") rotate3d(1, 0, 0, " + (ratio * 10) + "deg) translate3d(0,-" + y + "px,0)");
                self.$dialogWindow.css("opacity", Math.max(0, 1-ratio*0.3));
                if (ratio > 1) {
                    ended = true;
                }
            }
        });
    };

    namespace.dialogComponent.prototype.createScrollRule = function () {
        //@todo : this should be do better
        app.service.ui.css.addRule("html.dialog-displayed .xv-site-layout header.xv-header > .fixed", {
            "padding-right": app.utils.getScrollBarWidth()+"px"
        });
    };


    namespace.dialogComponent.prototype.setManualClose = function(value) {
        this._manualClose = !!value;
        return this;
    };
    
    namespace.dialogComponent.prototype.setWidth = function(value) {
        this.$dialogWindow.width(value);
        return this;
    };
    
    namespace.dialogComponent.prototype.setHeight = function(value) {
        this.$dialogWindow.height(value);
        return this;
    };
    
    
    namespace.dialogComponent.prototype.show = function() {
        $("html").addClass("dialog-displayed");
        app.service.ui.tips && app.service.ui.tips.hideAll();

        var self = this;

        app.utils.requestAnimFrame(function(){
            self.$element.addClass("show-animation");
        });
        return this;
    };
    
  
    
    
    
    namespace.dialogComponent.prototype.close = function() { 
        var self = this;
        this.$element.addClass("closing");
        var $application = app.utils.getApplication();
        
        if($application.find("xv-dialog:not(.closing)").length == 0){
            $("html").removeClass("dialog-displayed");
        }
        
        
        var timeout = app.utils.getTranistionDuration(this.$element) + app.utils.getTranistionDelay(this.$element);
        self.$element.addClass("remove-animation");
        setTimeout(function(){
            self.$element.remove();
        }, timeout);
  
        return this;
    };
    
    
    namespace.dialogComponent.prototype.setHeaderComponent = function(component) {
        var self = this;
        return app.utils.buildComponent(component).then(function ($html) {
            self.$header.html($html);
            return true;
        });
    };
    
    namespace.dialogComponent.prototype.setContentComponent = function(component) {
        var self = this;
        return app.utils.buildComponent(component).then(function ($html) {
            self.$content.html($html);
            return true;
        });
    };
    
    namespace.dialogComponent.prototype.setFooterComponent = function(component) {
        var self = this;
        return app.utils.buildComponent(component).then(function ($html) {
            self.$footer.html($html);
            return true;
        });
    };
    
    
    
    
    namespace.dialogComponent.prototype.bindEvents = function() {
        var self = this;
        
        this.$close.on("click touchend", function(e){
            self.trigger("onClose");
            if(!self._manualClose){
                self.close();
            }
            
            return false;
        });
 
        
        this.$element.on("click", function(e){
            if($(e.target).is(".closable")){
                self.$close.trigger("click");
                return false;
            }
        });
        
        this.$element.on("event-esc", function () {
            if($("xv-shared-place > xv-dialog:last").is(self.$element)){ //@todo: here should be better solution
                self.$close.trigger("click");
            }
        });

        this.$element.on("event-resize", function () {
            self.$element[$(this).width() < 700 ? 'addClass' : 'removeClass']("slim");
        });

        this.$element.on('event-insert', function(){
            self.$element.focus();
            setTimeout(function(){
                self.$element.focus();
            }, 100);
        });
    };
    
    
   
    
    return namespace.dialogComponent;
})(__ARGUMENT_LIST__);