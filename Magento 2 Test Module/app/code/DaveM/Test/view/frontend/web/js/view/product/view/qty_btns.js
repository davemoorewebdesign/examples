define([
    'ko',
    'uiComponent'
], function (ko, Component) {
    'use strict';
    return Component.extend({
        initialize: function () {
            this._super();
            this.qty = ko.observable(this.defaultQty);
        },
        increaseQty: function() {
		    var updatedQty = this.qty() + 1;
		    this.qty(updatedQty);
        },
		decreaseQty: function() {
		    var updatedQty = this.qty() - 1;
		    if (updatedQty < 1) {
			    updatedQty = 1;
		    }
		    this.qty(updatedQty);
        }
    });
});