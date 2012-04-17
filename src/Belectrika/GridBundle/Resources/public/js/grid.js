var PriceItem = function (data) {
    data = data || {};
    var self = this;
    self.editable = ['title', 'price', 'amount'];
    self.serializable = Array.concat(['id'], self.editable);
    ko.utils.arrayForEach(self.editable, function (name) {
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

    self.reset = function () {
        ko.utils.arrayForEach(self.editable, function (name) {
            self[name].reset();
        });
        self.inViewMode(true);
    };

    self.label = ko.computed(function () {
        return self.title() + ' (' + self.id() + ')';
    });

    self.isNew = ko.computed(function () {
        return self.id() < 0;
    });

    self.isValid = ko.observable(true);

};

PriceItem.prototype.toJSON = function() {

    var object = ko.toJS(this); //easy way to get a clean copy

    var selectedProperties = {};

    ko.utils.arrayForEach(object.serializable, function (name) {
        selectedProperties[name] = object[name];
    });

    return selectedProperties; //return the copy to be serialized

};

function PriceViewModel(config) {
    var self = this;

    self.PriceItems = ko.observableArray([]);

    self.preload = function () {
        $.getJSON(config.url, function (data) {
            for (var i=0; i<data.length; i++) {
                var item = new PriceItem(data[i]);
                self.PriceItems.push(item);
            }
        });
    };

    self.saveItem = function (item) {
        ko.utils.arrayForEach(item.editable, function (name) {
            item[name].commit();
        });
        item.inViewMode(true);

        $.post(config.url, ko.toJSON(item), function (data) {
            //TODO: handle errors, display some kind of flash
            //TODO: use mapping plugin?
            if (data.errors) {
                item.isValid(false);
            }
            if (data.id) {
                item.isValid(true);
                item.id(data.id);
                item.title(data.title);
                item.price(data.price);
                item.amount(data.amount);
            }

        }, 'json');
    };

    self.templateName = function (item) {
        return item.inViewMode() ? 'viewItemTmpl':'editItemTmpl';
    };

    self.createNewItem = function () {
        var item = new PriceItem();
        item.inViewMode(false);
        self.PriceItems.push(item);
    };
    self.deleteItem = function (item) {
        if (confirm('Are you sure?')) {
            self.PriceItems.remove(item);
            $.ajax(config.url, {
                data: ko.toJSON(item),
                type: 'DELETE',
                dataType: 'json',
                success: function (data) {
                    console.log('item removed');
                }

            });
        }

    };

    self.preload();
}

ko.applyBindings(new PriceViewModel(App.Config));