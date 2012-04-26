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
     * An array of items that are currently in edit mode
     *
     */
    self.pending = ko.observableArray([]);

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
                    var item = self.find(itemData['id']);
                    if (!item) {
                        item = new Price.Item(itemData);
                        self.content.push(item);
                    }
                }
            }
        });
    };

    self.persist = function (item) {
        ko.utils.arrayForEach(item.editable, function (name) {
            item[name].commit();
        });
        self.pending.remove(item);

        item.isNew() ? self.persistCreate(item) : self.persistUpdate(item);
    };

    self.find = function (id) {
        return ko.utils.arrayFirst(self.content(), function (item) {
            return id == item.id();
        });
    };

    self.persistCreate = function (item) {
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
                    self.map(item, data);
                }
            }
        });
    };

    self.persistUpdate = function (item) {

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
                    self.map(item, data);
                }
            }
        });
    };


    self.templateName = function (item) {
        return self.isPending(item) ? 'editItemTmpl' : 'viewItemTmpl';
    };

    self.create = function (data) {
        var item = new Price.Item();
        self.map(item, data);
        self.content.push(item);
    };

    self.map = function (item, data) {
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

    self.delete = function (item) {
        self.content.remove(item);
    };

    self.showCreateForm = function () {
        var item = new Price.Item();
        item.group(parent.activeGroup());
        self.content.push(item);
        self.pending.push(item);
    };

    self.showEditForm = function (item) {
        self.pending.push(item);
    };

    self.showDeleteForm = function (item) {
        if (confirm('Are you sure?')) {
            self.pending.remove(item);
            self.delete(item);
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

    self.cancel = function (item, remove) {
        remove = typeof remove !== 'undefined' ? remove : true;
        if (remove) {
            self.pending.remove(item);
        }

        if (item.isNew()) {
            self.delete(item);
        }
    };

    self.cancelPending = function () {
        ko.utils.arrayForEach(self.pending(), function (item) {
            self.cancel(item, false);
        });
        self.pending.removeAll();
    };

    self.isPending = function (item) {
        return ko.utils.arrayFirst(self.pending(), function (i) {
            return i.id() == item.id();
        });
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
                    var item = self.find(changelog.itemId);
                    //if its update or delete, need to check if item still exists
                    //it may have been already deleted
                    if (changelog.type != 1 && !item) {
                        continue;
                    }
                    if (changelog.type == 1) {
                        self.create(changelog.item);
                    } else if (changelog.type == 2) {
                        self.map(item, changelog.item);
                    } else if (changelog.type == 3) {
                        self.delete(item);
                    }
                }
            }
        });
    };

    var pollEnabled = $.cookie('items-poll-enabled');
    //get cookie value, fallback to config value
    pollEnabled = pollEnabled !== null ? (pollEnabled === 'true') : config.pollEnabled;
    self.pollEnabled = ko.observable(pollEnabled);
    self.pollEnabled.subscribe(function (value) {
        //cookies are strings only, so storing an integer instead of boolean
        $.cookie('items-poll-enabled', value, { expires: 30 * 12 });
    });

};

