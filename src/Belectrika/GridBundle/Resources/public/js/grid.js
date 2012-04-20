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
    self.edit = function () {
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

PriceItem.prototype.toJSON = function () {

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
        $.get(config.url, function (data) {
            for (var i = 0; i < data.length; i++) {
                var item = new PriceItem(data[i]);
                self.PriceItems.push(item);
            }
        }, 'json');
    };

    self.persistItem = function (item) {
        ko.utils.arrayForEach(item.editable, function (name) {
            item[name].commit();
        });
        item.inViewMode(true);

        item.isNew() ? self.persistCreateItem(item) : self.persistUpdateItem(item);
    };

    self.findItem = function (id) {
        return ko.utils.arrayFirst(self.PriceItems(), function (item) {
            return id == item.id();
        });
    };

    self.mapItem = function (item, data) {
        item.id(data.id);
        item.title(data.title);
        item.price(data.price);
        item.amount(data.amount);
        //hmm
        ko.utils.arrayForEach(item.editable, function (name) {
            item[name].commit();
        });
    };

    self.persistCreateItem = function (item) {
        $.ajax(config.url, {
            data: ko.toJSON({'item': item, 'pageId': config.pageId}),
            type: 'post',
            dataType: 'json',
            success: function (data) {
                //TODO: handle errors, display some kind of flash
                //TODO: use mapping plugin?
                if (data.errors) {
                    item.isValid(false);
                }
                if (data.id) {
                    item.isValid(true);
                    self.mapItem(item, data);
                }
            }
        });
    };

    self.persistUpdateItem = function (item) {

        $.ajax(config.url, {
            data: ko.toJSON({'item': item, 'pageId': config.pageId}),
            type: 'put',
            dataType: 'json',
            success: function (data) {
                //TODO: handle errors, display some kind of flash
                //TODO: use mapping plugin?
                if (data.errors) {
                    item.isValid(false);
                }
                if (data.id) {
                    item.isValid(true);
                    self.mapItem(item, data);
                }
            }
        });
    };


    self.templateName = function (item) {
        return item.inViewMode() ? 'viewItemTmpl' : 'editItemTmpl';
    };

    self.createItem = function (data) {
        var item = new PriceItem();
        self.mapItem(item, data);
        item.inViewMode(true);
        self.PriceItems.push(item);
    };
    self.showCreateItemForm = function () {
        var item = new PriceItem();
        item.inViewMode(false);
        self.PriceItems.push(item);
    };

    self.deleteItem = function (item) {
        self.PriceItems.remove(item);
    };
    self.showDeleteItemForm = function (item) {
        if (confirm('Are you sure?')) {
            self.deleteItem(item);
            $.ajax(config.url, {
                data: ko.toJSON({'item': item, 'pageId': config.pageId}),
                type: 'DELETE',
                dataType: 'json',
                success: function (data) {
                    //console.log('item removed');
                }

            });
        }

    };

    self.pollChanges = function () {
        $.ajax(config.url_poll, {
            type: 'GET',
            data: {'pageId': config.pageId},
            dataType: 'json',
            success: function (data) {
                //console.log(data);
                for (var i = 0; i < data.length; i++) {
                    var changelog = data[i];
                    var item = self.findItem(changelog.itemId);
                    var itemData = changelog.item || false;
                    //if its update or delete, need to check if item still exists
                    //it may have been already deleted
                    if (changelog.type != 1 && !item) {
                        continue;
                    }
                    if (changelog.type == 1) {
                        self.createItem(itemData);
                    } else if (changelog.type == 2) {
                        self.mapItem(item, itemData);
                    } else if (changelog.type == 3) {
                        self.deleteItem(item);
                    }
                }
            }
        });
    };

    self.pollEnabled = ko.observable(true);

    self.preload();
    setInterval(function () {
        return self.pollEnabled() && self.pollChanges();
    }, 10000);
    //start polling changes

}

ko.applyBindings(new PriceViewModel(App.Config));