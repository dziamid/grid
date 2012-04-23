Price.GroupVM = function(parent, config) {
    var self = this;
    /**
     * An array of all groups
     *
     */
    self.content = ko.observableArray([]);

    /**
     * Active (selected) group
     *
     */
    self.active = ko.observable();

    self.isActive = function(group) {
        return self.active() && self.active().id == group.id;
    };

    self.select = function (group) {
        if (self.isActive(group)) {
            return;
        }
        self.active(group);
    };

    self.preload = function () {
        $.ajax(config.url.group, {
            type: 'get',
            dataType: 'json',
            success: function (data) {
                for (var i = 0; i < data.length; i++) {
                    var item = new Price.Group(data[i]);
                    self.content.push(item);
                }
            }
        });
    };

};
