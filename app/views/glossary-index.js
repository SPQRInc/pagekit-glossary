module.exports = {

    el: '#glossary-index',

    data: {
        config: $data.config,
        items: $data.items,
        alphabet: $data.alphabet,
        selectedLetter: undefined
    },
    created() {
    },
    computed: {
        filteredItems() {
            let items = this.items;
            let selectedLetter = this.selectedLetter;
            let result = {};
            if (selectedLetter) {
                Object.keys(items).forEach(key => {
                    const item = items[key];
                    if (String(selectedLetter).charAt(0).toUpperCase() == item.title.charAt(0).toUpperCase()) {
                        result[key] = item
                    }
                });
            } else {
                result = items;
            }

            if (Object.keys(result).length < 1) result = false;

            return result;
        }

    },
    methods: {},
    filters: {
        truncate: function (string, value) {
            return string.substring(0, value) + '...';
        },
    }
};

Vue.ready(module.exports);