Price.PageVM = function(config) {
    var self = this;
    self.items = new Price.ItemVM(self, config);
    self.groups = new Price.GroupVM(self, config);

    self.activeGroup = self.groups.active;
    self.preload = function () {
        self.groups.preload();
        self.items.preload();
        setInterval(function () {
            return self.items.pollEnabled() && self.items.pollChanges();
        }, 10000);
    };

};

Price.page = new Price.PageVM(Price.config);
ko.applyBindings(Price.page);
Price.page.preload();
