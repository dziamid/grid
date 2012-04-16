var PriceItem = function (data) {
    var self = this;
    self.title = data.title;
    self.price = ko.observable(data.price);
    self.amount = ko.observable(data.amount);

};

function MenuViewModel(config) {
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

ko.applyBindings(new MenuViewModel(App.Config));