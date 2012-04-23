Price.Group = function (data) {
    data = data || {};
    var self = this;

    if (data.id === undefined) {
        data.id = ko.generateId();
    }
    self.id = ko.observable(data.id);
    self.title = data.title;

};