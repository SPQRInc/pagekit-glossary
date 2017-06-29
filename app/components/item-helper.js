module.exports = {

    methods: {

     itemInfoFromPickerSelection: function (pickerSelection) {
            var itemInfo = {};

            itemInfo.id = parseInt(pickerSelection.id);

            if(!!pickerSelection.data.title === true) {
                itemInfo.title = pickerSelection.data.title;
            }

            return itemInfo;
        },
        flatToNestedItemInfo: function (flatItemInfo) {
            var nestedItemInfo = {};

            flatItemInfo = this.validateFlatItemInfo(flatItemInfo);

            if (flatItemInfo.id) {
                nestedItemInfo.id = flatItemInfo.id;
                delete flatItemInfo.id;
            }

            if (Object.keys(flatItemInfo).length > 0) {
                nestedItemInfo.data = flatItemInfo;
            }

            return nestedItemInfo;
        },
        validateFlatItemInfo: function (flatItemInfo) {
            var validFlatItemInfo = {};

            if(parseInt(flatItemInfo.id) > 0) {
                validFlatItemInfo.id = parseInt(flatItemInfo.id);
            }

            if(!!flatItemInfo.title === true) {
                validFlatItemInfo.title = flatItemInfo.title;
            }

            return validFlatItemInfo;
        },

    }

};