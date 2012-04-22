Price.PageVM = function(config) {
    var self = this;
    self.items = new Price.ItemVM(config);
    self.groups = new Price.GroupVM(config);

    self.preload = function () {
        self.items.preload();
        self.groups.preload();
        setInterval(function () {
            return self.items.pollEnabled() && self.items.pollChanges();
        }, 10000);
    };

};

Price.page = new Price.PageVM(Price.config);
ko.applyBindings(Price.page);
Price.page.preload();
