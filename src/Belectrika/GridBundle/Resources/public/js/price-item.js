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