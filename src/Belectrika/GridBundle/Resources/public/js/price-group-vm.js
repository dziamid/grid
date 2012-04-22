Price.GroupVM = function(config) {
    var self = this;
    self.content = ko.observableArray([
        new Price.Group({title: 'Group 1'}),
        new Price.Group({title: 'Group 2'}),
        new Price.Group({title: 'Group 3'})
    ]);

    self.choose = function (group) {
        console.log(group.title);
    };

    self.preload = function () {
        $.get(config.url.group, function (data) {

        }, 'json');
    };

};
