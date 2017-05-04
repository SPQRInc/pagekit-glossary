window.items = {

    el: '#items',

    data: function () {
        return _.merge({
            items: false,
            config: {
                filter: this.$session.get('items.filter', {order: 'date desc', limit: 25})
            },
            pages: 0,
            count: '',
            selected: []
        }, window.$data);
    },
    ready: function () {
        this.resource = this.$resource('api/glossary/item{/id}');
        this.$watch('config.page', this.load, {immediate: true});
    },
    watch: {
        'config.filter': {
            handler: function (filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }

                this.$session.set('items.filter', filter);
            },
            deep: true
        }
    },
    computed: {
        statusOptions: function () {
            var options = _.map(this.$data.statuses, function (status, id) {
                return {text: status, value: id};
            });

            return [{label: this.$trans('Filter by'), options: options}];
        }
    },
    methods: {
        active: function (item) {
            return this.selected.indexOf(item.id) != -1;
        },
        save: function (item) {
            this.resource.save({id: item.id}, {item: item}).then(function () {
                this.load();
                this.$notify('Item saved.');
            });
        },
        status: function (status) {

            var items = this.getSelected();

            items.forEach(function (item) {
                item.status = status;
            });

            this.resource.save({id: 'bulk'}, {items: items}).then(function () {
                this.load();
                this.$notify('Items saved.');
            });
        },
        toggleStatus: function (item) {
            item.status = item.status === 1 ? 2 : 1;
            this.save(item);
        },
        remove: function () {

            this.resource.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
                this.load();
                this.$notify('Items deleted.');
            });
        },
        copy: function () {

            if (!this.selected.length) {
                return;
            }

            this.resource.save({id: 'copy'}, {ids: this.selected}).then(function () {
                this.load();
                this.$notify('Items copied.');
            });
        },
        load: function () {
            this.resource.query({filter: this.config.filter, page: this.config.page}).then(function (res) {

                var data = res.data;

                this.$set('items', data.items);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('selected', []);
            });
        },
        getSelected: function () {
            return this.items.filter(function (item) {
                return this.selected.indexOf(item.id) !== -1;
            }, this);
        },
        removeItems: function () {
            this.resource.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
                this.load();
                this.$notify('Items(s) deleted.');
            });
        },
        getStatusText: function (item) {
            return this.statuses[item.status];
        }
    },
    components: {}
};
Vue.ready(window.items);
