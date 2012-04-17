var PriceItem = function (data) {
    var self = this;
    self.protectedObservables = ['title', 'price', 'amount'];
    ko.utils.arrayForEach(self.protectedObservables, function (name) {
        self[name] = ko.protectedObservable(data[name]);
    });
    if (data.id === undefined) {
        data.id = ko.generateId();
    }

    self.id = ko.observable(data.id);

    self.inViewMode = ko.observable(data.inViewMode || true);
    self.edit = function() {
        self.inViewMode(false);
    };

    self.commit = function () {
        ko.utils.arrayForEach(self.protectedObservables, function (name) {
            self[name].commit();
        });
        self.inViewMode(true);
    };
    self.reset = function () {
        ko.utils.arrayForEach(self.protectedObservables, function (name) {
            self[name].reset();
        });
        self.inViewMode(true);
    }
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