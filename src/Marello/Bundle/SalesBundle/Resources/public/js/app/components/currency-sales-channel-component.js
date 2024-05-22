define(function(require) {
    'use strict';

    const MultipleEntityComponent = require('oroform/js/multiple-entity/component');

    const CurrencySalesChannelComponent = MultipleEntityComponent.extend({
        optionNames: MultipleEntityComponent.prototype.optionNames.concat([
            'currency'
        ])
        // Not sure what to do here
    });

    return CurrencySalesChannelComponent;
});