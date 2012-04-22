Price.GroupVM = function(config) {
    var self = this;
    self.content = ko.observableArray([]);

    self.choose = function (group) {
        console.log(group.title);
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
