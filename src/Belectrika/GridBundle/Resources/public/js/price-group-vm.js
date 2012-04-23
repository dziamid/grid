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

    self.find = function (id) {
        return ko.utils.arrayFirst(self.content(), function (g) {
            return g.id() == id;
        });
    };

    self.isActive = function(group) {
        return self.active() && self.active().id == group.id;
    };

    self.select = function (group) {
        if (self.isActive(group)) {
            return;
        }
        self.active(group);
        $.cookie('selected-group-id', group.id(), { expires: 30*12 });
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
                self.onPreload();
            }
        });
    };

    self.onPreload = function () {
        var groupId = $.cookie('selected-group-id');
        var group = groupId && self.find(groupId);
        if (group) {
            self.select(group);
        }
    };


};
