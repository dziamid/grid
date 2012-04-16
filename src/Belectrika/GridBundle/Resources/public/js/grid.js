var PriceItem = function (data) {
    var self = this;
    if (data.id === undefined) {
        data.id = ko.generateId();
    }

    self.id = ko.observable(data.id);
    self.title = ko.observable(data.title);
    self.price = ko.observable(data.price);
    self.amount = ko.observable(data.amount);
    self.inViewMode = ko.observable(data.inViewMode || true);
    self.edit = function() {
        self.inViewMode(false);
    };
    self.save = function() {
        self.inViewMode(true);
    };
};

function PriceViewModel(config) {
    var self = this;

    self.PriceItems = ko.observableArray([
        new PriceItem({title: 'Item title', price: 10500, amount: 10})
    ]);

    self.preload = function () {
        $.getJSON(config.url, function (data) {
            for (var i=0; i<data.length; i++) {
                var item = new PriceItem(data[i]);
                self.PriceItems.push(item);
            }
        });
    };

    self.preload();
}

ko.applyBindings(new PriceViewModel(App.Config));