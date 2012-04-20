var PriceVM = function(config) {
    var self = this;
    self.items = new PriceItemsVM(config);
    //self.priceGroupVM = new priceGroupVM(config);
};

ko.applyBindings(new PriceVM(App.Config));