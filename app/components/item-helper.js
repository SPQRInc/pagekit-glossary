module.exports = {

    methods: {

     itemInfoFromPickerSelection: function (pickerSelection) {
            var itemInfo = {};

            itemInfo.id = parseInt(pickerSelection.id);

            if(!!pickerSelection.data.text === true) {
                itemInfo.text = pickerSelection.data.text;
            }

            return itemInfo;
        },
        flatToNestedItemInfo: function (flatItemInfo) {
            var nestedWidgetInfo = {};

            flatItemInfo = this.validateFlatItemInfo(flatItemInfo);

            if (flatItemInfo.id) {
                nestedWidgetInfo.id = flatItemInfo.id;
                delete flatItemInfo.id;
            }

            if (Object.keys(flatItemInfo).length > 0) {
                nestedWidgetInfo.data = flatItemInfo;
            }

            return nestedWidgetInfo;
        },
        validateFlatItemInfo: function (flatItemInfo) {
            var validFlatItemInfo = {};

            if(parseInt(flatItemInfo.id) > 0) {
                validFlatItemInfo.id = parseInt(flatItemInfo.id);
            }

            if(!!flatItemInfo.text === true) {
                validFlatItemInfo.text = flatItemInfo.text;
            }

            return validFlatItemInfo;
        },

    }

};