Price.ItemVM = function (parent, config) {
    var self = this;

    /**
     * All items
     */
    self.content = ko.observableArray([]);

    /**
     * Items of active group
     *
     */
    self.active = ko.computed(function () {
        //console.log('items.active called');
        return ko.utils.arrayFilter(self.content(), function (i) {
            return parent.activeGroup() && i.groupId() == parent.activeGroup().id();
        });
    });

    /**
     * Preload items for given group
     *
     * @param groupId
     */
    self.preload = function (groupId) {

        $.ajax(config.url.item, {
            type: 'get',
            dataType: 'json',
            data: {groupId: groupId},
            success: function (data) {
                for (var i = 0; i < data.length; i++) {
                    var itemData = data[i];
                    var item = self.findItem(itemData['id']);
                    if (!item) {
                        item = new Price.Item(itemData);
                        self.content.push(item);
                    }
                }
            }
        });
    };

    self.persistItem = function (item) {
        ko.utils.arrayForEach(item.editable, function (name) {
            item[name].commit();
        });
        item.inViewMode(true);

        item.isNew() ? self.persistCreateItem(item) : self.persistUpdateItem(item);
    };

    self.findItem = function (id) {
        return ko.utils.arrayFirst(self.content(), function (item) {
            return id == item.id();
        });
    };

    self.mapItem = function (item, data) {
        //TODO: just loop through data and set on item
        item.id(data.id);
        item.title(data.title);
        item.price(data.price);
        item.amount(data.amount);
        item.groupId(data.groupId);

        //hmm
        ko.utils.arrayForEach(item.editable, function (name) {
            item[name].commit();
        });
    };

    self.persistCreateItem = function (item) {
        $.ajax(config.url.item, {
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

        $.ajax(config.url.item, {
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
        var item = new Price.Item();
        self.mapItem(item, data);
        item.inViewMode(true);
        self.content.push(item);
    };
    self.showCreateItemForm = function () {
        var item = new Price.Item();
        item.group(parent.activeGroup());
        item.inViewMode(false);
        self.content.push(item);
    };

    self.deleteItem = function (item) {
        self.content.remove(item);
    };
    self.showDeleteItemForm = function (item) {
        if (confirm('Are you sure?')) {
            self.deleteItem(item);
            $.ajax(config.url.item, {
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
        $.ajax(config.url.changelog, {
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

}

