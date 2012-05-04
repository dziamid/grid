Price.Group = function (data, level) {
    level = level || 0;
    data = data || {};
    var self = this;

    if (data.id === undefined) {
        data.id = ko.generateId();
    }
    self.id = ko.observable(data.id);
    self.title = data.title;
    self.level = ko.observable(level);

    self.children = ko.observableArray([]);
    for (var i = 0; i < data.children.length; i++) {
        var group = new Price.Group(data.children[i], 1);
        self.children.push(group);
    }

};