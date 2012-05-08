ko.generateId = function () {
    return -(new Date()).getTime();
};

//wrapper to an observable that requires accept/cancel
ko.protectedObservable = function (initial) {
        //private variables
        var _temp = ko.observable(initial);
        var _actual = ko.observable(initial);

        var result = ko.dependentObservable({
            read: function () {
                return _actual();
            },
            write: function (newValue) {
                _temp(newValue);
            }
        });

        //commit the temporary value to our observable, if it is different
        result.commit = function () {
            var temp = _temp();
            if (temp !== _actual()) {
                _actual(temp);
            }
        };

        //notify subscribers to update their value with the original
        result.reset = function () {
            _actual.valueHasMutated();
            _temp(_actual());
        };

        //public property that stores value that is being edited
        result.temp = _temp;

        return result;
    };