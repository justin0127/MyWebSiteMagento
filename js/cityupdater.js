DistrictUpdater = Class.create();
DistrictUpdater.prototype = {

    initialize: function (countryEl, regionTextEl, regionSelectEl, regions, disableAction)
    {
        this.countryEl = $(countryEl);
        this.regionTextEl = $(regionTextEl);
        this.regionSelectEl = $(regionSelectEl);
        this.regions = regions;

        this.disableAction = (typeof disableAction=='undefined') ? 'hide' : disableAction;

        if (this.regionSelectEl.options.length<=1) {
            this.update();
        }

        Event.observe(this.countryEl, 'change', this.update.bind(this));
    },

    update: function()
    {
        if (this.regions[this.countryEl.value]) {
            var i, option, region, def;

            if (this.regionTextEl) {
                def = this.regionTextEl.value.toLowerCase();
                this.regionTextEl.value = '';
            }
            if (!def) {
                def = this.regionSelectEl.getAttribute('defaultValue');
            }

            this.regionSelectEl.options.length = 1;
            this.districts = this.regions[this.countryEl.value];
            
           for (var i=0, len=this.districts.length; i<len; ++i ) {
                option = document.createElement('OPTION');
                option.value = this.districts[i];
                option.text = this.districts[i];

                if (this.regionSelectEl.options.add) {
                    this.regionSelectEl.options.add(option);
                } else {
                    this.regionSelectEl.appendChild(option);
                }
           }



            if (this.disableAction=='hide') {
                if (this.regionTextEl) {
                    this.regionTextEl.style.display = 'none';
                }

                this.regionSelectEl.style.display = '';
            } else if (this.disableAction=='disable') {
                if (this.regionTextEl) {
                    this.regionTextEl.disabled = true;
                }
                this.regionSelectEl.disabled = false;
            }
            this.setMarkDisplay(this.regionSelectEl, true);
        } else {
            if (this.disableAction=='hide') {
                if (this.regionTextEl) {
                    this.regionTextEl.style.display = '';
                }
                this.regionSelectEl.style.display = 'none';
                Validation.reset(this.regionSelectEl);
            } else if (this.disableAction=='disable') {
                if (this.regionTextEl) {
                    this.regionTextEl.disabled = false;
                }
                this.regionSelectEl.disabled = true;
            } else if (this.disableAction=='nullify') {
                this.regionSelectEl.options.length = 1;
                this.regionSelectEl.value = '';
                this.regionSelectEl.selectedIndex = 0;
                this.lastCountryId = '';
            }
            this.setMarkDisplay(this.regionSelectEl, false);
        }
       
    },

    setMarkDisplay: function(elem, display){
        elem = $(elem);
        var labelElement = elem.up(1).down('label > span.required') || 
                           elem.up(2).down('label > span.required') ||
                           elem.up(1).down('label.required > em') ||
                           elem.up(2).down('label.required > em');
        if(labelElement) {
            display ? labelElement.show() : labelElement.hide();
        }
    }
}
